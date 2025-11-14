<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\BillingModel;
use App\Models\PaymentModel;
use App\Models\PatientModel;

class Billing extends BaseController
{
    private function normalizeAmount($value): string
    {
        if ($value === null) return '';
        if (is_array($value)) $value = implode('', $value);
        $v = trim((string)$value);
        // Replace comma decimal separator with dot and strip spaces
        $v = str_replace([' ', ','], ['', '.'], $v);
        return $v;
    }
    private function money($value): float
    {
        if ($value === null) return 0.0;
        if (is_string($value)) {
            $value = (float)$value;
        }
        return round((float)$value, 2);
    }
    public function index()
    {
        helper(['form']);
        $model = new BillingModel();
        $bills = [];
        $unpaidSum = ['sum' => 0];
        $paidSum = ['sum' => 0];
        $overdueSum = ['sum' => 0];

        try {
            $db = \Config\Database::connect();
            if ($db->tableExists('bills')) {
                $bills = $model->orderBy('id', 'DESC')->findAll();

                // Compute paid amounts per bill and attach balances
                if ($db->tableExists('payments')) {
                    $payments = (new PaymentModel())
                        ->select('bill_id, SUM(amount) as paid_total')
                        ->groupBy('bill_id')
                        ->findAll();
                    $paidMap = [];
                    foreach ($payments as $p) {
                        $paidMap[(int)$p['bill_id']] = $this->money($p['paid_total'] ?? 0);
                    }
                    foreach ($bills as &$bill) {
                        $id = (int)($bill['id'] ?? 0);
                        $paidAmt = $paidMap[$id] ?? 0.0;
                        $amount = $this->money($bill['amount'] ?? 0);
                        $balance = max(0.0, $this->money($amount - $paidAmt));
                        $bill['paid_total'] = $paidAmt;
                        $bill['balance'] = $balance;
                    }
                    unset($bill);
                } else {
                    foreach ($bills as &$bill) {
                        $bill['paid_total'] = 0.0;
                        $bill['balance'] = $this->money($bill['amount'] ?? 0);
                    }
                    unset($bill);
                }
                // Compute totals from balances and payments for real-time accuracy
                $today = date('Y-m-d');
                $totalUnpaidCalc = 0.0;
                $totalPaidCalc = 0.0;
                $totalOverdueCalc = 0.0;
                foreach ($bills as $b) {
                    $paid = $this->money($b['paid_total'] ?? 0);
                    $balance = $this->money($b['balance'] ?? ($b['amount'] ?? 0));
                    $totalPaidCalc += $paid;
                    $totalUnpaidCalc += max(0.0, $balance);
                    $due = $b['due_date'] ?? '';
                    if (!empty($due) && $balance > 0 && $due < $today) {
                        $totalOverdueCalc += $balance;
                    }
                }
                $unpaidSum = ['sum' => $totalUnpaidCalc];
                $paidSum = ['sum' => $totalPaidCalc];
                $overdueSum = ['sum' => $totalOverdueCalc];
                
                // Recent payments (latest 10)
                $recentPayments = [];
                if ($db->tableExists('payments')) {
                    $paymentModel = new PaymentModel();
                    $recentPayments = $paymentModel
                        ->orderBy('id', 'DESC')
                        ->limit(10)
                        ->find();
                    // Attach bill info
                    if (!empty($recentPayments)) {
                        // Build bill map for quick lookup
                        $billIds = array_unique(array_map(fn($p) => (int)($p['bill_id'] ?? 0), $recentPayments));
                        if (!empty($billIds)) {
                            $billRows = $model->whereIn('id', $billIds)->findAll();
                            $billMap = [];
                            foreach ($billRows as $br) {
                                $billMap[(int)$br['id']] = $br;
                            }
                            foreach ($recentPayments as &$rp) {
                                $bid = (int)($rp['bill_id'] ?? 0);
                                $b = $billMap[$bid] ?? null;
                                $rp['bill_invoice_no'] = $b['invoice_no'] ?? ('INV-' . str_pad((string)($bid), 6, '0', STR_PAD_LEFT));
                                $rp['bill_patient_name'] = $b['patient_name'] ?? '';
                            }
                            unset($rp);
                        }
                    }
                }
            } else {
                session()->setFlashdata('error', 'Billing database not ready. Please run migrations.');
            }
        } catch (\Throwable $e) {
            // Prevent crash and show a friendly message
            session()->setFlashdata('error', 'Unable to load billing data. ' . ($e->getCode() ? '(Code ' . $e->getCode() . ')' : ''));
        }

        // Build open bills list (with positive balance) for quick payment selector
        $openBills = [];
        foreach ($bills as $b) {
            $balance = (float)($b['balance'] ?? (float)($b['amount'] ?? 0));
            if ($balance > 0.0) {
                $openBills[] = $b;
            }
        }

        // Build patients list for create-bill select
        $patientsList = [];
        try {
            $pModel = new PatientModel();
            $patientsList = $pModel->select('id, first_name, last_name, patient_code, insurance_provider')
                                   ->orderBy('last_name', 'ASC')
                                   ->orderBy('first_name', 'ASC')
                                   ->findAll();
        } catch (\Throwable $e) {
            $patientsList = [];
        }

        $data = [
            'title' => 'Billing Dashboard',
            'activeMenu' => 'billing',
            'bills' => $bills,
            'totalUnpaid' => (float) ($unpaidSum['sum'] ?? 0),
            'totalPaid' => (float) ($paidSum['sum'] ?? 0),
            'totalOverdue' => (float) ($overdueSum['sum'] ?? 0),
            'recentPayments' => $recentPayments ?? [],
            'openBills' => $openBills,
            'patientsList' => $patientsList,
        ];

        return view('roles/admin/billing/index', $data);
    }

    public function store()
    {
        helper(['form']);
        $session = session();
        $db = \Config\Database::connect();

        if (! $db->tableExists('bills')) {
            return redirect()->to(base_url('admin/billing#new-bill-form'))
                ->withInput()
                ->with('errors', ['database' => 'Billing database not ready. Please run migrations.']);
        }
        
        // Normalize decimal inputs
        $post = $this->request->getPost();
        if (isset($post['amount'])) {
            $post['amount'] = $this->normalizeAmount($post['amount']);
            $this->request->setGlobal('post', $post);
        }

        $rules = [
            'patient_id'   => 'required|is_natural_no_zero',
            'bill_date'    => 'required|valid_date',
            'due_date'     => 'required|valid_date',
            'description'  => 'required|min_length[3]',
            'amount'       => 'required|decimal',
            // status is forced to 'unpaid' by the system
            'invoice_no'   => 'permit_empty|max_length[50]',
        ];
        
        if (! $this->validate($rules)) {
            return redirect()->to(base_url('admin/billing#new-bill-form'))
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }
        
        // Fetch patient and compute insurance
        $patientId = (int)$this->request->getPost('patient_id');
        $pModel = new PatientModel();
        $patient = $pModel->find($patientId);
        if (! $patient) {
            return redirect()->to(base_url('admin/billing#new-bill-form'))
                ->withInput()
                ->with('errors', ['patient_id' => 'Selected patient not found.']);
        }

        $amount = $this->money($this->request->getPost('amount'));
        $billDate = $this->request->getPost('bill_date');

        // Determine insurance applicability (provider-based fixed coverage map 10–30%)
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
        $isValid = ($provider !== '') && ($validUntil === null || $validUntil === '' || $validUntil >= $billDate);

        $insuredAmount = 0.0;
        if ($isValid && $coveragePct > 0 && $amount > 0) {
            $insuredAmount = $this->money($amount * ($coveragePct / 100));
            $insuredAmount = min($insuredAmount, $amount);
        }
        $patientResp = $this->money($amount - $insuredAmount);
        $insuranceStatus = $insuredAmount > 0 ? 'applied' : 'none';

        // Compose patient display name (Last, First)
        $patientName = trim((string)($patient['last_name'] ?? ''));
        if (!empty($patient['first_name'])) {
            $patientName .= ($patientName ? ', ' : '') . $patient['first_name'];
        }

        $payload = [
            'patient_id'             => $patientId,
            'patient_name'           => $patientName,
            'bill_date'              => $billDate,
            'due_date'               => $this->request->getPost('due_date'),
            'description'            => $this->request->getPost('description'),
            'amount'                 => $amount,
            'insured_amount'         => $insuredAmount,
            'patient_responsibility' => $patientResp,
            'insurance_status'       => $insuranceStatus,
            'status'                 => 'unpaid',
            'invoice_no'             => null,
        ];
        
        try {
            $model = new BillingModel();
            $model->insert($payload);
            $newId = $model->getInsertID();
            if ($newId) {
                $generated = 'INV-' . date('Ym') . '-' . str_pad((string)$newId, 6, '0', STR_PAD_LEFT);
                // Attempt to update invoice_no uniquely
                $model->update($newId, ['invoice_no' => $generated]);

                // If there is an insured portion, auto-record it as an insurance payment
                if ($insuredAmount > 0) {
                    $paymentModel = new PaymentModel();
                    $paymentModel->insert([
                        'bill_id' => (int)$newId,
                        'amount'  => $insuredAmount,
                        'method'  => 'insurance',
                        'paid_at' => date('Y-m-d H:i:s'),
                        'notes'   => 'Auto-applied insurance coverage',
                    ]);
                    // If insurance covers full amount, mark as paid
                    if ($insuredAmount >= $amount) {
                        $model->update($newId, ['status' => 'paid']);
                    }
                }

                $session->setFlashdata('success', 'Bill saved successfully. Invoice: ' . $generated);
            } else {
                $session->setFlashdata('success', 'Bill saved successfully.');
            }
        } catch (\Throwable $e) {
            return redirect()->to(base_url('admin/billing#new-bill-form'))
                ->withInput()
                ->with('errors', ['database' => 'Failed to save bill. Please try again.']);
        }

        return redirect()->to(base_url('admin/billing'));
    }

    public function create()
    {
        // Will be implemented to create new bills
    }

    public function view($id)
    {
        // Will be implemented to view bill details
    }

    public function pay($id)
    {
        helper(['form']);
        $session = session();
        $db = \Config\Database::connect();

        if (! $db->tableExists('bills') || ! $db->tableExists('payments')) {
            return redirect()->to(base_url('admin/billing'))
                ->with('errors', ['database' => 'Billing or payments database not ready. Please run migrations.']);
        }

        // Normalize decimal input
        $post = $this->request->getPost();
        if (isset($post['amount'])) {
            $post['amount'] = $this->normalizeAmount($post['amount']);
            $this->request->setGlobal('post', $post);
        }

        $rules = [
            'amount' => 'required|decimal',
            'method' => 'permit_empty|max_length[50]',
            'notes'  => 'permit_empty|max_length[500]'
        ];
        if (! $this->validate($rules)) {
            return redirect()->to(base_url('admin/billing'))
                ->with('errors', $this->validator->getErrors());
        }

        $billModel = new BillingModel();
        $paymentModel = new PaymentModel();
        $bill = $billModel->find($id);
        if (! $bill) {
            $session->setFlashdata('error', 'Bill not found.');
            return redirect()->to(base_url('admin/billing'));
        }

        // Compute current paid and remaining
        $paidRow = $paymentModel->select('SUM(amount) as paid_total')->where('bill_id', $id)->first();
        $paidTotal = $this->money($paidRow['paid_total'] ?? 0);
        $amountDue = $this->money($bill['amount'] ?? 0);
        $balance = $this->money(max(0.0, $amountDue - $paidTotal));

        $payAmount = $this->money($this->request->getPost('amount'));
        if ($payAmount <= 0) {
            return redirect()->to(base_url('admin/billing'))
                ->with('errors', ['amount' => 'Payment amount must be greater than zero.']);
        }
        if ($payAmount > $this->money($balance)) {
            return redirect()->to(base_url('admin/billing'))
                ->with('errors', ['amount' => 'Payment exceeds remaining balance.']);
        }

        // Insert payment
        $paymentModel->insert([
            'bill_id' => (int)$id,
            'amount'  => $payAmount,
            'method'  => $this->request->getPost('method'),
            'paid_at' => date('Y-m-d H:i:s'),
            'notes'   => $this->request->getPost('notes'),
        ]);

        // Update status if fully paid
        $newPaidTotal = $paidTotal + $payAmount;
        if ($newPaidTotal >= $amountDue && $amountDue > 0) {
            $billModel->update($id, ['status' => 'paid']);
        }

        $session->setFlashdata('success', 'Payment recorded successfully.');
        return redirect()->to(base_url('admin/billing'));
    }

    public function updateStatus($id, $status)
    {
        // Will be implemented to update bill status
    }

    public function generateReceipt($id)
    {
        // Will be implemented to generate PDF receipt
    }

    public function quickPay()
    {
        helper(['form']);
        $session = session();
        $db = \Config\Database::connect();

        if (! $db->tableExists('bills') || ! $db->tableExists('payments')) {
            return redirect()->to(base_url('admin/billing#payments-quick'))
                ->withInput()
                ->with('errors', ['database' => 'Billing or payments database not ready. Please run migrations.']);
        }

        // Normalize decimal input
        $post = $this->request->getPost();
        if (isset($post['amount'])) {
            $post['amount'] = $this->normalizeAmount($post['amount']);
            $this->request->setGlobal('post', $post);
        }

        $rules = [
            'bill_id' => 'required|is_natural_no_zero',
            'amount'  => 'required|decimal',
            'method'  => 'permit_empty|max_length[50]',
            'notes'   => 'permit_empty|max_length[500]'
        ];
        if (! $this->validate($rules)) {
            return redirect()->to(base_url('admin/billing#payments-quick'))
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $billId = (int)$this->request->getPost('bill_id');
        $amount = $this->money($this->request->getPost('amount'));
        if ($amount <= 0) {
            return redirect()->to(base_url('admin/billing#payments-quick'))
                ->withInput()
                ->with('errors', ['amount' => 'Payment amount must be greater than zero.']);
        }

        $billModel = new BillingModel();
        $paymentModel = new PaymentModel();
        $bill = $billModel->find($billId);
        if (! $bill) {
            return redirect()->to(base_url('admin/billing#payments-quick'))
                ->with('errors', ['bill_id' => 'Selected bill not found.']);
        }

        // Current balance
        $paidRow = $paymentModel->select('SUM(amount) as paid_total')->where('bill_id', $billId)->first();
        $paidTotal = $this->money($paidRow['paid_total'] ?? 0);
        $amountDue = $this->money($bill['amount'] ?? 0);
        $balance = $this->money(max(0.0, $amountDue - $paidTotal));
        if ($amount > $this->money($balance)) {
            return redirect()->to(base_url('admin/billing#payments-quick'))
                ->withInput()
                ->with('errors', ['amount' => 'Payment exceeds remaining balance.']);
        }

        // Insert payment
        $paymentModel->insert([
            'bill_id' => $billId,
            'amount'  => $amount,
            'method'  => $this->request->getPost('method'),
            'paid_at' => date('Y-m-d H:i:s'),
            'notes'   => $this->request->getPost('notes'),
        ]);

        // Update status if fully paid
        $newPaidTotal = $paidTotal + $amount;
        if ($newPaidTotal >= $amountDue && $amountDue > 0) {
            $billModel->update($billId, ['status' => 'paid']);
        }

        $session->setFlashdata('success', 'Payment recorded successfully.');
        return redirect()->to(base_url('admin/billing#payments-quick'));
    }
}
