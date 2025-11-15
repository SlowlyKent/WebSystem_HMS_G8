<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\PrescriptionModel;
use App\Models\PrescriptionItemModel;
use App\Models\MedicineModel;
use App\Models\PatientModel;
use App\Libraries\AutoBillingHelper;

class Pharmacy extends BaseController
{
    /**
     * Show all prescriptions
     */
    public function index()
    {
        helper(['form']);
        $db = \Config\Database::connect();

        if (!session()->get('isLoggedIn')) {
            return redirect()->to(base_url('login'))->with('error', 'Please login to access Pharmacy.');
        }

        $prescriptionModel = new PrescriptionModel();
        $prescriptions = [];

        if ($db->tableExists('prescriptions')) {
            $prescriptions = $db->table('prescriptions p')
                ->select('p.*, pt.first_name as patient_first, pt.last_name as patient_last, pt.patient_code, 
                         u.first_name as doctor_first, u.last_name as doctor_last')
                ->join('patients pt', 'pt.id = p.patient_id', 'left')
                ->join('doctors d', 'd.id = p.doctor_id', 'left')
                ->join('users u', 'u.id = d.user_id', 'left')
                ->orderBy('p.id', 'DESC')
                ->get()
                ->getResultArray();
        }

        // Get medicines for dropdown
        $medicineModel = new MedicineModel();
        $medicines = $medicineModel->where('is_active', 1)
                                   ->orderBy('name', 'ASC')
                                   ->findAll();

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

        return view('roles/admin/pharmacy/index', [
            'title' => 'Pharmacy Management',
            'prescriptions' => $prescriptions,
            'medicines' => $medicines,
            'patients' => $patients,
            'doctors' => $doctors,
        ]);
    }

    /**
     * Create prescription (Doctor prescribes medicine)
     */
    public function createPrescription()
    {
        helper(['form']);
        $session = session();

        if (!session()->get('isLoggedIn')) {
            return redirect()->to(base_url('login'))->with('error', 'Please login.');
        }

        $rules = [
            'patient_id' => 'required|is_natural_no_zero',
            'doctor_id' => 'required|is_natural_no_zero',
            'prescription_date' => 'required|valid_date',
            'medicines' => 'required',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $patientId = (int)$this->request->getPost('patient_id');
        $doctorId = (int)$this->request->getPost('doctor_id');
        $prescriptionDate = $this->request->getPost('prescription_date');
        $appointmentId = $this->request->getPost('appointment_id') ? (int)$this->request->getPost('appointment_id') : null;
        $notes = $this->request->getPost('notes') ?: null;
        $medicines = $this->request->getPost('medicines'); // Array: [medicine_id => quantity]

        // Validate medicines
        if (empty($medicines) || !is_array($medicines)) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Please select at least one medicine.');
        }

        try {
            $db = \Config\Database::connect();
            $db->transStart();

            // Create prescription
            $prescriptionModel = new PrescriptionModel();
            $prescriptionData = [
                'patient_id' => $patientId,
                'doctor_id' => $doctorId,
                'appointment_id' => $appointmentId,
                'prescription_date' => $prescriptionDate,
                'status' => 'pending',
                'notes' => $notes,
            ];

            $prescriptionModel->insert($prescriptionData);
            $prescriptionId = $prescriptionModel->getInsertID();

            if (!$prescriptionId) {
                $db->transRollback();
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Failed to create prescription.');
            }

            // Add prescription items and calculate total
            $prescriptionItemModel = new PrescriptionItemModel();
            $medicineModel = new MedicineModel();
            $totalPrice = 0.0;
            $medicineNames = [];

            foreach ($medicines as $medicineId => $quantity) {
                $medicineIdInt = (int)$medicineId;
                $quantityInt = (int)$quantity;

                if ($medicineIdInt <= 0 || $quantityInt <= 0) {
                    continue;
                }

                // Get medicine info
                $medicine = $medicineModel->find($medicineIdInt);
                if (!$medicine) {
                    continue;
                }

                $unitPrice = (float)$medicine['price'];
                $itemTotal = $quantityInt * $unitPrice;
                $totalPrice += $itemTotal;
                $medicineNames[] = $medicine['name'] . ' (' . $quantityInt . ' ' . ($medicine['unit'] ?? 'units') . ')';

                // Create prescription item
                $prescriptionItemModel->insert([
                    'prescription_id' => $prescriptionId,
                    'medicine_id' => $medicineIdInt,
                    'quantity' => $quantityInt,
                    'unit_price' => $unitPrice,
                    'total_price' => $itemTotal,
                ]);
            }

            if ($totalPrice <= 0) {
                $db->transRollback();
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Invalid medicine selection.');
            }

            // Auto-create bill for prescription
            $billResult = AutoBillingHelper::createBill([
                'patient_id' => $patientId,
                'amount' => $totalPrice,
                'description' => 'Prescription: ' . implode(', ', $medicineNames),
                'bill_date' => $prescriptionDate,
                'prescription_id' => $prescriptionId,
            ]);

            if (!$billResult['success']) {
                $db->transRollback();
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Failed to create bill: ' . ($billResult['error'] ?? 'Unknown error'));
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Failed to create prescription.');
            }

            return redirect()->to(base_url('admin/pharmacy'))
                ->with('success', 'Prescription created successfully.');

        } catch (\Throwable $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create prescription: ' . $e->getMessage());
        }
    }

    /**
     * Dispense medicine (Pharmacist dispenses)
     */
    public function dispense($id)
    {
        helper(['form']);
        $session = session();

        if (!session()->get('isLoggedIn')) {
            return redirect()->to(base_url('login'))->with('error', 'Please login.');
        }

        $prescriptionModel = new PrescriptionModel();
        $prescription = $prescriptionModel->find($id);

        if (!$prescription) {
            return redirect()->back()->with('error', 'Prescription not found.');
        }

        if ($prescription['status'] === 'dispensed') {
            return redirect()->back()->with('error', 'Prescription already dispensed.');
        }

        try {
            $db = \Config\Database::connect();
            $db->transStart();

            // Get prescription items
            $prescriptionItemModel = new PrescriptionItemModel();
            $items = $prescriptionItemModel->where('prescription_id', $id)->findAll();

            // Check stock and reduce
            $medicineModel = new MedicineModel();
            foreach ($items as $item) {
                $medicine = $medicineModel->find($item['medicine_id']);
                if (!$medicine) {
                    $db->transRollback();
                    return redirect()->back()->with('error', 'Medicine not found: ID ' . $item['medicine_id']);
                }

                $newStock = (int)$medicine['stock'] - (int)$item['quantity'];
                if ($newStock < 0) {
                    $db->transRollback();
                    return redirect()->back()->with('error', 'Insufficient stock for: ' . $medicine['name']);
                }

                $medicineModel->update($item['medicine_id'], ['stock' => $newStock]);
            }

            // Update prescription status
            $prescriptionModel->update($id, ['status' => 'dispensed']);

            $db->transComplete();

            if ($db->transStatus() === false) {
                return redirect()->back()->with('error', 'Failed to dispense prescription.');
            }

            return redirect()->to(base_url('admin/pharmacy'))
                ->with('success', 'Medicine dispensed successfully. Stock updated.');

        } catch (\Throwable $e) {
            return redirect()->back()
                ->with('error', 'Failed to dispense: ' . $e->getMessage());
        }
    }
}

