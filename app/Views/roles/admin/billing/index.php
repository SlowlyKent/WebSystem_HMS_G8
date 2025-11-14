<?= $this->extend('layouts/template') ?>

<?= $this->section('content') ?>
<div class="dashboard-welcome mb-4">
  <div class="d-flex justify-content-between align-items-center">
    <div class="d-flex align-items-center">
      <i class="fas fa-file-invoice-dollar fa-2x me-3"></i>
      <div>
        <h2 class="mb-1">Billing Dashboard</h2>
        <p class="mb-0 opacity-75">Manage patient bills, payments, and financial records</p>
      </div>
    </div>
    <?php $errors = session()->getFlashdata('errors'); ?>
    <?php if (session()->getFlashdata('success')): ?>
      <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= esc(session()->getFlashdata('success')) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    <?php endif; ?>
    <?php if (!empty($errors) && is_array($errors)): ?>
      <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong>There were some problems with your input:</strong>
        <ul class="mb-0 mt-2">
          <?php foreach ($errors as $field => $msg): ?>
            <li><?= esc($msg) ?></li>
          <?php endforeach; ?>
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    <?php endif; ?>
    <div class="btn-group">
      <a href="#new-bill-form" class="btn btn-primary">
        <i class="fas fa-plus me-2"></i>New Bill
      </a>
      <button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-expanded="false">
        <span class="visually-hidden">Toggle Dropdown</span>
      </button>
      <ul class="dropdown-menu">
        <li><a class="dropdown-item" href="#"><i class="fas fa-file-export me-2"></i>Export Report</a></li>
        <li><a class="dropdown-item" href="#"><i class="fas fa-filter me-2"></i>Filter Records</a></li>
        <li><hr class="dropdown-divider"></li>
        <li><a class="dropdown-item" href="#"><i class="fas fa-cog me-2"></i>Settings</a></li>
      </ul>
    </div>
  </div>
</div>

<!-- Bottom Payments Section -->
<div id="payments-section" class="card border-0 shadow-sm mt-4 mb-5">
  <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
    <div>
      <h5 class="mb-0">Recent Payments</h5>
      <p class="text-muted small mb-0 mt-1">Latest 10 recorded payments</p>
    </div>
  </div>
  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-striped">
        <thead>
          <tr>
            <th>Invoice #</th>
            <th>Patient</th>
            <th>Amount</th>
            <th>Method</th>
            <th>Paid At</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($recentPayments)): ?>
            <?php foreach ($recentPayments as $p): ?>
              <tr>
                <td><?= esc($p['bill_invoice_no'] ?? '') ?></td>
                <td><?= esc($p['bill_patient_name'] ?? '') ?></td>
                <td>₱<?= number_format((float)($p['amount'] ?? 0), 2) ?></td>
                <td><?= esc($p['method'] ?? '-') ?></td>
                <td><?= esc($p['paid_at'] ?? $p['created_at'] ?? '') ?></td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="5" class="text-center text-muted py-3">No payments recorded yet</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
  </div>

<!-- Quick Payment Section -->
<div id="payments-quick" class="card border-0 shadow-sm mt-4">
  <div class="card-header bg-white border-0 py-3">
    <h5 class="mb-0">Quick Payment</h5>
  </div>
  <div class="card-body">
    <form action="<?= base_url('admin/billing/pay') ?>" method="post" class="row g-3">
      <?= csrf_field() ?>
      <div class="col-md-6">
        <label class="form-label">Select Patient/Bill (with balance)</label>
        <select name="bill_id" class="form-select" required>
          <option value="">-- Choose bill --</option>
          <?php foreach (($openBills ?? []) as $ob): ?>
            <?php 
              $label = ($ob['patient_name'] ?? 'Unknown') . ' — ' . ($ob['invoice_no'] ?? ('INV-' . str_pad((string)($ob['id'] ?? 0), 6, '0', STR_PAD_LEFT))) . ' — Balance: ₱' . number_format((float)($ob['balance'] ?? (float)($ob['amount'] ?? 0)), 2);
            ?>
            <option value="<?= (int)($ob['id'] ?? 0) ?>"><?= esc($label) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-3">
        <label class="form-label">Amount</label>
        <input type="number" name="amount" step="0.01" min="0.01" class="form-control" placeholder="Enter amount" required>
      </div>
      <div class="col-md-3">
        <label class="form-label">Method</label>
        <select name="method" class="form-select">
          <option value="cash">Cash</option>
          <option value="card">Card</option>
          <option value="gcash">GCash</option>
        </select>
      </div>
      <div class="col-12">
        <label class="form-label">Notes (optional)</label>
        <input type="text" name="notes" class="form-control" placeholder="Optional notes">
      </div>
      <div class="col-12 d-flex justify-content-end">
        <button type="submit" class="btn btn-success">Record Payment</button>
      </div>
    </form>
  </div>
</div>

<div class="row g-4 mb-4">
  <!-- Summary Cards -->
  <div class="col-md-4">
    <div class="card border-0 bg-primary bg-opacity-10">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <h6 class="text-uppercase text-muted mb-1">Total Unpaid</h6>
            <h3 class="mb-0">₱<?= number_format($totalUnpaid, 2) ?></h3>
          </div>
          <div class="bg-primary bg-opacity-25 p-3 rounded-circle">
            <i class="fas fa-exclamation-circle fa-2x text-primary"></i>
          </div>
        </div>
      </div>
    </div>
  </div>
  
  <div class="col-md-4">
    <div class="card border-0 bg-success bg-opacity-10">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <h6 class="text-uppercase text-muted mb-1">Total Paid</h6>
            <h3 class="mb-0">₱<?= number_format($totalPaid, 2) ?></h3>
          </div>
          <div class="bg-success bg-opacity-25 p-3 rounded-circle">
            <i class="fas fa-check-circle fa-2x text-success"></i>
          </div>
        </div>
      </div>
    </div>
  </div>
  
  <div class="col-md-4">
    <div class="card border-0 bg-danger bg-opacity-10">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <h6 class="text-uppercase text-muted mb-1">Overdue</h6>
            <h3 class="mb-0">₱<?= number_format($totalOverdue, 2) ?></h3>
          </div>
          <div class="bg-danger bg-opacity-25 p-3 rounded-circle">
            <i class="fas fa-clock fa-2x text-danger"></i>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="card border-0 shadow-sm">
  <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
    <div>
      <h5 class="mb-0">Billing Records</h5>
      <p class="text-muted small mb-0 mt-1">Showing all billing records</p>
    </div>
    <div class="d-flex">
    <div class="input-group ms-3" style="max-width: 300px;">
      <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
      <input type="text" class="form-control border-start-0" placeholder="Search bills..." id="searchBills">
    </div>
    </div>
  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-hover" id="billsTable">
        <thead>
          <tr>
            <th>Invoice #</th>
            <th>Patient</th>
            <th>Date</th>
            <th>Due Date</th>
            <th>Total Amount</th>
            <th>Paid</th>
            <th>Balance</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($bills)): ?>
            <?php foreach ($bills as $bill): ?>
              <?php
                $status = $bill['status'] ?? 'unpaid';
                $badge = 'secondary';
                if ($status === 'paid') $badge = 'success';
                elseif ($status === 'unpaid') $badge = 'warning';
                elseif ($status === 'overdue') $badge = 'danger';
              ?>
              <tr>
                <td><?= esc($bill['invoice_no'] ?? ('INV-' . str_pad((string)($bill['id'] ?? 0), 6, '0', STR_PAD_LEFT))) ?></td>
                <td><?= esc($bill['patient_name'] ?? '') ?></td>
                <td><?= esc($bill['bill_date'] ?? '') ?></td>
                <td><?= esc($bill['due_date'] ?? '') ?></td>
                <td>₱<?= number_format((float)($bill['amount'] ?? 0), 2) ?></td>
                <td>₱<?= number_format((float)($bill['paid_total'] ?? 0), 2) ?></td>
                <td>₱<?= number_format((float)($bill['balance'] ?? max(0,(float)($bill['amount'] ?? 0))), 2) ?></td>
                <td><span class="badge bg-<?= $badge ?> text-capitalize"><?= esc($status) ?></span></td>
                <td>
                  <form class="d-flex gap-2 align-items-center" action="<?= base_url('admin/billing/' . (int)($bill['id'] ?? 0) . '/pay') ?>" method="post">
                    <?= csrf_field() ?>
                    <input type="number" name="amount" step="0.01" min="0.01" class="form-control form-control-sm" placeholder="Amount" style="max-width: 110px;">
                    <select name="method" class="form-select form-select-sm" style="max-width: 110px;">
                      <option value="cash">Cash</option>
                      <option value="card">Card</option>
                      <option value="gcash">GCash</option>
                    </select>
                    <button type="submit" class="btn btn-sm btn-outline-success" title="Record Payment"><i class="fas fa-money-bill"></i></button>
                    <a href="#" class="btn btn-sm btn-outline-primary" title="View"><i class="fas fa-eye"></i></a>
                    <a href="#" class="btn btn-sm btn-outline-secondary" title="Receipt"><i class="fas fa-print"></i></a>
                  </form>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="9" class="text-center text-muted py-4">No billing records found</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Inline New Bill Form Section -->
<div id="new-bill-form" class="card border-0 shadow-sm mt-4">
  <div class="card-header bg-white border-0 py-3">
    <h5 class="mb-0">Create New Bill</h5>
  </div>
  <div class="card-body">
    <form id="newBillForm" action="<?= base_url('admin/billing') ?>" method="post">
      <?= csrf_field() ?>
      <div class="row g-3">
        <div class="col-md-6">
          <label for="patientId" class="form-label">Patient</label>
          <select class="form-select" id="patientId" name="patient_id" required>
            <option value="">-- Select patient --</option>
            <?php foreach (($patientsList ?? []) as $p): ?>
              <?php 
                $code = $p['patient_code'] ?? '';
                $name = trim(($p['last_name'] ?? '') . ', ' . ($p['first_name'] ?? ''));
                $provider = trim((string)($p['insurance_provider'] ?? ''));
                // Match the same coverage map as backend for display
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
          <label for="billDate" class="form-label">Date</label>
          <input type="date" class="form-control" id="billDate" name="bill_date" value="<?= old('bill_date') ?>" required>
        </div>
        <div class="col-md-3">
          <label for="dueDate" class="form-label">Due Date</label>
          <input type="date" class="form-control" id="dueDate" name="due_date" value="<?= old('due_date') ?>" required>
        </div>
        <div class="col-12">
          <label for="description" class="form-label">Description</label>
          <textarea class="form-control" id="description" name="description" rows="3" placeholder="Describe the services rendered" required><?= old('description') ?></textarea>
        </div>
        <div class="col-md-4">
          <label for="amount" class="form-label">Amount (₱)</label>
          <input type="number" step="0.01" min="0" class="form-control" id="amount" name="amount" value="<?= old('amount') ?>" required>
        </div>
        <div class="col-md-4">
          <label for="invoiceNo" class="form-label">Invoice #</label>
          <input type="text" class="form-control" id="invoiceNo" name="invoice_no" value="<?= old('invoice_no') ?>" placeholder="e.g., INV-2025-001">
        </div>
      </div>
      <div class="mt-3 d-flex justify-content-end">
        <button type="submit" class="btn btn-primary">Save Bill</button>
      </div>
    </form>
  </div>
</div>

<!-- Add this to your template's scripts section -->
<?= $this->section('scripts') ?>
<!-- Make sure jQuery is loaded before Bootstrap JS -->
<script>
// Wait for everything to be fully loaded
window.addEventListener('load', function() {
        // Initialize DataTable
        if ($.fn.DataTable) {
            var table = $('#billsTable').DataTable({
                order: [[2, 'desc']], // Sort by date by default
                responsive: true,
                dom: '<"d-flex justify-content-between align-items-center mb-3"f<"d-flex align-items-center">>rt<"d-flex justify-content-between align-items-center"ip>',
                language: {
                    search: "",
                    searchPlaceholder: "Search bills...",
                    lengthMenu: "Show _MENU_ entries",
                    info: "Showing _START_ to _END_ of _TOTAL_ entries",
                    infoEmpty: "No records available",
                    zeroRecords: "No matching records found"
                },
                initComplete: function() {
                    // Add custom filter buttons
                    var filterDiv = $('<div class="btn-group ms-3" role="group">' +
                        '<input type="radio" class="btn-check" name="statusFilter" id="allStatus" autocomplete="off" checked>' +
                        '<label class="btn btn-outline-secondary btn-sm" for="allStatus">All</label>' +
                        '<input type="radio" class="btn-check" name="statusFilter" id="paidStatus" autocomplete="off">' +
                        '<label class="btn btn-outline-success btn-sm" for="paidStatus">Paid</label>' +
                        '<input type="radio" class="btn-check" name="statusFilter" id="unpaidStatus" autocomplete="off">' +
                        '<label class="btn btn-outline-warning btn-sm" for="unpaidStatus">Unpaid</label>' +
                        '<input type="radio" class="btn-check" name="statusFilter" id="overdueStatus" autocomplete="off">' +
                        '<label class="btn btn-outline-danger btn-sm" for="overdueStatus">Overdue</label>' +
                        '</div>');
                    
                    $('.dataTables_wrapper .dataTables_filter').append(filterDiv);
                    
                    // Filter table when radio buttons change
                    $('input[name="statusFilter"]').change(function() {
                        var status = $(this).attr('id').replace('Status', '');
                        if (status === 'all') {
                            table.column(5).search('').draw();
                        } else {
                            table.column(5).search('^' + status + '$', true, false).draw();
                        }
                    });
                }
            });
            
            // Custom search for the search input
            $('#searchBills').on('keyup', function() {
                table.search(this.value).draw();
            });
        }
        
        // Tooltip initialization
    if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.forEach(function(tooltipTriggerEl) {
            new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }
});
</script>

<?= $this->endSection() ?>


<?= $this->endSection() ?>
