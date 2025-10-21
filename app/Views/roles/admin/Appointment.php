<?= $this->extend('layouts/template') ?>

<?= $this->section('content') ?>
<div class="dashboard-welcome mb-4">
  <div class="d-flex align-items-center">
    <i class="fas fa-calendar-check fa-2x me-3"></i>
    <div>
      <h2 class="mb-1">Appointments</h2>
      <p class="mb-0 opacity-75">Book a patient into an available doctor's schedule.</p>
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
  <div class="card-header d-flex align-items-center justify-content-between">
    <h5 class="mb-0">Available Schedules</h5>
    <form method="get" class="d-flex align-items-center gap-2">
      <label class="form-label mb-0">Week starting</label>
      <input type="date" name="week_start" value="<?= esc($weekStart ?? '') ?>" class="form-control" style="width: 200px;">
      <button class="btn btn-outline-primary">Apply</button>
    </form>
  </div>
  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-striped align-middle">
        <thead>
          <tr>
            <th>Date</th>
            <th>Day</th>
            <th>Doctor</th>
            <th>Start</th>
            <th>End</th>
            <th>Room</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($schedules)): ?>
            <?php foreach ($schedules as $sc): ?>
              <?php $doctorName = trim(($sc['first_name'] ?? '') . ' ' . ($sc['last_name'] ?? '')); ?>
              <tr>
                <td><?= esc($sc['date']) ?></td>
                <td><?= esc(date('l', strtotime($sc['date']))) ?></td>
                <td><?= esc($doctorName) ?></td>
                <td><?= esc($sc['start_time']) ?></td>
                <td><?= esc($sc['end_time']) ?></td>
                <td><?= esc($sc['room_number'] ?? '') ?></td>
                <td><?= esc(ucfirst($sc['status'])) ?></td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr><td colspan="7" class="text-center text-muted">No available schedules for the selected week.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php if (!empty($canAssign)): ?>
<div class="card mb-4">
  <div class="card-header">
    <h5 class="mb-0">Create Appointment</h5>
  </div>
  <div class="card-body">
    <form action="<?= site_url('appointments') ?>" method="post" class="row g-3">
      <?= csrf_field() ?>

      <div class="col-md-4">
        <label class="form-label">Patient <span class="text-danger">*</span></label>
        <select name="patient_id" class="form-select" required>
          <option value="">Select patient</option>
          <?php foreach (($patients ?? []) as $p): ?>
            <?php $pname = trim(($p['first_name'] ?? '') . ' ' . ($p['last_name'] ?? '')); ?>
            <option value="<?= esc($p['id']) ?>" <?= set_select('patient_id', $p['id']) ?>><?= esc($pname) ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="col-md-5">
        <label class="form-label">Doctor Schedule <span class="text-danger">*</span></label>
        <select name="doctor_schedule_id" class="form-select" required id="scheduleSelect">
          <option value="">Select a schedule (week <?= esc($weekStart ?? '') ?> - <?= esc($weekEnd ?? '') ?>)</option>
          <?php if (!empty($schedules)):
            $grouped = [];
            foreach ($schedules as $s) { $grouped[$s['date']][] = $s; }
            foreach ($grouped as $date => $items): ?>
              <optgroup label="<?= esc($date) ?> (<?= esc(date('l', strtotime($date))) ?>)">
                <?php foreach ($items as $s):
                  $docName = trim(($s['first_name'] ?? '') . ' ' . ($s['last_name'] ?? ''));
                  $label = $docName . ' | ' . ($s['start_time'] ?? '') . ' - ' . ($s['end_time'] ?? '') . ' | ' . ($s['room_number'] ?? '');
                ?>
                  <option value="<?= esc($s['schedule_id']) ?>" <?= set_select('doctor_schedule_id', $s['schedule_id']) ?>><?= esc($label) ?></option>
                <?php endforeach; ?>
              </optgroup>
            <?php endforeach; endif; ?>
        </select>
      </div>

      <div class="col-md-3">
        <label class="form-label">Appointment Date <span class="text-danger">*</span></label>
        <input type="date" name="appointment_date" value="<?= esc(old('appointment_date', $weekStart ?? '')) ?>" class="form-control" required>
        <div class="form-text">Must match the selected schedule's date.</div>
      </div>

      <div class="col-md-4">
        <label class="form-label">Schedule Type <span class="text-danger">*</span></label>
        <select name="schedule_type_id" class="form-select" required>
          <option value="">Select type</option>
          <?php foreach (($scheduleTypes ?? []) as $t): ?>
            <?php 
              $typeName = $t['type_name'] ?? ('Type #' . (string) $t['id']);
              $desc = $t['description'] ?? '';
              $tLabel = $desc !== '' ? ($typeName . ' - ' . $desc) : $typeName;
            ?>
            <option value="<?= esc($t['id']) ?>" <?= set_select('schedule_type_id', $t['id']) ?>><?= esc($tLabel) ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="col-md-3">
        <label class="form-label">Status</label>
        <select name="status" class="form-select">
          <?php $statuses = ['pending' => 'Pending', 'confirmed' => 'Confirmed', 'cancelled' => 'Cancelled']; ?>
          <?php foreach ($statuses as $key => $label): ?>
            <option value="<?= $key ?>" <?= set_select('status', $key, $key==='pending') ?>><?= $label ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="col-12 d-flex justify-content-end">
        <button class="btn btn-success">
          <i class="fas fa-save me-2"></i>Save Appointment
        </button>
      </div>
    </form>
  </div>
</div>
<?php endif; ?>


<div class="card">
  <div class="card-header">
    <h5 class="mb-0">This Week's Appointments (Mon–Fri)</h5>
  </div>
  <div class="card-body">
    <div class="row g-3">
      <?php foreach (($weekDays ?? []) as $d): ?>
        <?php $dayLabel = date('l', strtotime($d)); ?>
        <div class="col-12 col-md-6 col-lg-4 col-xl-2">
          <div class="border rounded h-100 d-flex flex-column">
            <div class="p-2 bg-light fw-semibold text-center">
              <?= esc($dayLabel) ?><br>
              <small class="text-muted"><?= esc($d) ?></small>
            </div>
            <div class="p-2 flex-grow-1">
              <?php $list = $appointmentsByDate[$d] ?? []; ?>
              <?php if (!empty($list)): ?>
                <div class="table-responsive">
                  <table class="table table-sm mb-0">
                    <thead>
                      <tr>
                        <th>Time</th>
                        <th>Patient</th>
                        <th>Doctor</th>
                        <th>Status</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php foreach ($list as $row): ?>
                        <tr>
                          <td><?= esc(substr($row['start_time'] ?? '',0,5)) ?><?= ($row['end_time']??'') ? ' - '.esc(substr($row['end_time'],0,5)) : '' ?></td>
                          <td><?= esc(trim(($row['p_first'] ?? '') . ' ' . ($row['p_last'] ?? ''))) ?></td>
                          <td><?= esc(trim(($row['d_first'] ?? '') . ' ' . ($row['d_last'] ?? ''))) ?></td>
                          <td><?= esc(ucfirst($row['status'] ?? '')) ?></td>
                        </tr>
                      <?php endforeach; ?>
                    </tbody>
                  </table>
                </div>
              <?php else: ?>
                <div class="text-center text-muted py-3">No appointments</div>
              <?php endif; ?>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</div>

<?= $this->endSection() ?>
