<?= $this->extend('layouts/template') ?>

<?= $this->section('content') ?>
<div class="dashboard-welcome">
    <div class="d-flex align-items-center">
        <i class="fas fa-flask fa-2x me-3"></i>
        <div>
            <h2 class="mb-1">Laboratory Staff Dashboard</h2>
            <p class="mb-0 opacity-75">Welcome, <strong><?= esc($user['name'] ?? 'Lab Staff') ?></strong>!</p>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-md-6 col-lg-3">
        <div class="card text-center">
            <div class="card-body">
                <div class="text-primary mb-3">
                    <i class="fas fa-vial fa-2x"></i>
                </div>
                <h5 class="card-title">Pending Tests</h5>
                <h3 class="text-primary">18</h3>
                <small class="text-muted">Awaiting processing</small>
            </div>
        </div>
    </div>
    
    <div class="col-md-6 col-lg-3">
        <div class="card text-center">
            <div class="card-body">
                <div class="text-success mb-3">
                    <i class="fas fa-check-circle fa-2x"></i>
                </div>
                <h5 class="card-title">Completed</h5>
                <h3 class="text-success">42</h3>
                <small class="text-muted">Tests completed today</small>
            </div>
        </div>
    </div>
    
    <div class="col-md-6 col-lg-3">
        <div class="card text-center">
            <div class="card-body">
                <div class="text-warning mb-3">
                    <i class="fas fa-exclamation-triangle fa-2x"></i>
                </div>
                <h5 class="card-title">Urgent</h5>
                <h3 class="text-warning">5</h3>
                <small class="text-muted">Priority tests</small>
            </div>
        </div>
    </div>
    
    <div class="col-md-6 col-lg-3">
        <div class="card text-center">
            <div class="card-body">
                <div class="text-info mb-3">
                    <i class="fas fa-microscope fa-2x"></i>
                </div>
                <h5 class="card-title">Equipment</h5>
                <h3 class="text-info">12</h3>
                <small class="text-muted">Active machines</small>
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
                    Today's Test Queue
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Sample ID</th>
                                <th>Patient</th>
                                <th>Test Type</th>
                                <th>Priority</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>LAB001</td>
                                <td>John Doe</td>
                                <td>Blood Test</td>
                                <td><span class="badge bg-danger">Urgent</span></td>
                                <td><span class="badge bg-warning">In Progress</span></td>
                            </tr>
                            <tr>
                                <td>LAB002</td>
                                <td>Jane Smith</td>
                                <td>Urine Analysis</td>
                                <td><span class="badge bg-info">Normal</span></td>
                                <td><span class="badge bg-secondary">Pending</span></td>
                            </tr>
                            <tr>
                                <td>LAB003</td>
                                <td>Mike Johnson</td>
                                <td>X-Ray</td>
                                <td><span class="badge bg-warning">High</span></td>
                                <td><span class="badge bg-success">Completed</span></td>
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
                    <i class="fas fa-tools me-2"></i>
                    Equipment Status
                </h5>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="flex-shrink-0">
                        <div class="bg-success rounded-circle p-2">
                            <i class="fas fa-microscope text-white small"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="mb-1">Microscope #1</h6>
                        <p class="text-muted small mb-0">Status: Online</p>
                    </div>
                </div>
                
                <div class="d-flex align-items-center mb-3">
                    <div class="flex-shrink-0">
                        <div class="bg-warning rounded-circle p-2">
                            <i class="fas fa-vial text-white small"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="mb-1">Analyzer #2</h6>
                        <p class="text-muted small mb-0">Status: Maintenance</p>
                    </div>
                </div>
                
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="bg-success rounded-circle p-2">
                            <i class="fas fa-x-ray text-white small"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="mb-1">X-Ray Machine</h6>
                        <p class="text-muted small mb-0">Status: Available</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
