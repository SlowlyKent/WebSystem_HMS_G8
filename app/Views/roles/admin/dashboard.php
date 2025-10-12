<?= $this->extend('layouts/template') ?>

<?= $this->section('content') ?>
<!-- Dashboard Header -->
<div class="card mb-4" style="background: linear-gradient(135deg, #4285f4 0%, #5a9fd4 100%); border: none;">
    <div class="card-body text-white py-4">
        <h2 class="mb-1 fw-bold">Admin Dashboard</h2>
        <p class="mb-0 opacity-90">Welcome, System Administrator!</p>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row g-4 mb-4">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h6 class="text-muted mb-2">Total Users</h6>
                <h2 class="fw-bold mb-0">1,247</h2>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h6 class="text-muted mb-2">Total Courses</h6>
                <h2 class="fw-bold mb-0">89</h2>
            </div>
        </div>
    </div>
</div>

<!-- Recent Activity Card -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-0 py-3">
        <h5 class="mb-0 fw-semibold">Recent Activity</h5>
    </div>
    <div class="card-body">
        <div class="d-flex align-items-center p-3 bg-light bg-opacity-50 rounded mb-3">
            <div class="bg-success bg-opacity-10 p-2 rounded me-3">
                <i class="fas fa-check text-success"></i>
            </div>
            <div class="flex-grow-1">
                <p class="mb-1 fw-medium">Welcome, System Administrator!</p>
                <small class="text-muted">Dashboard loaded successfully</small>
            </div>
        </div>
        
        <div class="d-flex align-items-center p-3 bg-light bg-opacity-50 rounded mb-3">
            <div class="bg-info bg-opacity-10 p-2 rounded me-3">
                <i class="fas fa-users text-info"></i>
            </div>
            <div class="flex-grow-1">
                <p class="mb-1 fw-medium">User Management System Active</p>
                <small class="text-muted">All user roles configured properly</small>
            </div>
        </div>
        
        <div class="d-flex align-items-center p-3 bg-light bg-opacity-50 rounded">
            <div class="bg-warning bg-opacity-10 p-2 rounded me-3">
                <i class="fas fa-database text-warning"></i>
            </div>
            <div class="flex-grow-1">
                <p class="mb-1 fw-medium">Database Status: Healthy</p>
                <small class="text-muted">All connections stable</small>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
