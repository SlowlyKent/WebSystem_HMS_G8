<?= $this->extend('layouts/template') ?>

<?= $this->section('content') ?>

<div class="dashboard-welcome mb-4">
  <div class="d-flex align-items-center justify-content-between">
    <div class="d-flex align-items-center">
      <i class="fas fa-user fa-2x me-3"></i>
      <div>
        <h2 class="mb-1">Patient Details</h2>
        <p class="mb-0 opacity-75">View patient information and records.</p>
      </div>
    </div>
    <div>
      <a href="<?= site_url('admin/patients/registration') ?>" class="btn btn-outline-secondary me-2">
        <i class="fas fa-arrow-left me-2"></i>Back to List
      </a>
      <a href="<?= site_url('admin/patients/edit/' . $patient['id']) ?>" class="btn btn-primary">
        <i class="fas fa-edit me-2"></i>Edit Patient
      </a>
    </div>
  </div>
</div>

<?php if (session()->getFlashdata('success')): ?>
  <div class="alert alert-success mb-3"><?= session()->getFlashdata('success') ?></div>
<?php endif; ?>
<?php if (session()->getFlashdata('error')): ?>
  <div class="alert alert-danger mb-3"><?= esc(session()->getFlashdata('error')) ?></div>
<?php endif; ?>

<div class="row">
  <!-- Personal Information -->
  <div class="col-md-6 mb-4">
    <div class="card">
      <div class="card-header bg-primary text-white">
        <h5 class="mb-0"><i class="fas fa-id-card me-2"></i>Personal Information</h5>
      </div>
      <div class="card-body">
        <table class="table table-borderless">
          <tr>
            <th width="40%">Patient Code:</th>
            <td><strong><?= esc($patient['patient_code'] ?? 'N/A') ?></strong></td>
          </tr>
          <tr>
            <th>Full Name:</th>
            <td><?= esc(trim(($patient['first_name'] ?? '') . ' ' . ($patient['middle_name'] ?? '') . ' ' . ($patient['last_name'] ?? ''))) ?></td>
          </tr>
          <tr>
            <th>Date of Birth:</th>
            <td><?= $patient['date_of_birth'] ? date('F d, Y', strtotime($patient['date_of_birth'])) : 'N/A' ?></td>
          </tr>
          <tr>
            <th>Gender:</th>
            <td><?= esc($patient['gender'] ?? 'N/A') ?></td>
          </tr>
          <tr>
            <th>Blood Type:</th>
            <td><?= esc($patient['blood_type'] ?? 'N/A') ?></td>
          </tr>
          <tr>
            <th>Status:</th>
            <td><span class="badge bg-info"><?= esc($patient['status'] ?? 'N/A') ?></span></td>
          </tr>
        </table>
      </div>
    </div>
  </div>

  <!-- Contact Information -->
  <div class="col-md-6 mb-4">
    <div class="card">
      <div class="card-header bg-info text-white">
        <h5 class="mb-0"><i class="fas fa-address-book me-2"></i>Contact Information</h5>
      </div>
      <div class="card-body">
        <table class="table table-borderless">
          <tr>
            <th width="40%">Phone:</th>
            <td><?= esc($patient['phone'] ?? 'N/A') ?></td>
          </tr>
          <tr>
            <th>Email:</th>
            <td><?= esc($patient['email'] ?? 'N/A') ?></td>
          </tr>
          <tr>
            <th>Address:</th>
            <td>
              <?php if ($address): ?>
                <?php
                $addressParts = array_filter([
                  $address['street'] ?? '',
                  $address['barangay'] ?? '',
                  $address['city_municipality'] ?? '',
                  $address['province'] ?? ''
                ]);
                echo !empty($addressParts) ? esc(implode(', ', $addressParts)) : 'N/A';
                ?>
              <?php else: ?>
                N/A
              <?php endif; ?>
            </td>
          </tr>
        </table>
      </div>
    </div>
  </div>
</div>

<!-- Insurance Information -->
<div class="card mb-4">
  <div class="card-header bg-success text-white">
    <h5 class="mb-0"><i class="fas fa-shield-alt me-2"></i>Insurance Information</h5>
  </div>
  <div class="card-body">
    <div class="row">
      <div class="col-md-3">
        <strong>Provider:</strong><br>
        <?= esc($patient['insurance_provider'] ?? 'N/A') ?>
      </div>
      <div class="col-md-3">
        <strong>Policy Number:</strong><br>
        <?= esc($patient['insurance_policy_no'] ?? 'N/A') ?>
      </div>
      <div class="col-md-3">
        <strong>Coverage:</strong><br>
        <?= $patient['insurance_coverage_pct'] ? esc($patient['insurance_coverage_pct']) . '%' : 'N/A' ?>
      </div>
      <div class="col-md-3">
        <strong>Max per Bill:</strong><br>
        <?= $patient['insurance_max_per_bill'] ? '₱' . number_format($patient['insurance_max_per_bill'], 2) : 'N/A' ?>
      </div>
      <?php if ($patient['insurance_valid_until']): ?>
      <div class="col-md-3 mt-3">
        <strong>Valid Until:</strong><br>
        <?= date('F d, Y', strtotime($patient['insurance_valid_until'])) ?>
      </div>
      <?php endif; ?>
    </div>
  </div>
</div>

<!-- Medical Notes -->
<?php if (!empty($patient['medical_notes'])): ?>
<div class="card">
  <div class="card-header bg-warning text-dark">
    <h5 class="mb-0"><i class="fas fa-notes-medical me-2"></i>Medical Notes</h5>
  </div>
  <div class="card-body">
    <p class="mb-0"><?= nl2br(esc($patient['medical_notes'])) ?></p>
  </div>
</div>
<?php endif; ?>

<?= $this->endSection() ?>

