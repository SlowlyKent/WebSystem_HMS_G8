<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\PatientModel;
use App\Models\PatientAddressModel;

class Patients extends BaseController
{
    public function registration()
    {
        $model = new PatientModel();
        $addressModel = new PatientAddressModel();

        $q = trim((string) ($this->request->getGet('q') ?? ''));

        // Build query for search + pagination
        if ($q !== '') {
            $model->groupStart()
                ->like('patient_code', $q)
                ->orLike('first_name', $q)
                ->orLike('last_name', $q)
                ->orLike('email', $q)
            ->groupEnd();
        }

        $patients = $model->orderBy('id', 'DESC')->paginate(10);

        return view('roles/admin/patients/registration', [
            'title'    => 'Patient Registration & EHR',
            'patients' => $patients,
            'pager'    => $model->pager,
            'q'        => $q,
        ]);
    }

    public function store()
    {
        $coveragePctInput = $this->request->getPost('insurance_coverage_pct');
        $coveragePct = ($coveragePctInput !== null && $coveragePctInput !== '') ? (float) $coveragePctInput : null;

        $maxCoverageInput = $this->request->getPost('insurance_max_per_bill');
        $sanitizedMaxCoverage = null;
        if ($maxCoverageInput !== null && $maxCoverageInput !== '') {
            $numeric = preg_replace('/[^\d\.]/', '', str_replace(',', '', (string) $maxCoverageInput));
            $sanitizedMaxCoverage = $numeric !== '' ? (float) $numeric : null;
        }

        $validUntil = $this->request->getPost('insurance_valid_until') ?: null;
        $insuranceProvider = trim((string) $this->request->getPost('insurance_provider'));
        $policyNumber = trim((string) $this->request->getPost('insurance_policy_no'));

        $province = $this->request->getPost('province') ?: null;
        $cityMunicipality = $this->request->getPost('city_municipality') ?: null;
        $barangay = $this->request->getPost('barangay') ?: null;
        $street = $this->request->getPost('street') ?: null;

        $data = [
            'patient_code'   => $this->request->getPost('patient_code') ?: null,
            'first_name'     => trim((string) $this->request->getPost('first_name')),
            'middle_name'    => $this->request->getPost('middle_name') ?: null,
            'last_name'      => trim((string) $this->request->getPost('last_name')),
            'date_of_birth'  => $this->request->getPost('date_of_birth') ?: null,
            'gender'         => $this->request->getPost('gender') ?: null,
            'phone'          => $this->request->getPost('phone') ?: null,
            'email'          => $this->request->getPost('email') ?: null,
            'status'         => $this->request->getPost('status') ?: null,
            'insurance_provider' => $insuranceProvider !== '' ? $insuranceProvider : null,
            'insurance_policy_no' => $policyNumber !== '' ? $policyNumber : null,
            'insurance_coverage_pct' => $coveragePct,
            'insurance_max_per_bill' => $sanitizedMaxCoverage,
            'insurance_valid_until' => $validUntil,
            'blood_type'     => $this->request->getPost('blood_type') ?: null,
            'medical_notes'  => $this->request->getPost('medical_notes') ?: null,
        ];

        // Basic validation
        $rules = [
            'first_name' => 'required|min_length[2]',
            'last_name'  => 'required|min_length[2]',
            'email'      => 'permit_empty|valid_email',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->with('error', 'Please correct the errors.')
                ->with('errors', $this->validator->getErrors())
                ->withInput();
        }

        $model = new PatientModel();
        $addressModel = new PatientAddressModel();
        
        try {
            // Auto-generate patient_code when empty: P-0001 sequence
            if (empty($data['patient_code'])) {
                // Find the latest numeric sequence based on existing codes or id
                $last = $model->orderBy('id', 'DESC')->first();
                $nextNumber = $last ? ((int) filter_var($last['patient_code'] ?? '', FILTER_SANITIZE_NUMBER_INT) ?: ((int) $last['id'])) + 1 : 1;
                $data['patient_code'] = 'P-' . str_pad((string) $nextNumber, 4, '0', STR_PAD_LEFT);
            }

            $db = \Config\Database::connect();
            $db->transBegin();

            $model->insert($data);
            $patientId = $model->getInsertID();

            if ($patientId) {
                $addressModel->insert([
                    'patient_id'        => $patientId,
                    'province'          => $province,
                    'city_municipality' => $cityMunicipality,
                    'barangay'          => $barangay,
                    'street'            => $street,
                ]);
            }

            if ($db->transStatus() === false) {
                $db->transRollback();
                throw new \RuntimeException('Failed to save patient address.');
            }

            $db->transCommit();

            return redirect()->back()->with('success', 'Patient registered successfully.');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Failed to save patient: ' . $e->getMessage())
                ->with('errors', [$e->getMessage()])
                ->withInput();
        }
    }

    public function view($id)
    {
        $model = new PatientModel();
        $addressModel = new PatientAddressModel();

        $patient = $model->find($id);
        
        if (!$patient) {
            return redirect()->to(site_url('admin/patients/registration'))
                ->with('error', 'Patient not found.');
        }

        // Get patient address
        $address = $addressModel->where('patient_id', $id)->first();

        return view('roles/admin/patients/view', [
            'title'   => 'View Patient',
            'patient' => $patient,
            'address' => $address,
        ]);
    }

    public function edit($id)
    {
        $model = new PatientModel();
        $addressModel = new PatientAddressModel();

        $patient = $model->find($id);
        
        if (!$patient) {
            return redirect()->to(site_url('admin/patients/registration'))
                ->with('error', 'Patient not found.');
        }

        // Get patient address
        $address = $addressModel->where('patient_id', $id)->first();

        // Prepare data for form (same as registration view)
        $insuranceProviders = [
            'PhilHealth'  => 20,
            'Maxicare'    => 25,
            'MediCard'    => 15,
            'PhilCare'    => 18,
            'Cocolife'    => 22,
            'Intellicare' => 12,
            'Other'       => 10,
        ];

        $coverageOptions = [10, 12, 15, 18, 20, 22, 25, 30, 35, 40, 50, 60, 75, 80, 100];
        $bloodTypes = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-', 'Unknown'];

        return view('roles/admin/patients/edit', [
            'title'             => 'Edit Patient',
            'patient'           => $patient,
            'address'           => $address,
            'insuranceProviders' => $insuranceProviders,
            'coverageOptions'  => $coverageOptions,
            'bloodTypes'        => $bloodTypes,
        ]);
    }

    public function update($id)
    {
        $model = new PatientModel();
        $addressModel = new PatientAddressModel();

        $patient = $model->find($id);
        
        if (!$patient) {
            return redirect()->to(site_url('admin/patients/registration'))
                ->with('error', 'Patient not found.');
        }

        $coveragePctInput = $this->request->getPost('insurance_coverage_pct');
        $coveragePct = ($coveragePctInput !== null && $coveragePctInput !== '') ? (float) $coveragePctInput : null;

        $maxCoverageInput = $this->request->getPost('insurance_max_per_bill');
        $sanitizedMaxCoverage = null;
        if ($maxCoverageInput !== null && $maxCoverageInput !== '') {
            $numeric = preg_replace('/[^\d\.]/', '', str_replace(',', '', (string) $maxCoverageInput));
            $sanitizedMaxCoverage = $numeric !== '' ? (float) $numeric : null;
        }

        $validUntil = $this->request->getPost('insurance_valid_until') ?: null;
        $insuranceProvider = trim((string) $this->request->getPost('insurance_provider'));
        $policyNumber = trim((string) $this->request->getPost('insurance_policy_no'));

        $province = $this->request->getPost('province') ?: null;
        $cityMunicipality = $this->request->getPost('city_municipality') ?: null;
        $barangay = $this->request->getPost('barangay') ?: null;
        $street = $this->request->getPost('street') ?: null;

        $data = [
            'first_name'     => trim((string) $this->request->getPost('first_name')),
            'middle_name'    => $this->request->getPost('middle_name') ?: null,
            'last_name'      => trim((string) $this->request->getPost('last_name')),
            'date_of_birth'  => $this->request->getPost('date_of_birth') ?: null,
            'gender'         => $this->request->getPost('gender') ?: null,
            'phone'          => $this->request->getPost('phone') ?: null,
            'email'          => $this->request->getPost('email') ?: null,
            'status'         => $this->request->getPost('status') ?: null,
            'insurance_provider' => $insuranceProvider !== '' ? $insuranceProvider : null,
            'insurance_policy_no' => $policyNumber !== '' ? $policyNumber : null,
            'insurance_coverage_pct' => $coveragePct,
            'insurance_max_per_bill' => $sanitizedMaxCoverage,
            'insurance_valid_until' => $validUntil,
            'blood_type'     => $this->request->getPost('blood_type') ?: null,
            'medical_notes'  => $this->request->getPost('medical_notes') ?: null,
        ];

        // Basic validation
        $rules = [
            'first_name' => 'required|min_length[2]',
            'last_name'  => 'required|min_length[2]',
            'email'      => 'permit_empty|valid_email',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->with('error', 'Please correct the errors.')
                ->with('errors', $this->validator->getErrors())
                ->withInput();
        }

        try {
            $db = \Config\Database::connect();
            $db->transBegin();

            // Update patient
            $model->update($id, $data);

            // Update or create address
            $existingAddress = $addressModel->where('patient_id', $id)->first();
            
            $addressData = [
                'province'          => $province,
                'city_municipality' => $cityMunicipality,
                'barangay'          => $barangay,
                'street'            => $street,
            ];

            if ($existingAddress) {
                $addressModel->update($existingAddress['id'], $addressData);
            } else {
                $addressData['patient_id'] = $id;
                $addressModel->insert($addressData);
            }

            if ($db->transStatus() === false) {
                $db->transRollback();
                throw new \RuntimeException('Failed to update patient address.');
            }

            $db->transCommit();

            return redirect()->to(site_url('admin/patients/registration'))
                ->with('success', 'Patient updated successfully.');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Failed to update patient: ' . $e->getMessage())
                ->with('errors', [$e->getMessage()])
                ->withInput();
        }
    }
}
