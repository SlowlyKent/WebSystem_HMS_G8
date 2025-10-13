<?= $this->extend('layouts/template') ?>

<?= $this->section('content') ?>
<div class="dashboard-welcome mb-4">
  <div class="d-flex align-items-center">
    <i class="fas fa-calendar-alt fa-2x me-3"></i>
    <div>
      <h2 class="mb-1">Doctor Scheduling</h2>
      <p class="mb-0 opacity-75">Assign a schedule to an existing doctor.</p>
    </div>
  </div>
  </div>

<?php if (session()->getFlashdata('success')): ?>
  <div class="alert alert-success mb-3"><?= session()->getFlashdata('success') ?></div>
<?php endif; ?>
<?php if (session()->getFlashdata('error')): ?>
  <div class="alert alert-danger mb-3"><?= esc(session()->getFlashdata('error')) ?></div>
<?php endif; ?>

<?php if (!empty($canAssign)): ?>
<div class="card mb-4">
  <div class="card-header">
    <h5 class="mb-0">Assign Schedule</h5>
  </div>
  <div class="card-body">
    <form action="<?= site_url('scheduling') ?>" method="post" class="row g-3">
      <?= csrf_field() ?>

      <div class="col-md-4">
        <label class="form-label">Doctor <span class="text-danger">*</span></label>
        <select name="doctor_id" class="form-select" required>
          <option value="">Select doctor</option>
          <?php foreach (($doctors ?? []) as $d): ?>
            <?php $name = trim(($d['first_name'] ?? '') . ' ' . ($d['last_name'] ?? '')); ?>
            <option value="<?= esc($d['id']) ?>" <?= set_select('doctor_id', $d['id']) ?>><?= esc($name) ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="col-md-3">
        <label class="form-label">Day of the Week <span class="text-danger">*</span></label>
        <select name="day_of_week" class="form-select" required>
          <?php $days = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday']; ?>
          <option value="">Select day</option>
          <?php foreach ($days as $day): ?>
            <option value="<?= strtolower($day) ?>" <?= set_select('day_of_week', strtolower($day)) ?>><?= $day ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="col-md-2">
        <label class="form-label">Start Time <span class="text-danger">*</span></label>
        <input type="time" name="start_time" value="<?= old('start_time') ?>" class="form-control" required>
      </div>
      <div class="col-md-2">
        <label class="form-label">End Time <span class="text-danger">*</span></label>
        <input type="time" name="end_time" value="<?= old('end_time') ?>" class="form-control" required>
      </div>

      <div class="col-md-3">
        <label class="form-label">Room</label>
        <select name="room_number" class="form-select" id="roomSelect">
          <option value="">Select a room</option>
          <?php
          $roomTypes = [
            'EMM' => 'EMM',
            'AGM' => 'AGM',
            'CLINIC' => 'CLINIC',
            'LAB' => 'LAB',
            'WARD' => 'WARD'
          ];

          foreach ($roomTypes as $key => $type) {
            echo "<optgroup label='{$type} Rooms'>";
            for ($i = 1; $i <= 5; $i++) {
              $room = "$key " . str_pad($i, 2, '0', STR_PAD_LEFT);
              $selected = (old('room_number') === $room) ? 'selected' : '';
              echo "<option value='{$room}' {$selected}>{$room}</option>";
            }
            echo '</optgroup>';
          }
          ?>
        </select>
      </div>

      <div class="col-md-3">
        <label class="form-label">Status <span class="text-danger">*</span></label>
        <select name="status" class="form-select" required>
          <?php $statuses = ['available' => 'Available', 'booked' => 'Booked', 'cancelled' => 'Cancelled']; ?>
          <option value="">Select status</option>
          <?php foreach ($statuses as $key => $label): ?>
            <option value="<?= $key ?>" <?= set_select('status', $key) ?>><?= $label ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="col-12 d-flex justify-content-end">
        <button class="btn btn-success">
          <i class="fas fa-save me-2"></i>Save Schedule
        </button>
      </div>
    </form>
  </div>
</div>
<?php endif; ?>

<div class="card">
  <div class="card-header">
    <h5 class="mb-0">Assigned Schedules</h5>
  </div>
  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-striped align-middle">
        <thead>
          <tr>
            <th>Doctor</th>
            <th>Day</th>
            <th>Date</th>
            <th>Start</th>
            <th>End</th>
            <th>Room</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($schedules)): ?>
            <?php foreach ($schedules as $row): ?>
              <?php 
                $doctorName = trim(($row['first_name'] ?? '') . ' ' . ($row['last_name'] ?? ''));
                $date = $row['date'] ?? '';
                $dayName = $date ? date('l', strtotime($date)) : '';
              ?>
              <tr>
                <td><?= esc($doctorName) ?></td>
                <td><?= esc($dayName) ?></td>
                <td><?= esc($date) ?></td>
                <td><?= esc($row['start_time'] ?? '') ?></td>
                <td><?= esc($row['end_time'] ?? '') ?></td>
                <td><?= esc($row['room_number'] ?? '') ?></td>
                <td><?= esc(ucfirst($row['status'] ?? '')) ?></td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr><td colspan="7" class="text-center text-muted">No schedules yet.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?= $this->endSection() ?>