<?= $this->extend('layouts/template') ?>

<?= $this->section('content') ?>
<div class="dashboard-welcome">
    <div class="d-flex align-items-center">
        <i class="fas fa-pills fa-2x me-3"></i>
        <div>
            <h2 class="mb-1">Pharmacist Dashboard</h2>
            <p class="mb-0 opacity-75">Welcome, <strong><?= esc($user['name'] ?? 'Pharmacist') ?></strong>!</p>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-md-6 col-lg-3">
        <div class="card text-center">
            <div class="card-body">
                <div class="text-primary mb-3">
                    <i class="fas fa-prescription-bottle-alt fa-2x"></i>
                </div>
                <h5 class="card-title">Prescriptions</h5>
                <h3 class="text-primary">32</h3>
                <small class="text-muted">Pending today</small>
            </div>
        </div>
    </div>
    
    <div class="col-md-6 col-lg-3">
        <div class="card text-center">
            <div class="card-body">
                <div class="text-success mb-3">
                    <i class="fas fa-check-circle fa-2x"></i>
                </div>
                <h5 class="card-title">Dispensed</h5>
                <h3 class="text-success">89</h3>
                <small class="text-muted">Completed today</small>
            </div>
        </div>
    </div>
    
    <div class="col-md-6 col-lg-3">
        <div class="card text-center">
            <div class="card-body">
                <div class="text-warning mb-3">
                    <i class="fas fa-exclamation-triangle fa-2x"></i>
                </div>
                <h5 class="card-title">Low Stock</h5>
                <h3 class="text-warning">7</h3>
                <small class="text-muted">Items need reorder</small>
            </div>
        </div>
    </div>
    
    <div class="col-md-6 col-lg-3">
        <div class="card text-center">
            <div class="card-body">
                <div class="text-info mb-3">
                    <i class="fas fa-boxes fa-2x"></i>
                </div>
                <h5 class="card-title">Inventory</h5>
                <h3 class="text-info">1,245</h3>
                <small class="text-muted">Total items</small>
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
                    Prescription Queue
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Rx ID</th>
                                <th>Patient</th>
                                <th>Medication</th>
                                <th>Doctor</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>RX001</td>
                                <td>Sarah Wilson</td>
                                <td>Amoxicillin 500mg</td>
                                <td>Dr. Smith</td>
                                <td><span class="badge bg-warning">Processing</span></td>
                            </tr>
                            <tr>
                                <td>RX002</td>
                                <td>John Doe</td>
                                <td>Lisinopril 10mg</td>
                                <td>Dr. Johnson</td>
                                <td><span class="badge bg-secondary">Pending</span></td>
                            </tr>
                            <tr>
                                <td>RX003</td>
                                <td>Mary Brown</td>
                                <td>Metformin 850mg</td>
                                <td>Dr. Davis</td>
                                <td><span class="badge bg-success">Ready</span></td>
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
                    <i class="fas fa-exclamation-circle me-2"></i>
                    Inventory Alerts
                </h5>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="flex-shrink-0">
                        <div class="bg-danger rounded-circle p-2">
                            <i class="fas fa-pills text-white small"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="mb-1">Aspirin 325mg</h6>
                        <p class="text-muted small mb-0">Stock: 15 units (Critical)</p>
                    </div>
                </div>
                
                <div class="d-flex align-items-center mb-3">
                    <div class="flex-shrink-0">
                        <div class="bg-warning rounded-circle p-2">
                            <i class="fas fa-capsules text-white small"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="mb-1">Ibuprofen 400mg</h6>
                        <p class="text-muted small mb-0">Stock: 45 units (Low)</p>
                    </div>
                </div>
                
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="bg-info rounded-circle p-2">
                            <i class="fas fa-prescription-bottle text-white small"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="mb-1">Insulin Pens</h6>
                        <p class="text-muted small mb-0">Expiring in 30 days</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
