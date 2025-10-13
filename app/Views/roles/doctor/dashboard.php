<?= $this->extend('layouts/template') ?>

<?= $this->section('content') ?>
<?php
    // Compute schedule count for the logged-in doctor
    $db = db_connect();
    $userId = (int) (session()->get('user_id') ?? 0);
    $doctorRow = $db->table('doctors')->select('id')->where('user_id', $userId)->get()->getFirstRow('array');
    $doctorId = $doctorRow['id'] ?? 0;
    $scheduleCount = 0;
    if ($doctorId) {
        $scheduleCount = (int) $db->table('doctor_schedules')->where('doctor_id', $doctorId)->countAllResults();
    }
?>
<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-success text-white">
                    <h1 class="card-title mb-0">Doctor Dashboard</h1>
                </div>
                <div class="card-body">
                    <p class="lead">Welcome, <strong><?= esc($user['name']) ?></strong>!</p>
                    
                    <div class="row g-4 mb-4">
                        <div class="col-md-4">
                            <div class="card border-info">
                                <div class="card-body text-center">
                                    <h5 class="card-title text-info">Today's Appointments</h5>
                                    <h2 class="display-4 text-info">—</h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card border-warning">
                                <div class="card-body text-center">
                                    <h5 class="card-title text-warning">Pending Reports</h5>
                                    <h2 class="display-4 text-warning">—</h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card border-success">
                                <div class="card-body text-center">
                                    <h5 class="card-title text-success">Schedule</h5>
                                    <h2 class="display-5 text-success mb-2"><?= $scheduleCount ?></h2>
                                    <p class="mb-3 text-muted">Total schedules.</p>
                                    <a href="<?= base_url('scheduling') ?>" class="btn btn-success btn-sm">
                                        <i class="fas fa-calendar-alt me-1"></i> View My Schedule
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Recent Patient Activity</h5>
                        </div>
                        <div class="card-body">
                            <p class="text-muted">No recent patient activity to show.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
