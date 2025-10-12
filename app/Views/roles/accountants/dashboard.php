<?= $this->extend('layouts/template') ?>

<?= $this->section('content') ?>
<div class="dashboard-welcome">
    <div class="d-flex align-items-center">
        <i class="fas fa-file-invoice-dollar fa-2x me-3"></i>
        <div>
            <h2 class="mb-1">Accountant Dashboard</h2>
            <p class="mb-0 opacity-75">Welcome, <strong><?= esc($user['name'] ?? 'Accountant') ?></strong>!</p>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-md-6 col-lg-3">
        <div class="card text-center">
            <div class="card-body">
                <div class="text-success mb-3">
                    <i class="fas fa-chart-line fa-2x"></i>
                </div>
                <h5 class="card-title">Revenue Today</h5>
                <h3 class="text-success">₱0</h3>
                <small class="text-muted">Collected payments</small>
            </div>
        </div>
    </div>
    
    <div class="col-md-6 col-lg-3">
        <div class="card text-center">
            <div class="card-body">
                <div class="text-warning mb-3">
                    <i class="fas fa-file-invoice fa-2x"></i>
                </div>
                <h5 class="card-title">Pending Invoices</h5>
                <h3 class="text-warning">0</h3>
                <small class="text-muted">Awaiting payment</small>
            </div>
        </div>
    </div>
    
    <div class="col-md-6 col-lg-3">
        <div class="card text-center">
            <div class="card-body">
                <div class="text-primary mb-3">
                    <i class="fas fa-cash-register fa-2x"></i>
                </div>
                <h5 class="card-title">Transactions</h5>
                <h3 class="text-primary">0</h3>
                <small class="text-muted">Processed today</small>
            </div>
        </div>
    </div>
    
    <div class="col-md-6 col-lg-3">
        <div class="card text-center">
            <div class="card-body">
                <div class="text-danger mb-3">
                    <i class="fas fa-exclamation-triangle fa-2x"></i>
                </div>
                <h5 class="card-title">Disputes</h5>
                <h3 class="text-danger">0</h3>
                <small class="text-muted">Open cases</small>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-receipt me-2"></i>
                    Recent Transactions
                </h5>
            </div>
            <div class="card-body">
                <p class="text-muted">No recent transactions to show.</p>
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
                    <button class="btn btn-outline-success">
                        <i class="fas fa-file-invoice-dollar me-2"></i>
                        Generate Invoice
                    </button>
                    <button class="btn btn-outline-primary">
                        <i class="fas fa-upload me-2"></i>
                        Post Payment
                    </button>
                    <button class="btn btn-outline-warning">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        Review Disputes
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
