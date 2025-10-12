<?= $this->extend('layouts/template') ?>

<?= $this->section('content') ?>
<div class="dashboard-welcome">
    <div class="d-flex align-items-center">
        <i class="fas fa-user-tie fa-2x me-3"></i>
        <div>
            <h2 class="mb-1">Receptionist Dashboard</h2>
            <p class="mb-0 opacity-75">Welcome, <strong><?= esc($user['name'] ?? 'Receptionist') ?></strong>!</p>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-md-6 col-lg-3">
        <div class="card text-center">
            <div class="card-body">
                <div class="text-primary mb-3">
                    <i class="fas fa-calendar-check fa-2x"></i>
                </div>
                <h5 class="card-title">Appointments</h5>
                <h3 class="text-primary">28</h3>
                <small class="text-muted">Scheduled today</small>
            </div>
        </div>
    </div>
    
    <div class="col-md-6 col-lg-3">
        <div class="card text-center">
            <div class="card-body">
                <div class="text-success mb-3">
                    <i class="fas fa-user-plus fa-2x"></i>
                </div>
                <h5 class="card-title">Check-ins</h5>
                <h3 class="text-success">15</h3>
                <small class="text-muted">Patients checked in</small>
            </div>
        </div>
    </div>
    
    <div class="col-md-6 col-lg-3">
        <div class="card text-center">
            <div class="card-body">
                <div class="text-warning mb-3">
                    <i class="fas fa-clock fa-2x"></i>
                </div>
                <h5 class="card-title">Waiting</h5>
                <h3 class="text-warning">8</h3>
                <small class="text-muted">In waiting room</small>
            </div>
        </div>
    </div>
    
    <div class="col-md-6 col-lg-3">
        <div class="card text-center">
            <div class="card-body">
                <div class="text-info mb-3">
                    <i class="fas fa-phone fa-2x"></i>
                </div>
                <h5 class="card-title">Calls</h5>
                <h3 class="text-info">42</h3>
                <small class="text-muted">Handled today</small>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-list-alt me-2"></i>
                    Today's Appointments
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Time</th>
                                <th>Patient</th>
                                <th>Doctor</th>
                                <th>Type</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>09:00 AM</td>
                                <td>Emma Johnson</td>
                                <td>Dr. Smith</td>
                                <td>Consultation</td>
                                <td><span class="badge bg-success">Checked In</span></td>
                            </tr>
                            <tr>
                                <td>09:30 AM</td>
                                <td>Robert Davis</td>
                                <td>Dr. Wilson</td>
                                <td>Follow-up</td>
                                <td><span class="badge bg-warning">Waiting</span></td>
                            </tr>
                            <tr>
                                <td>10:00 AM</td>
                                <td>Lisa Brown</td>
                                <td>Dr. Johnson</td>
                                <td>Check-up</td>
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
                    <i class="fas fa-tasks me-2"></i>
                    Quick Actions
                </h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <button class="btn btn-outline-primary">
                        <i class="fas fa-user-plus me-2"></i>
                        Register New Patient
                    </button>
                    <button class="btn btn-outline-success">
                        <i class="fas fa-calendar-plus me-2"></i>
                        Schedule Appointment
                    </button>
                    <button class="btn btn-outline-info">
                        <i class="fas fa-check-circle me-2"></i>
                        Patient Check-in
                    </button>
                    <button class="btn btn-outline-warning">
                        <i class="fas fa-file-invoice-dollar me-2"></i>
                        Process Payment
                    </button>
                </div>
                
                <hr class="my-3">
                
                <h6 class="mb-3">Recent Messages</h6>
                <div class="d-flex align-items-start mb-2">
                    <div class="flex-shrink-0">
                        <div class="bg-info rounded-circle p-1" style="width: 8px; height: 8px;"></div>
                    </div>
                    <div class="flex-grow-1 ms-2">
                        <p class="small mb-0">Dr. Smith is running 15 min late</p>
                        <small class="text-muted">5 min ago</small>
                    </div>
                </div>
                
                <div class="d-flex align-items-start">
                    <div class="flex-shrink-0">
                        <div class="bg-success rounded-circle p-1" style="width: 8px; height: 8px;"></div>
                    </div>
                    <div class="flex-grow-1 ms-2">
                        <p class="small mb-0">Room 3 is now available</p>
                        <small class="text-muted">10 min ago</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
