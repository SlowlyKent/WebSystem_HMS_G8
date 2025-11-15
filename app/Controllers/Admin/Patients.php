<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\PatientModel;

class Patients extends BaseController
{
    public function registration()
    {
        $model = new PatientModel();

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

        $data = [
            'patient_code'   => $this->request->getPost('patient_code') ?: null,
            'first_name'     => trim((string) $this->request->getPost('first_name')),
            'middle_name'    => $this->request->getPost('middle_name') ?: null,
            'last_name'      => trim((string) $this->request->getPost('last_name')),
            'date_of_birth'  => $this->request->getPost('date_of_birth') ?: null,
            'gender'         => $this->request->getPost('gender') ?: null,
            'phone'          => $this->request->getPost('phone') ?: null,
            'email'          => $this->request->getPost('email') ?: null,
            'address'        => $this->request->getPost('address') ?: null,
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
        try {
            // Auto-generate patient_code when empty: P-0001 sequence
            if (empty($data['patient_code'])) {
                // Find the latest numeric sequence based on existing codes or id
                $last = $model->orderBy('id', 'DESC')->first();
                $nextNumber = $last ? ((int) filter_var($last['patient_code'] ?? '', FILTER_SANITIZE_NUMBER_INT) ?: ((int) $last['id'])) + 1 : 1;
                $data['patient_code'] = 'P-' . str_pad((string) $nextNumber, 4, '0', STR_PAD_LEFT);
            }

            $model->insert($data);
            return redirect()->back()->with('success', 'Patient registered successfully.');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Failed to save patient: ' . $e->getMessage())
                ->with('errors', [$e->getMessage()])
                ->withInput();
        }
    }
}
