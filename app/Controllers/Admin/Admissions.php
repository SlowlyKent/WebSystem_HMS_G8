<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\AdmissionModel;
use App\Models\RoomRateModel;
use App\Models\PatientModel;
use App\Libraries\AutoBillingHelper;

class Admissions extends BaseController
{
    /**
     * Show all admissions
     */
    public function index()
    {
        helper(['form']);
        $db = \Config\Database::connect();

        if (!session()->get('isLoggedIn')) {
            return redirect()->to(base_url('login'))->with('error', 'Please login to access Admissions.');
        }

        $admissionModel = new AdmissionModel();
        $admissions = [];

        if ($db->tableExists('admissions')) {
            $admissions = $db->table('admissions a')
                ->select('a.*, p.first_name as patient_first, p.last_name as patient_last, p.patient_code, 
                         u.first_name as doctor_first, u.last_name as doctor_last')
                ->join('patients p', 'p.id = a.patient_id', 'left')
                ->join('doctors d', 'd.id = a.doctor_id', 'left')
                ->join('users u', 'u.id = d.user_id', 'left')
                ->orderBy('a.id', 'DESC')
                ->get()
                ->getResultArray();
        }

        // Get patients for dropdown
        $patientModel = new PatientModel();
        $patients = $patientModel->select('id, first_name, last_name, patient_code')
                                 ->orderBy('last_name', 'ASC')
                                 ->findAll();

        // Get doctors for dropdown
        $doctors = [];
        if ($db->tableExists('doctors')) {
            $doctors = $db->table('doctors d')
                ->select('d.id, u.first_name, u.last_name')
                ->join('users u', 'u.id = d.user_id')
                ->where('u.status', 'active')
                ->get()
                ->getResultArray();
        }

        return view('roles/admin/admissions/index', [
            'title' => 'Patient Admissions',
            'admissions' => $admissions,
            'patients' => $patients,
            'doctors' => $doctors,
        ]);
    }

    /**
     * Admit a patient
     */
    public function admit()
    {
        helper(['form']);
        $session = session();

        if (!session()->get('isLoggedIn')) {
            return redirect()->to(base_url('login'))->with('error', 'Please login.');
        }

        $rules = [
            'patient_id' => 'required|is_natural_no_zero',
            'doctor_id' => 'required|is_natural_no_zero',
            'admission_date' => 'required|valid_date',
            'room_type' => 'required|in_list[ICU,NICU,WARD]',
            'room_number' => 'required|min_length[1]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $patientId = (int)$this->request->getPost('patient_id');
        $doctorId = (int)$this->request->getPost('doctor_id');
        $admissionDate = $this->request->getPost('admission_date');
        $roomType = $this->request->getPost('room_type');
        $roomNumber = $this->request->getPost('room_number');
        $reason = $this->request->getPost('reason') ?: null;

        try {
            $admissionModel = new AdmissionModel();
            
            $admissionData = [
                'patient_id' => $patientId,
                'doctor_id' => $doctorId,
                'admission_date' => $admissionDate,
                'room_type' => $roomType,
                'room_number' => $roomNumber,
                'reason' => $reason,
                'status' => 'admitted',
            ];

            $admissionModel->insert($admissionData);
            $admissionId = $admissionModel->getInsertID();

            if (!$admissionId) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Failed to create admission record.');
            }

            // Create first day room bill
            $this->createRoomBill($admissionId, $admissionDate, $roomType);

            return redirect()->to(base_url('admin/admissions'))
                ->with('success', 'Patient admitted successfully.');

        } catch (\Throwable $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to admit patient: ' . $e->getMessage());
        }
    }

    /**
     * Discharge a patient
     */
    public function discharge($id)
    {
        helper(['form']);
        $session = session();

        if (!session()->get('isLoggedIn')) {
            return redirect()->to(base_url('login'))->with('error', 'Please login.');
        }

        $admissionModel = new AdmissionModel();
        $admission = $admissionModel->find($id);

        if (!$admission) {
            return redirect()->back()->with('error', 'Admission not found.');
        }

        if ($admission['status'] === 'discharged') {
            return redirect()->back()->with('error', 'Patient is already discharged.');
        }

        try {
            $dischargeDate = date('Y-m-d');
            
            // Update admission
            $admissionModel->update($id, [
                'discharge_date' => $dischargeDate,
                'status' => 'discharged',
            ]);

            // Create final room bill
            $this->createRoomBill($id, $dischargeDate, $admission['room_type']);

            return redirect()->to(base_url('admin/admissions'))
                ->with('success', 'Patient discharged successfully. Final bill created.');

        } catch (\Throwable $e) {
            return redirect()->back()
                ->with('error', 'Failed to discharge patient: ' . $e->getMessage());
        }
    }

    /**
     * Create room bill for admission
     * This calculates days stayed and creates/updates bill
     */
    private function createRoomBill($admissionId, $billDate, $roomType)
    {
        $admissionModel = new AdmissionModel();
        $admission = $admissionModel->find($admissionId);

        if (!$admission) {
            return false;
        }

        // Calculate days stayed
        $admissionDate = new \DateTime($admission['admission_date']);
        $endDate = $admission['discharge_date'] ? new \DateTime($admission['discharge_date']) : new \DateTime($billDate);
        $daysStayed = $admissionDate->diff($endDate)->days + 1; // +1 because admission day counts

        // Get current room rate
        $roomRateModel = new RoomRateModel();
        $rate = $roomRateModel->getCurrentRate($roomType);

        if (!$rate) {
            // Default rates if no rate found
            $defaultRates = [
                'ICU' => 5000.00,
                'NICU' => 6000.00,
                'WARD' => 2000.00,
            ];
            $dailyRate = $defaultRates[$roomType] ?? 2000.00;
        } else {
            $dailyRate = (float)$rate['daily_rate'];
        }

        $totalCharge = $daysStayed * $dailyRate;

        // Check if bill already exists for this admission
        $db = \Config\Database::connect();
        $existingBill = $db->table('bills')
                          ->where('admission_id', $admissionId)
                          ->where('bill_date', $billDate)
                          ->get()
                          ->getFirstRow('array');

        if ($existingBill) {
            // Update existing bill
            $billingModel = new \App\Models\BillingModel();
            $billingModel->update($existingBill['id'], [
                'amount' => $totalCharge,
                'description' => "Room stay - {$roomType} Room {$admission['room_number']} - {$daysStayed} days",
            ]);
        } else {
            // Create new bill
            $billResult = AutoBillingHelper::createBill([
                'patient_id' => $admission['patient_id'],
                'amount' => $totalCharge,
                'description' => "Room stay - {$roomType} Room {$admission['room_number']} - {$daysStayed} days",
                'bill_date' => $billDate,
                'admission_id' => $admissionId,
            ]);

            return $billResult['success'];
        }

        return true;
    }
}

