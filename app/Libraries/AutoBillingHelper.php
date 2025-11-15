<?php

namespace App\Libraries;

use App\Models\BillingModel;
use App\Models\PatientModel;
use App\Models\PaymentModel;

/**
 * Helper class for automatic bill creation
 * This makes billing logic reusable across different modules
 */
class AutoBillingHelper
{
    /**
     * Create a bill automatically with insurance calculation
     * 
     * @param array $data Bill data (patient_id, amount, description, etc.)
     * @return array ['success' => bool, 'bill_id' => int|null, 'error' => string|null]
     */
    public static function createBill($data)
    {
        try {
            // Required fields
            if (empty($data['patient_id']) || empty($data['amount'])) {
                return [
                    'success' => false,
                    'bill_id' => null,
                    'error' => 'Patient ID and amount are required'
                ];
            }

            $patientModel = new PatientModel();
            $patient = $patientModel->find($data['patient_id']);
            
            if (!$patient) {
                return [
                    'success' => false,
                    'bill_id' => null,
                    'error' => 'Patient not found'
                ];
            }

            // Get patient name
            $patientName = trim(($patient['last_name'] ?? '') . ', ' . ($patient['first_name'] ?? ''));
            
            // Calculate insurance
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
            $billDate = $data['bill_date'] ?? date('Y-m-d');
            $isValid = ($provider !== '') && ($validUntil === null || $validUntil === '' || $validUntil >= $billDate);

            $amount = (float)$data['amount'];
            $insuredAmount = 0.0;
            
            if ($isValid && $coveragePct > 0 && $amount > 0) {
                $insuredAmount = round($amount * ($coveragePct / 100), 2);
                $insuredAmount = min($insuredAmount, $amount);
            }
            
            $patientResp = round($amount - $insuredAmount, 2);
            $insuranceStatus = $insuredAmount > 0 ? 'applied' : 'none';

            // Create bill
            $billingModel = new BillingModel();
            $billData = [
                'patient_id' => $data['patient_id'],
                'patient_name' => $patientName,
                'bill_date' => $billDate,
                'due_date' => $data['due_date'] ?? date('Y-m-d', strtotime($billDate . ' +30 days')),
                'description' => $data['description'] ?? 'Medical service',
                'amount' => $amount,
                'insured_amount' => $insuredAmount,
                'patient_responsibility' => $patientResp,
                'insurance_status' => $insuranceStatus,
                'status' => 'unpaid',
                // Source tracking (optional)
                'appointment_id' => $data['appointment_id'] ?? null,
                'admission_id' => $data['admission_id'] ?? null,
                'lab_request_id' => $data['lab_request_id'] ?? null,
                'prescription_id' => $data['prescription_id'] ?? null,
            ];

            $billingModel->insert($billData);
            $billId = $billingModel->getInsertID();

            if (!$billId) {
                return [
                    'success' => false,
                    'bill_id' => null,
                    'error' => 'Failed to create bill'
                ];
            }

            // Generate invoice number
            $invoiceNo = 'INV-' . date('Ym') . '-' . str_pad((string)$billId, 6, '0', STR_PAD_LEFT);
            $billingModel->update($billId, ['invoice_no' => $invoiceNo]);

            // Auto-create payment if insurance covers
            if ($insuredAmount > 0) {
                $paymentModel = new PaymentModel();
                $paymentModel->insert([
                    'bill_id' => $billId,
                    'amount' => $insuredAmount,
                    'method' => 'insurance',
                    'paid_at' => date('Y-m-d H:i:s'),
                    'notes' => 'Auto-applied insurance coverage',
                ]);

                // If insurance covers full amount, mark as paid
                if ($insuredAmount >= $amount) {
                    $billingModel->update($billId, ['status' => 'paid']);
                }
            }

            return [
                'success' => true,
                'bill_id' => $billId,
                'error' => null
            ];

        } catch (\Throwable $e) {
            return [
                'success' => false,
                'bill_id' => null,
                'error' => $e->getMessage()
            ];
        }
    }
}

