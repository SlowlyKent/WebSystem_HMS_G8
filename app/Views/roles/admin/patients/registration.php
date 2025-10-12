<?= $this->extend('layouts/template') ?>

<?= $this->section('content') ?>
<div class="dashboard-welcome mb-4">
  <div class="d-flex align-items-center">
    <i class="fas fa-id-card fa-2x me-3"></i>
    <div>
      <h2 class="mb-1">Patient Registration</h2>
      <p class="mb-0 opacity-75">Fill the form below to register a new patient.</p>
    </div>
  </div>
</div>

<?php if (session()->getFlashdata('success')): ?>
  <div class="alert alert-success mb-3"><?= session()->getFlashdata('success') ?></div>
<?php endif; ?>
<?php if (session()->getFlashdata('error')): ?>
  <div class="alert alert-danger mb-3"><?= esc(session()->getFlashdata('error')) ?></div>
<?php endif; ?>

<div class="card mb-4">
  <div class="card-header">
    <h5 class="mb-0">Add New Patient Record</h5>
  </div>
  <div class="card-body">
    <form action="<?= site_url('admin/patients/registration') ?>" method="post" class="row g-3">
      <?= csrf_field() ?>
      <input type="hidden" name="patient_code" value="">
      <div class="col-12 mb-3">
        <div class="alert alert-info d-flex align-items-center">
          <i class="fas fa-info-circle me-2"></i>
          <span>Patient code will be automatically generated upon submission.</span>
        </div>
      </div>
      <div class="col-md-4">
        <label class="form-label">First Name <span class="text-danger">*</span></label>
        <input type="text" name="first_name" value="<?= old('first_name') ?>" class="form-control" required>
      </div>
      <div class="col-md-4">
        <label class="form-label">Middle Name</label>
        <input type="text" name="middle_name" value="<?= old('middle_name') ?>" class="form-control">
      </div>

      <div class="col-md-4">
        <label class="form-label">Last Name <span class="text-danger">*</span></label>
        <input type="text" name="last_name" value="<?= old('last_name') ?>" class="form-control" required>
      </div>
      <div class="col-md-4">
        <label class="form-label">Date of Birth</label>
        <input type="date" name="date_of_birth" value="<?= old('date_of_birth') ?>" class="form-control">
      </div>
      <div class="col-md-4">
        <label class="form-label">Gender</label>
        <select name="gender" class="form-select">
          <option value="" <?= old('gender') === '' ? 'selected' : '' ?>>Select gender</option>
          <option value="Male" <?= old('gender') === 'Male' ? 'selected' : '' ?>>Male</option>
          <option value="Female" <?= old('gender') === 'Female' ? 'selected' : '' ?>>Female</option>
          <option value="Other" <?= old('gender') === 'Other' ? 'selected' : '' ?>>Other</option>
        </select>
      </div>

      <div class="col-md-4">
        <label class="form-label">Phone</label>
        <input type="text" name="phone" value="<?= old('phone') ?>" class="form-control">
      </div>
      <div class="col-md-4">
        <label class="form-label">Email</label>
        <input type="email" name="email" value="<?= old('email') ?>" class="form-control">
      </div>
      <div class="col-md-4">
        <label class="form-label">Status</label>
        <select name="status" class="form-select">
          <option value="" <?= old('status') === '' ? 'selected' : '' ?>>Select status</option>
          <option value="Inpatient" <?= old('status') === 'Inpatient' ? 'selected' : '' ?>>Inpatient</option>
          <option value="Outpatient" <?= old('status') === 'Outpatient' ? 'selected' : '' ?>>Outpatient</option>
          <option value="Discharged" <?= old('status') === 'Discharged' ? 'selected' : '' ?>>Discharged</option>
        </select>
      </div>

      <div class="col-md-8">
        <label class="form-label">Address</label>
        <input type="text" name="address" value="<?= old('address') ?>" class="form-control">
      </div>
      <div class="col-md-2">
        <label class="form-label">Room</label>
        <input type="text" name="room" value="<?= old('room') ?>" class="form-control" placeholder="e.g., 302">
      </div>
      <div class="col-12">
        <label class="form-label">Initial Medical Notes</label>
        <textarea name="medical_notes" class="form-control" rows="3" placeholder="Any initial observations or notes..."><?= old('medical_notes') ?></textarea>
      </div>

      <div class="col-12 d-flex justify-content-end">
        <button class="btn btn-success">
          <i class="fas fa-save me-2"></i>Register Patient
        </button>
      </div>
    </form>
  </div>
</div>

<div class="card">
  <div class="card-header">
    <h5 class="mb-0">Existing Patient Records</h5>
  </div>
  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-striped align-middle">
        <thead>
          <tr>
            <th>Code</th>
            <th>Name</th>
            <th>Gender</th>
            <th>Status</th>
            <th>Room</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($patients)): ?>
            <?php foreach ($patients as $p): ?>
              <tr>
                <td><?= esc($p['patient_code'] ?? 'N/A') ?></td>
                <td><?= esc(trim(($p['first_name'] ?? '') . ' ' . ($p['last_name'] ?? ''))) ?></td>
                <td><?= esc($p['gender'] ?? 'N/A') ?></td>
                <td><?= esc($p['status'] ?? 'N/A') ?></td>
                <td><?= esc($p['room'] ?? 'N/A') ?></td>
                <td>
                  <div class="btn-group btn-group-sm" role="group">
                    <a class="btn btn-outline-primary" href="#" title="View"><i class="fas fa-eye"></i></a>
                    <a class="btn btn-outline-secondary" href="#" title="Edit"><i class="fas fa-edit"></i></a>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr><td colspan="6" class="text-center text-muted">No patient records yet.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?= $this->endSection() ?>
