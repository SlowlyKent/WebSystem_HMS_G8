<?= $this->extend('layouts/template') ?>

<?= $this->section('content') ?>
<div class="dashboard-welcome mb-4">
  <div class="d-flex align-items-center">
    <a href="<?= base_url('laboratory') ?>" class="btn btn-outline-secondary me-3">
      <i class="fas fa-arrow-left"></i> Back
    </a>
    <i class="fas fa-file-medical fa-2x me-3"></i>
    <div>
      <h2 class="mb-1">Lab Test Results</h2>
      <p class="mb-0 opacity-75">Request Code: <strong><?= esc($request['request_code'] ?? 'N/A') ?></strong></p>
    </div>
  </div>
</div>

<?php if (session()->getFlashdata('success')): ?>
  <div class="alert alert-success alert-dismissible fade show" role="alert">
    <?= esc(session()->getFlashdata('success')) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
<?php endif; ?>

<div class="row g-4 mb-4">
  <!-- Patient Information -->
  <div class="col-md-6">
    <div class="card border-0 shadow-sm">
      <div class="card-header bg-primary text-white">
        <h5 class="mb-0"><i class="fas fa-user me-2"></i>Patient Information</h5>
      </div>
      <div class="card-body">
        <p class="mb-2"><strong>Name:</strong> <?= esc($patient['first_name'] ?? '') ?> <?= esc($patient['last_name'] ?? '') ?></p>
        <p class="mb-2"><strong>Patient Code:</strong> <?= esc($patient['patient_code'] ?? 'N/A') ?></p>
        <p class="mb-2"><strong>Date of Birth:</strong> <?= esc($patient['date_of_birth'] ?? 'N/A') ?></p>
        <p class="mb-0"><strong>Gender:</strong> <?= esc($patient['gender'] ?? 'N/A') ?></p>
      </div>
    </div>
  </div>

  <!-- Request Information -->
  <div class="col-md-6">
    <div class="card border-0 shadow-sm">
      <div class="card-header bg-info text-white">
        <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Request Information</h5>
      </div>
      <div class="card-body">
        <p class="mb-2"><strong>Request Date:</strong> <?= esc($request['request_date'] ?? 'N/A') ?></p>
        <p class="mb-2"><strong>Doctor:</strong> Dr. <?= esc($doctor['first_name'] ?? '') ?> <?= esc($doctor['last_name'] ?? '') ?></p>
        <p class="mb-2"><strong>Priority:</strong> 
          <span class="badge bg-<?= $request['priority'] === 'urgent' ? 'danger' : ($request['priority'] === 'stat' ? 'warning' : 'info') ?>">
            <?= ucfirst($request['priority'] ?? 'routine') ?>
          </span>
        </p>
        <p class="mb-0"><strong>Status:</strong> 
          <span class="badge bg-<?= $request['status'] === 'completed' ? 'success' : ($request['status'] === 'in_progress' ? 'warning' : 'secondary') ?>">
            <?= ucfirst(str_replace('_', ' ', $request['status'] ?? 'pending')) ?>
          </span>
        </p>
      </div>
    </div>
  </div>
</div>

<!-- Sample Information -->
<?php if (!empty($sample)): ?>
<div class="card border-0 shadow-sm mb-4">
  <div class="card-header bg-secondary text-white">
    <h5 class="mb-0"><i class="fas fa-vial me-2"></i>Sample Information</h5>
  </div>
  <div class="card-body">
    <div class="row">
      <div class="col-md-4">
        <p class="mb-2"><strong>Sample Type:</strong> <?= esc($sample['sample_type'] ?? 'N/A') ?></p>
      </div>
      <div class="col-md-4">
        <p class="mb-2"><strong>Collection Date:</strong> <?= esc($sample['collection_date'] ?? 'N/A') ?></p>
      </div>
      <div class="col-md-4">
        <?php if (!empty($sample['notes'])): ?>
          <p class="mb-0"><strong>Notes:</strong> <?= esc($sample['notes']) ?></p>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>
<?php endif; ?>

<!-- Test Results -->
<div class="card border-0 shadow-sm">
  <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
    <h5 class="mb-0"><i class="fas fa-flask me-2"></i>Test Results</h5>
    <?php if ($request['status'] !== 'completed'): ?>
      <form action="<?= base_url('laboratory/verify/' . $request['id']) ?>" method="post" class="d-inline">
        <?= csrf_field() ?>
        <button type="submit" class="btn btn-light btn-sm" onclick="return confirm('Verify and complete this lab request?')">
          <i class="fas fa-check me-1"></i>Verify & Complete
        </button>
      </form>
    <?php endif; ?>
  </div>
  <div class="card-body">
    <?php if (!empty($testTypes)): ?>
      <div class="table-responsive">
        <table class="table table-bordered">
          <thead class="table-light">
            <tr>
              <th>Test Name</th>
              <th>Category</th>
              <th>Normal Range</th>
              <th>Result Value</th>
              <th>Status</th>
              <th>Notes</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($testTypes as $test): ?>
              <?php
                // Find result for this test
                $result = null;
                foreach ($results as $r) {
                  if ($r['test_type_id'] == $test['id']) {
                    $result = $r;
                    break;
                  }
                }
              ?>
              <tr>
                <td><strong><?= esc($test['name']) ?></strong></td>
                <td><?= esc($test['category'] ?? 'N/A') ?></td>
                <td>
                  <?= esc($test['normal_range'] ?? 'N/A') ?>
                  <?php if (!empty($test['unit'])): ?>
                    <span class="text-muted">(<?= esc($test['unit']) ?>)</span>
                  <?php endif; ?>
                </td>
                <td>
                  <?php if ($result): ?>
                    <span class="badge bg-<?= $result['is_normal'] ? 'success' : 'danger' ?>">
                      <?= esc($result['result_value'] ?? 'N/A') ?>
                    </span>
                  <?php else: ?>
                    <span class="text-muted">Pending</span>
                  <?php endif; ?>
                </td>
                <td>
                  <?php if ($result): ?>
                    <?php
                      $statusClass = $result['status'] === 'verified' ? 'bg-success' : 
                                    ($result['status'] === 'approved' ? 'bg-primary' : 'bg-warning');
                    ?>
                    <span class="badge <?= $statusClass ?>">
                      <?= ucfirst($result['status'] ?? 'pending') ?>
                    </span>
                  <?php else: ?>
                    <span class="badge bg-secondary">Not Entered</span>
                  <?php endif; ?>
                </td>
                <td><?= esc($result['notes'] ?? '-') ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php else: ?>
      <p class="text-muted text-center py-4">No tests found for this request.</p>
    <?php endif; ?>
  </div>
</div>

<!-- Print Button -->
<div class="mt-4 text-center">
  <button onclick="window.print()" class="btn btn-primary">
    <i class="fas fa-print me-2"></i>Print Report
  </button>
</div>

<?= $this->endSection() ?>

