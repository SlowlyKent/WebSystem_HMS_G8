<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\LabRequestModel;
use App\Models\LabRequestItemModel;
use App\Models\LabTestTypeModel;
use App\Models\LabSampleModel;
use App\Models\LabResultModel;
use App\Models\PatientModel;
use App\Models\BillingModel;

class Laboratory extends BaseController
{
    /**
     * Show all lab requests (main dashboard)
     */
    public function index()
    {
        helper(['form']);
        $db = \Config\Database::connect();
        
        // Check if user is logged in
        if (!session()->get('isLoggedIn')) {
            return redirect()->to(base_url('login'))->with('error', 'Please login to access Laboratory.');
        }

        $requestModel = new LabRequestModel();
        $requests = [];

        // Get all lab requests with patient and doctor info
        if ($db->tableExists('lab_requests')) {
            $requests = $db->table('lab_requests lr')
                ->select('lr.*, p.first_name as patient_first, p.last_name as patient_last, p.patient_code, 
                         u.first_name as doctor_first, u.last_name as doctor_last')
                ->join('patients p', 'p.id = lr.patient_id', 'left')
                ->join('doctors d', 'd.id = lr.doctor_id', 'left')
                ->join('users u', 'u.id = d.user_id', 'left')
                ->orderBy('lr.id', 'DESC')
                ->get()
                ->getResultArray();
        }

        // Get test types for dropdown
        $testTypes = [];
        if ($db->tableExists('lab_test_types')) {
            $testTypeModel = new LabTestTypeModel();
            $testTypes = $testTypeModel->where('status', 'active')->findAll();
        }

        // Get patients for dropdown
        $patients = [];
        $patientModel = new PatientModel();
        $patients = $patientModel->select('id, first_name, last_name, patient_code')
                                 ->orderBy('last_name', 'ASC')
                                 ->findAll();

        return view('roles/admin/laboratory/index', [
            'title' => 'Laboratory & Diagnostics',
            'requests' => $requests,
            'testTypes' => $testTypes,
            'patients' => $patients,
        ]);
    }

    /**
     * Create a new lab request (Doctor orders test)
     */
    public function createRequest()
    {
        helper(['form']);
        $session = session();

        if (!session()->get('isLoggedIn')) {
            return redirect()->to(base_url('login'))->with('error', 'Please login to create lab request.');
        }

        // Validation rules
        $role = $session->get('role');
        $rules = [
            'patient_id' => 'required|is_natural_no_zero',
            'request_date' => 'required|valid_date',
        ];

        // Doctor ID is only required if not a doctor (admin selects doctor)
        if ($role !== 'doctor') {
            $rules['doctor_id'] = 'required|is_natural_no_zero';
        }

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $patientId = (int)$this->request->getPost('patient_id');
        $requestDate = $this->request->getPost('request_date');
        $priority = $this->request->getPost('priority') ?: 'routine';
        $notes = $this->request->getPost('notes') ?: null;
        $testTypes = $this->request->getPost('test_types'); // Array of test type IDs

        // Validate test types separately (since it's an array)
        if (empty($testTypes) || !is_array($testTypes) || count($testTypes) === 0) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Please select at least one test type.');
        }

        // Get doctor ID - if doctor is logged in, use their ID, otherwise use posted value
        $doctorId = 0;
        
        if ($role === 'doctor') {
            // If doctor is creating request, get their doctor ID from session
            $userId = (int)$session->get('user_id');
            $db = \Config\Database::connect();
            $doctorRow = $db->table('doctors')->select('id')->where('user_id', $userId)->get()->getFirstRow('array');
            $doctorId = $doctorRow['id'] ?? 0;
        } else {
            // Admin or other roles can select doctor
            $doctorId = (int)$this->request->getPost('doctor_id');
        }

        if (!$doctorId) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Doctor not found or not selected.');
        }

        // Check if patient exists
        $patientModel = new PatientModel();
        $patient = $patientModel->find($patientId);
        if (!$patient) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Patient not found.');
        }

        // Get test types and calculate total price
        $testTypeModel = new LabTestTypeModel();
        $totalPrice = 0.0;
        $selectedTests = [];

        if (is_array($testTypes)) {
            foreach ($testTypes as $testTypeId) {
                $testType = $testTypeModel->find((int)$testTypeId);
                if ($testType) {
                    $totalPrice += (float)$testType['price'];
                    $selectedTests[] = $testType;
                }
            }
        }

        if (empty($selectedTests)) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Please select at least one test type.');
        }

        try {
            $db = \Config\Database::connect();
            
            // Check if bills table has required fields
            if (!$db->fieldExists('patient_id', 'bills')) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Bills table is missing patient_id field. Please run migration: AddInsuranceToPatientsAndBills');
            }
            
            // Check if lab_requests table exists
            if (!$db->tableExists('lab_requests')) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'lab_requests table does not exist. Please run migrations.');
            }
            
            $db->transStart();

            // Create lab request
            $requestModel = new LabRequestModel();
            $requestCode = $requestModel->generateRequestCode();
            
            if (empty($requestCode)) {
                $db->transRollback();
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Failed to generate request code.');
            }

            $requestData = [
                'request_code' => $requestCode,
                'patient_id' => $patientId,
                'doctor_id' => $doctorId,
                'request_date' => $requestDate,
                'status' => 'pending',
                'priority' => $priority,
                'notes' => $notes,
                'created_by' => (int)($session->get('user_id') ?? 0),
            ];

            $requestModel->insert($requestData);
            $requestId = $requestModel->getInsertID();

            // Check if request was created successfully
            if (!$requestId) {
                $db->transRollback();
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Failed to create lab request. Could not get request ID.');
            }

            // Create request items (link tests to request)
            $requestItemModel = new LabRequestItemModel();
            foreach ($testTypes as $testTypeId) {
                $testTypeIdInt = (int)$testTypeId;
                if ($testTypeIdInt <= 0) {
                    continue; // Skip invalid test type IDs
                }
                
                $itemResult = $requestItemModel->insert([
                    'request_id' => $requestId,
                    'test_type_id' => $testTypeIdInt,
                ]);
                
                if (!$itemResult) {
                    $db->transRollback();
                    $errors = $requestItemModel->errors();
                    $errorMsg = 'Failed to link test types to request.';
                    if (!empty($errors)) {
                        $errorMsg .= ' ' . implode(', ', $errors);
                    }
                    return redirect()->back()
                        ->withInput()
                        ->with('error', $errorMsg);
                }
            }

            // Auto-create bill for lab tests
            $patientName = trim($patient['last_name'] . ', ' . $patient['first_name']);
            $billingModel = new BillingModel();
            
            // Get patient insurance info for billing
            $provider = trim((string)($patient['insurance_provider'] ?? ''));
            $providerCoverage = [
                'PhilHealth'  => 20.0,
                'Maxicare'    => 25.0,
                'MediCard'    => 15.0,
                'PhilCare'    => 18.0,
                'Cocolife'    => 22.0,
                'Intellicare' => 12.0,
                'Other'       => 10.0,
            ];
            $coveragePct = isset($providerCoverage[$provider]) ? (float)$providerCoverage[$provider] : 0.0;
            $validUntil = $patient['insurance_valid_until'] ?? null;
            $isValid = ($provider !== '') && ($validUntil === null || $validUntil === '' || $validUntil >= $requestDate);

            $insuredAmount = 0.0;
            if ($isValid && $coveragePct > 0 && $totalPrice > 0) {
                $insuredAmount = round($totalPrice * ($coveragePct / 100), 2);
                $insuredAmount = min($insuredAmount, $totalPrice);
            }
            $patientResp = round($totalPrice - $insuredAmount, 2);
            $insuranceStatus = $insuredAmount > 0 ? 'applied' : 'none';

            // Create bill
            $billData = [
                'patient_id' => $patientId,
                'patient_name' => $patientName,
                'bill_date' => $requestDate,
                'due_date' => date('Y-m-d', strtotime($requestDate . ' +30 days')),
                'description' => 'Laboratory Tests: ' . implode(', ', array_column($selectedTests, 'name')),
                'amount' => $totalPrice,
                'insured_amount' => $insuredAmount,
                'patient_responsibility' => $patientResp,
                'insurance_status' => $insuranceStatus,
                'status' => 'unpaid',
            ];

            $billInsertResult = $billingModel->insert($billData);
            if (!$billInsertResult) {
                $db->transRollback();
                $errors = $billingModel->errors();
                $errorMsg = 'Failed to create bill.';
                if (!empty($errors)) {
                    $errorMsg .= ' ' . implode(', ', $errors);
                }
                return redirect()->back()
                    ->withInput()
                    ->with('error', $errorMsg);
            }
            
            $billId = $billingModel->getInsertID();

            // Generate invoice number
            if ($billId) {
                $invoiceNo = 'INV-' . date('Ym') . '-' . str_pad((string)$billId, 6, '0', STR_PAD_LEFT);
                $billingModel->update($billId, ['invoice_no' => $invoiceNo]);
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                $error = $db->error();
                $errorMsg = 'Database transaction failed. ';
                if (!empty($error)) {
                    if (is_array($error)) {
                        $errorMsg .= isset($error['message']) ? $error['message'] : json_encode($error);
                    } else {
                        $errorMsg .= $error;
                    }
                } else {
                    $errorMsg .= 'Unknown database error.';
                }
                return redirect()->back()
                    ->withInput()
                    ->with('error', $errorMsg);
            }

            return redirect()->to(base_url('laboratory'))
                ->with('success', 'Lab request created successfully. Request Code: ' . $requestCode);

        } catch (\Throwable $e) {
            // Show detailed error for debugging (you can remove this later)
            $errorMsg = 'Failed to create lab request: ' . $e->getMessage();
            if ($e->getCode()) {
                $errorMsg .= ' (Code: ' . $e->getCode() . ')';
            }
            $errorMsg .= ' File: ' . basename($e->getFile()) . ' Line: ' . $e->getLine();
            
            return redirect()->back()
                ->withInput()
                ->with('error', $errorMsg);
        }
    }

    /**
     * Collect sample (Lab staff collects sample from patient)
     */
    public function collectSample($requestId)
    {
        helper(['form']);
        $session = session();

        if (!session()->get('isLoggedIn')) {
            return redirect()->to(base_url('login'))->with('error', 'Please login.');
        }

        $rules = [
            'sample_type' => 'required|min_length[2]',
            'collection_date' => 'required',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        // Check if request exists
        $requestModel = new LabRequestModel();
        $request = $requestModel->find($requestId);
        if (!$request) {
            return redirect()->back()->with('error', 'Lab request not found.');
        }

        try {
            $db = \Config\Database::connect();
            $db->transStart();

            // Create sample record
            $sampleModel = new LabSampleModel();
            $sampleData = [
                'request_id' => $requestId,
                'sample_type' => $this->request->getPost('sample_type'),
                'collection_date' => $this->request->getPost('collection_date'),
                'collected_by' => (int)($session->get('user_id') ?? 0),
                'notes' => $this->request->getPost('notes') ?: null,
            ];

            $sampleModel->insert($sampleData);

            // Update request status
            $requestModel->update($requestId, ['status' => 'sample_collected']);

            $db->transComplete();

            if ($db->transStatus() === false) {
                return redirect()->back()->with('error', 'Failed to record sample collection.');
            }

            return redirect()->to(base_url('laboratory'))
                ->with('success', 'Sample collected successfully.');

        } catch (\Throwable $e) {
            return redirect()->back()
                ->with('error', 'Failed to record sample: ' . $e->getMessage());
        }
    }

    /**
     * Enter test results (Lab staff enters results)
     */
    public function enterResults($requestId)
    {
        helper(['form']);
        $session = session();

        if (!session()->get('isLoggedIn')) {
            return redirect()->to(base_url('login'))->with('error', 'Please login.');
        }

        // Check if request exists
        $requestModel = new LabRequestModel();
        $request = $requestModel->find($requestId);
        if (!$request) {
            return redirect()->back()->with('error', 'Lab request not found.');
        }

        // Get test types for this request
        $db = \Config\Database::connect();
        $requestItems = $db->table('lab_request_items')
            ->where('request_id', $requestId)
            ->get()
            ->getResultArray();

        if (empty($requestItems)) {
            return redirect()->back()->with('error', 'No tests found for this request.');
        }

        try {
            $db->transStart();

            $resultModel = new LabResultModel();
            $testTypeModel = new LabTestTypeModel();

            // Process each test result
            foreach ($requestItems as $item) {
                $testTypeId = $item['test_type_id'];
                $resultValue = $this->request->getPost('result_' . $testTypeId);
                $isNormal = (int)$this->request->getPost('is_normal_' . $testTypeId) ?: 0;
                $notes = $this->request->getPost('notes_' . $testTypeId) ?: null;

                if ($resultValue !== null && $resultValue !== '') {
                    // Check if result already exists
                    $existingResult = $resultModel->where('request_id', $requestId)
                                                   ->where('test_type_id', $testTypeId)
                                                   ->first();

                    $resultData = [
                        'request_id' => $requestId,
                        'test_type_id' => $testTypeId,
                        'result_value' => $resultValue,
                        'is_normal' => $isNormal,
                        'status' => 'pending',
                        'notes' => $notes,
                    ];

                    if ($existingResult) {
                        // Update existing result
                        $resultModel->update($existingResult['id'], $resultData);
                    } else {
                        // Create new result
                        $resultModel->insert($resultData);
                    }
                }
            }

            // Update request status
            $requestModel->update($requestId, ['status' => 'in_progress']);

            $db->transComplete();

            if ($db->transStatus() === false) {
                return redirect()->back()->with('error', 'Failed to save results.');
            }

            return redirect()->to(base_url('laboratory'))
                ->with('success', 'Test results entered successfully.');

        } catch (\Throwable $e) {
            return redirect()->back()
                ->with('error', 'Failed to save results: ' . $e->getMessage());
        }
    }

    /**
     * View test results
     */
    public function viewResults($requestId)
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to(base_url('login'))->with('error', 'Please login.');
        }

        $requestModel = new LabRequestModel();
        $request = $requestModel->find($requestId);

        if (!$request) {
            return redirect()->to(base_url('laboratory'))
                ->with('error', 'Lab request not found.');
        }

        $db = \Config\Database::connect();

        // Get patient info
        $patientModel = new PatientModel();
        $patient = $patientModel->find($request['patient_id']);

        // Get doctor info
        $doctorInfo = $db->table('doctors d')
            ->select('u.first_name, u.last_name, d.specialization')
            ->join('users u', 'u.id = d.user_id')
            ->where('d.id', $request['doctor_id'])
            ->get()
            ->getRowArray();

        // Get test types for this request
        $testTypes = $db->table('lab_request_items lri')
            ->select('ltt.*')
            ->join('lab_test_types ltt', 'ltt.id = lri.test_type_id')
            ->where('lri.request_id', $requestId)
            ->get()
            ->getResultArray();

        // Get results
        $results = $db->table('lab_results lr')
            ->select('lr.*, ltt.name as test_name, ltt.normal_range, ltt.unit')
            ->join('lab_test_types ltt', 'ltt.id = lr.test_type_id')
            ->where('lr.request_id', $requestId)
            ->get()
            ->getResultArray();

        // Get sample info
        $sampleModel = new LabSampleModel();
        $sample = $sampleModel->where('request_id', $requestId)->first();

        return view('roles/admin/laboratory/view_report', [
            'title' => 'Lab Test Results',
            'request' => $request,
            'patient' => $patient,
            'doctor' => $doctorInfo,
            'testTypes' => $testTypes,
            'results' => $results,
            'sample' => $sample,
        ]);
    }

    /**
     * Verify/Approve results
     */
    public function verifyResults($requestId)
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to(base_url('login'))->with('error', 'Please login.');
        }

        $requestModel = new LabRequestModel();
        $request = $requestModel->find($requestId);

        if (!$request) {
            return redirect()->back()->with('error', 'Lab request not found.');
        }

        try {
            $db = \Config\Database::connect();
            $db->transStart();

            // Update all results to verified
            $resultModel = new LabResultModel();
            $results = $resultModel->where('request_id', $requestId)->findAll();

            foreach ($results as $result) {
                $resultModel->update($result['id'], [
                    'status' => 'verified',
                    'verified_by' => (int)(session()->get('user_id') ?? 0),
                    'verified_at' => date('Y-m-d H:i:s'),
                ]);
            }

            // Update request status to completed
            $requestModel->update($requestId, ['status' => 'completed']);

            $db->transComplete();

            if ($db->transStatus() === false) {
                return redirect()->back()->with('error', 'Failed to verify results.');
            }

            return redirect()->to(base_url('laboratory'))
                ->with('success', 'Results verified and request completed.');

        } catch (\Throwable $e) {
            return redirect()->back()
                ->with('error', 'Failed to verify results: ' . $e->getMessage());
        }
    }
}

