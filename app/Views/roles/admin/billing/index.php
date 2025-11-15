<?= $this->extend('layouts/template') ?>

<?= $this->section('content') ?>
<style>
  .billing-header {
    background: linear-gradient(135deg, #1e3a5f 0%, #2c5282 100%);
    color: white;
    padding: 2rem;
    border-radius: 8px;
    margin-bottom: 2rem;
  }
  .stat-card {
    border-left: 4px solid;
    transition: transform 0.2s;
  }
  .stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
  }
  .stat-card.unpaid { border-left-color: #0d6efd; }
  .stat-card.paid { border-left-color: #198754; }
  .stat-card.overdue { border-left-color: #dc3545; }
  .bills-table {
    font-size: 0.9rem;
  }
  .bills-table thead {
    background-color: #f8f9fa;
    border-bottom: 2px solid #dee2e6;
  }
  .bills-table thead th {
    font-weight: 600;
    text-transform: uppercase;
    font-size: 0.75rem;
    letter-spacing: 0.5px;
    color: #495057;
    padding: 1rem 0.75rem;
  }
  .bills-table tbody td {
    padding: 1rem 0.75rem;
    vertical-align: middle;
  }
  .invoice-badge {
    font-family: 'Courier New', monospace;
    font-weight: 600;
    color: #1e3a5f;
  }
  .amount-cell {
    font-weight: 600;
    color: #212529;
  }
  .balance-cell {
    font-weight: 700;
  }
  .balance-cell.positive {
    color: #dc3545;
  }
  .balance-cell.zero {
    color: #198754;
  }
</style>

<!-- Formal Header -->
<div class="billing-header">
  <div class="d-flex justify-content-between align-items-center">
    <div>
      <h2 class="mb-2 fw-bold">Billing & Payment Management</h2>
      <p class="mb-0 opacity-90">Financial records and invoice processing system</p>
    </div>
    <div>
      <a href="#new-bill-form" class="btn btn-light">
        <i class="fas fa-file-invoice me-2"></i>Create New Invoice
      </a>
    </div>
  </div>
</div>

<!-- Alert Messages -->
<?php if (session()->getFlashdata('success')): ?>
  <div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="fas fa-check-circle me-2"></i><?= esc(session()->getFlashdata('success')) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
<?php endif; ?>
<?php if (session()->getFlashdata('error')): ?>
  <div class="alert alert-danger alert-dismissible fade show" role="alert">
    <i class="fas fa-exclamation-circle me-2"></i><?= esc(session()->getFlashdata('error')) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
<?php endif; ?>
<?php $errors = session()->getFlashdata('errors'); ?>
<?php if (!empty($errors) && is_array($errors)): ?>
  <div class="alert alert-danger alert-dismissible fade show" role="alert">
    <strong><i class="fas fa-exclamation-triangle me-2"></i>Validation Errors:</strong>
    <ul class="mb-0 mt-2">
      <?php foreach ($errors as $field => $msg): ?>
        <li><?= esc($msg) ?></li>
      <?php endforeach; ?>
    </ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
<?php endif; ?>

<!-- Financial Summary Cards -->
<div class="row g-4 mb-4">
  <div class="col-md-4">
    <div class="card stat-card unpaid border-0 shadow-sm h-100">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-start mb-3">
          <div>
            <p class="text-muted text-uppercase small fw-semibold mb-2">Outstanding Balance</p>
            <h2 class="mb-0 fw-bold text-primary">₱<?= number_format($totalUnpaid, 2) ?></h2>
          </div>
          <div class="bg-primary bg-opacity-10 p-3 rounded">
            <i class="fas fa-exclamation-circle fa-2x text-primary"></i>
          </div>
        </div>
        <p class="text-muted small mb-0">Total unpaid invoices</p>
      </div>
    </div>
  </div>
  
  <div class="col-md-4">
    <div class="card stat-card paid border-0 shadow-sm h-100">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-start mb-3">
          <div>
            <p class="text-muted text-uppercase small fw-semibold mb-2">Total Collected</p>
            <h2 class="mb-0 fw-bold text-success">₱<?= number_format($totalPaid, 2) ?></h2>
          </div>
          <div class="bg-success bg-opacity-10 p-3 rounded">
            <i class="fas fa-check-circle fa-2x text-success"></i>
          </div>
        </div>
        <p class="text-muted small mb-0">Total payments received</p>
      </div>
    </div>
  </div>
  
  <div class="col-md-4">
    <div class="card stat-card overdue border-0 shadow-sm h-100">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-start mb-3">
          <div>
            <p class="text-muted text-uppercase small fw-semibold mb-2">Overdue Amount</p>
            <h2 class="mb-0 fw-bold text-danger">₱<?= number_format($totalOverdue, 2) ?></h2>
          </div>
          <div class="bg-danger bg-opacity-10 p-3 rounded">
            <i class="fas fa-clock fa-2x text-danger"></i>
          </div>
        </div>
        <p class="text-muted small mb-0">Past due invoices</p>
      </div>
    </div>
  </div>
</div>

<!-- Billing Records Table -->
<div class="card border-0 shadow-sm mb-4">
  <div class="card-header bg-white border-bottom py-3">
    <div class="d-flex justify-content-between align-items-center">
      <div>
        <h5 class="mb-0 fw-semibold">Invoice Records</h5>
        <p class="text-muted small mb-0 mt-1">Complete billing and payment history</p>
      </div>
      <div class="input-group" style="max-width: 300px;">
        <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
        <input type="text" class="form-control border-start-0" placeholder="Search invoices..." id="searchBills">
      </div>
    </div>
  </div>
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table bills-table table-hover mb-0" id="billsTable">
        <thead>
          <tr>
            <th>Invoice Number</th>
            <th>Patient Name</th>
            <th>Bill Date</th>
            <th>Due Date</th>
            <th class="text-end">Total Amount</th>
            <th class="text-end">Amount Paid</th>
            <th class="text-end">Balance</th>
            <th>Status</th>
            <th class="text-center">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($bills)): ?>
            <?php foreach ($bills as $bill): ?>
              <?php
                $status = $bill['status'] ?? 'unpaid';
                $badge = 'secondary';
                $badgeText = 'Pending';
                if ($status === 'paid') {
                  $badge = 'success';
                  $badgeText = 'Paid';
                } elseif ($status === 'unpaid') {
                  $badge = 'warning';
                  $badgeText = 'Unpaid';
                } elseif ($status === 'overdue') {
                  $badge = 'danger';
                  $badgeText = 'Overdue';
                }
                
                $balance = (float)($bill['balance'] ?? max(0, (float)($bill['amount'] ?? 0)));
                $balanceClass = $balance > 0 ? 'positive' : 'zero';
              ?>
              <tr>
                <td>
                  <span class="invoice-badge"><?= esc($bill['invoice_no'] ?? ('INV-' . str_pad((string)($bill['id'] ?? 0), 6, '0', STR_PAD_LEFT))) ?></span>
                </td>
                <td>
                  <div class="fw-medium"><?= esc($bill['patient_name'] ?? '') ?></div>
                  <?php if (!empty($bill['description'])): ?>
                    <small class="text-muted"><?= esc(substr($bill['description'], 0, 40)) ?><?= strlen($bill['description']) > 40 ? '...' : '' ?></small>
                  <?php endif; ?>
                </td>
                <td><?= date('M d, Y', strtotime($bill['bill_date'] ?? '')) ?></td>
                <td>
                  <?php
                    $dueDate = $bill['due_date'] ?? '';
                    $today = date('Y-m-d');
                    $isOverdue = $dueDate < $today && $balance > 0;
                  ?>
                  <span class="<?= $isOverdue ? 'text-danger fw-semibold' : '' ?>">
                    <?= date('M d, Y', strtotime($dueDate)) ?>
                  </span>
                </td>
                <td class="text-end amount-cell">₱<?= number_format((float)($bill['amount'] ?? 0), 2) ?></td>
                <td class="text-end">₱<?= number_format((float)($bill['paid_total'] ?? 0), 2) ?></td>
                <td class="text-end balance-cell <?= $balanceClass ?>">
                  ₱<?= number_format($balance, 2) ?>
                </td>
                <td>
                  <span class="badge bg-<?= $badge ?> px-3 py-2"><?= $badgeText ?></span>
                </td>
                <td>
                  <div class="d-flex gap-2 justify-content-center">
                    <?php if ($balance > 0): ?>
                      <button type="button" class="btn btn-sm btn-outline-success" data-bs-toggle="modal" data-bs-target="#paymentModal<?= $bill['id'] ?>" title="Record Payment">
                        <i class="fas fa-money-bill-wave"></i>
                      </button>
                    <?php endif; ?>
                    <a href="#" class="btn btn-sm btn-outline-primary" title="View Details">
                      <i class="fas fa-eye"></i>
                    </a>
                    <a href="#" class="btn btn-sm btn-outline-secondary" title="Print Receipt">
                      <i class="fas fa-print"></i>
                    </a>
                  </div>
                </td>
              </tr>

              <!-- Payment Modal -->
              <div class="modal fade" id="paymentModal<?= $bill['id'] ?>" tabindex="-1">
                <div class="modal-dialog">
                  <div class="modal-content">
                    <form action="<?= base_url('admin/billing/' . (int)($bill['id'] ?? 0) . '/pay') ?>" method="post">
                      <?= csrf_field() ?>
                      <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title">Record Payment</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                      </div>
                      <div class="modal-body">
                        <div class="mb-3">
                          <label class="form-label fw-semibold">Invoice Number</label>
                          <input type="text" class="form-control" value="<?= esc($bill['invoice_no'] ?? '') ?>" readonly>
                        </div>
                        <div class="mb-3">
                          <label class="form-label fw-semibold">Patient</label>
                          <input type="text" class="form-control" value="<?= esc($bill['patient_name'] ?? '') ?>" readonly>
                        </div>
                        <div class="mb-3">
                          <label class="form-label fw-semibold">Outstanding Balance</label>
                          <input type="text" class="form-control fw-bold text-danger" value="₱<?= number_format($balance, 2) ?>" readonly>
                        </div>
                        <div class="mb-3">
                          <label class="form-label fw-semibold">Payment Amount <span class="text-danger">*</span></label>
                          <input type="number" name="amount" step="0.01" min="0.01" max="<?= $balance ?>" class="form-control" placeholder="Enter payment amount" required>
                        </div>
                        <div class="mb-3">
                          <label class="form-label fw-semibold">Payment Method</label>
                          <select name="method" class="form-select">
                            <option value="cash">Cash</option>
                            <option value="card">Credit/Debit Card</option>
                            <option value="gcash">GCash</option>
                            <option value="bank_transfer">Bank Transfer</option>
                            <option value="check">Check</option>
                          </select>
                        </div>
                        <div class="mb-3">
                          <label class="form-label">Notes (Optional)</label>
                          <textarea name="notes" class="form-control" rows="2" placeholder="Additional notes..."></textarea>
                        </div>
                      </div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">
                          <i class="fas fa-check me-2"></i>Record Payment
                        </button>
                      </div>
                    </form>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="9" class="text-center text-muted py-5">
                <i class="fas fa-inbox fa-3x mb-3 d-block opacity-25"></i>
                <p class="mb-0">No billing records found</p>
              </td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Quick Payment Section -->
<div class="card border-0 shadow-sm mb-4">
  <div class="card-header bg-white border-bottom py-3">
    <h5 class="mb-0 fw-semibold">Quick Payment Entry</h5>
  </div>
  <div class="card-body">
    <form action="<?= base_url('admin/billing/pay') ?>" method="post" class="row g-3">
      <?= csrf_field() ?>
      <div class="col-md-5">
        <label class="form-label fw-semibold">Select Invoice</label>
        <select name="bill_id" class="form-select" required>
          <option value="">-- Select invoice with outstanding balance --</option>
          <?php foreach (($openBills ?? []) as $ob): ?>
            <?php 
              $balance = (float)($ob['balance'] ?? (float)($ob['amount'] ?? 0));
              $label = ($ob['invoice_no'] ?? 'INV-' . str_pad((string)($ob['id'] ?? 0), 6, '0', STR_PAD_LEFT)) . ' — ' . 
                       ($ob['patient_name'] ?? 'Unknown') . ' — Balance: ₱' . number_format($balance, 2);
            ?>
            <option value="<?= (int)($ob['id'] ?? 0) ?>"><?= esc($label) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-3">
        <label class="form-label fw-semibold">Payment Amount</label>
        <input type="number" name="amount" step="0.01" min="0.01" class="form-control" placeholder="0.00" required>
      </div>
      <div class="col-md-2">
        <label class="form-label fw-semibold">Method</label>
        <select name="method" class="form-select">
          <option value="cash">Cash</option>
          <option value="card">Card</option>
          <option value="gcash">GCash</option>
          <option value="bank_transfer">Bank Transfer</option>
        </select>
      </div>
      <div class="col-md-2 d-flex align-items-end">
        <button type="submit" class="btn btn-success w-100">
          <i class="fas fa-check me-2"></i>Record
        </button>
      </div>
      <div class="col-12">
        <label class="form-label">Notes (Optional)</label>
        <input type="text" name="notes" class="form-control" placeholder="Optional payment notes">
      </div>
    </form>
  </div>
</div>

<!-- Recent Payments -->
<div class="card border-0 shadow-sm mb-4">
  <div class="card-header bg-white border-bottom py-3">
    <h5 class="mb-0 fw-semibold">Recent Payment Transactions</h5>
  </div>
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table bills-table mb-0">
        <thead>
          <tr>
            <th>Invoice #</th>
            <th>Patient</th>
            <th class="text-end">Amount</th>
            <th>Payment Method</th>
            <th>Date & Time</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($recentPayments)): ?>
            <?php foreach ($recentPayments as $p): ?>
              <tr>
                <td><span class="invoice-badge"><?= esc($p['bill_invoice_no'] ?? '') ?></span></td>
                <td><?= esc($p['bill_patient_name'] ?? '') ?></td>
                <td class="text-end amount-cell">₱<?= number_format((float)($p['amount'] ?? 0), 2) ?></td>
                <td>
                  <span class="badge bg-info"><?= esc(ucfirst($p['method'] ?? 'N/A')) ?></span>
                </td>
                <td><?= date('M d, Y h:i A', strtotime($p['paid_at'] ?? $p['created_at'] ?? '')) ?></td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="5" class="text-center text-muted py-4">No recent payments</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Create New Bill Form -->
<div id="new-bill-form" class="card border-0 shadow-sm">
  <div class="card-header bg-white border-bottom py-3">
    <h5 class="mb-0 fw-semibold">Create New Invoice</h5>
  </div>
  <div class="card-body">
    <form id="newBillForm" action="<?= base_url('admin/billing') ?>" method="post">
      <?= csrf_field() ?>
      <div class="row g-3">
        <div class="col-md-6">
          <label for="patientId" class="form-label fw-semibold">Patient <span class="text-danger">*</span></label>
          <select class="form-select" id="patientId" name="patient_id" required>
            <option value="">-- Select patient --</option>
            <?php foreach (($patientsList ?? []) as $p): ?>
              <?php 
                $code = $p['patient_code'] ?? '';
                $name = trim(($p['last_name'] ?? '') . ', ' . ($p['first_name'] ?? ''));
                $provider = trim((string)($p['insurance_provider'] ?? ''));
                $providerCoverage = [
                  'PhilHealth'  => 20.0,
                  'Maxicare'    => 25.0,
                  'MediCard'    => 15.0,
                  'PhilCare'    => 18.0,
                  'Cocolife'    => 22.0,
                  'Intellicare' => 12.0,
                  'Other'       => 10.0,
                ];
                $covTxt = '';
                if ($provider !== '' && isset($providerCoverage[$provider])) {
                  $covTxt = ' — ' . $provider . ' (' . rtrim(rtrim(number_format($providerCoverage[$provider], 2), '0'), '.') . '%)';
                }
                $label = ($code ? ($code . ' — ') : '') . $name . $covTxt;
                $value = (int)($p['id'] ?? 0);
                $selected = (string)old('patient_id') === (string)$value ? 'selected' : '';
              ?>
              <option value="<?= esc($value) ?>" <?= $selected ?>><?= esc($label) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-3">
          <label for="billDate" class="form-label fw-semibold">Invoice Date <span class="text-danger">*</span></label>
          <input type="date" class="form-control" id="billDate" name="bill_date" value="<?= old('bill_date') ?: date('Y-m-d') ?>" required>
        </div>
        <div class="col-md-3">
          <label for="dueDate" class="form-label fw-semibold">Due Date <span class="text-danger">*</span></label>
          <input type="date" class="form-control" id="dueDate" name="due_date" value="<?= old('due_date') ?: date('Y-m-d', strtotime('+30 days')) ?>" required>
        </div>
        <div class="col-12">
          <label for="description" class="form-label fw-semibold">Service Description <span class="text-danger">*</span></label>
          <textarea class="form-control" id="description" name="description" rows="3" placeholder="Describe the medical services or items billed" required><?= old('description') ?></textarea>
        </div>
        <div class="col-md-6">
          <label for="amount" class="form-label fw-semibold">Total Amount (₱) <span class="text-danger">*</span></label>
          <input type="number" step="0.01" min="0" class="form-control" id="amount" name="amount" value="<?= old('amount') ?>" placeholder="0.00" required>
        </div>
        <div class="col-md-6">
          <label for="invoiceNo" class="form-label fw-semibold">Invoice Number (Optional)</label>
          <input type="text" class="form-control" id="invoiceNo" name="invoice_no" value="<?= old('invoice_no') ?>" placeholder="Leave blank for auto-generation">
          <small class="text-muted">If left blank, system will generate automatically</small>
        </div>
      </div>
      <div class="mt-4 pt-3 border-top d-flex justify-content-end">
        <button type="reset" class="btn btn-outline-secondary me-2">Clear Form</button>
        <button type="submit" class="btn btn-primary">
          <i class="fas fa-save me-2"></i>Create Invoice
        </button>
      </div>
    </form>
  </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
// Simple search functionality
document.addEventListener('DOMContentLoaded', function() {
  const searchInput = document.getElementById('searchBills');
  if (searchInput) {
    searchInput.addEventListener('keyup', function() {
      const filter = this.value.toLowerCase();
      const table = document.getElementById('billsTable');
      const rows = table.getElementsByTagName('tr');

      for (let i = 1; i < rows.length; i++) {
        const row = rows[i];
        const text = row.textContent || row.innerText;
        if (text.toLowerCase().indexOf(filter) > -1) {
          row.style.display = '';
        } else {
          row.style.display = 'none';
        }
      }
    });
  }
});
</script>
<?= $this->endSection() ?>
