<?= $this->extend('layouts/template') ?>

<?= $this->section('content') ?>
<div class="dashboard-welcome">
    <div class="d-flex align-items-center">
        <i class="fas fa-user-nurse fa-2x me-3"></i>
        <div>
            <h2 class="mb-1">Nurse Dashboard</h2>
            <p class="mb-0 opacity-75">Welcome, <strong><?= esc($user['name'] ?? 'Nurse') ?></strong>!</p>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-md-6 col-lg-3">
        <div class="card text-center">
            <div class="card-body">
                <div class="text-primary mb-3">
                    <i class="fas fa-users fa-2x"></i>
                </div>
                <h5 class="card-title">Patients Today</h5>
                <h3 class="text-primary">24</h3>
                <small class="text-muted">Active patients</small>
            </div>
        </div>
    </div>
    
    <div class="col-md-6 col-lg-3">
        <div class="card text-center">
            <div class="card-body">
                <div class="text-success mb-3">
                    <i class="fas fa-calendar-check fa-2x"></i>
                </div>
                <h5 class="card-title">Appointments</h5>
                <h3 class="text-success">12</h3>
                <small class="text-muted">Scheduled today</small>
            </div>
        </div>
    </div>
    
    <div class="col-md-6 col-lg-3">
        <div class="card text-center">
            <div class="card-body">
                <div class="text-warning mb-3">
                    <i class="fas fa-pills fa-2x"></i>
                </div>
                <h5 class="card-title">Medications</h5>
                <h3 class="text-warning">8</h3>
                <small class="text-muted">To be administered</small>
            </div>
        </div>
    </div>
    
    <div class="col-md-6 col-lg-3">
        <div class="card text-center">
            <div class="card-body">
                <div class="text-danger mb-3">
                    <i class="fas fa-exclamation-triangle fa-2x"></i>
                </div>
                <h5 class="card-title">Alerts</h5>
                <h3 class="text-danger">3</h3>
                <small class="text-muted">Urgent notifications</small>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-clipboard-list me-2"></i>
                    Today's Patient Schedule
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Time</th>
                                <th>Patient</th>
                                <th>Room</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>09:00 AM</td>
                                <td>John Doe</td>
                                <td>Room 101</td>
                                <td><span class="badge bg-success">Completed</span></td>
                            </tr>
                            <tr>
                                <td>10:30 AM</td>
                                <td>Jane Smith</td>
                                <td>Room 102</td>
                                <td><span class="badge bg-warning">In Progress</span></td>
                            </tr>
                            <tr>
                                <td>11:00 AM</td>
                                <td>Mike Johnson</td>
                                <td>Room 103</td>
                                <td><span class="badge bg-secondary">Scheduled</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-bell me-2"></i>
                    Recent Notifications
                </h5>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-start mb-3">
                    <div class="flex-shrink-0">
                        <div class="bg-danger rounded-circle p-2">
                            <i class="fas fa-exclamation text-white small"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="mb-1">Critical Alert</h6>
                        <p class="text-muted small mb-0">Patient in Room 205 requires immediate attention</p>
                        <small class="text-muted">5 minutes ago</small>
                    </div>
                </div>
                
                <div class="d-flex align-items-start mb-3">
                    <div class="flex-shrink-0">
                        <div class="bg-warning rounded-circle p-2">
                            <i class="fas fa-pills text-white small"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="mb-1">Medication Due</h6>
                        <p class="text-muted small mb-0">Insulin for Patient ID: 12345</p>
                        <small class="text-muted">15 minutes ago</small>
                    </div>
                </div>
                
                <div class="d-flex align-items-start">
                    <div class="flex-shrink-0">
                        <div class="bg-info rounded-circle p-2">
                            <i class="fas fa-info text-white small"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="mb-1">Schedule Update</h6>
                        <p class="text-muted small mb-0">New appointment added for 2:00 PM</p>
                        <small class="text-muted">30 minutes ago</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
