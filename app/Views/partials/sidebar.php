<!-- Sidebar -->
<div class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <div class="d-flex align-items-center">
            <div class="sidebar-brand">
                <i class="fas fa-hospital-alt text-success me-2"></i>
                <span class="brand-text">St. Peter HMS</span>
            </div>
        </div>
    </div>
    
    <div class="sidebar-menu">
        <nav class="nav flex-column">
            <a class="nav-link active" href="<?= base_url('dashboard') ?>">
                <i class="fas fa-tachometer-alt"></i>
                <span>Dashboard</span>
            </a>
            
            <a class="nav-link" href="<?= base_url('patient-registration') ?>">
                <i class="fas fa-user-plus"></i>
                <span>Patient Registration & EHR</span>
            </a>
            
            <a class="nav-link" href="<?= base_url('scheduling') ?>">
                <i class="fas fa-calendar-alt"></i>
                <span>Scheduling</span>
            </a>
            
            <a class="nav-link" href="<?= base_url('billing') ?>">
                <i class="fas fa-file-invoice-dollar"></i>
                <span>Billing & Payment Processing</span>
            </a>
            
            <a class="nav-link" href="<?= base_url('laboratory') ?>">
                <i class="fas fa-flask"></i>
                <span>Laboratory & Diagnostic Management</span>
            </a>
            
            <a class="nav-link" href="<?= base_url('pharmacy') ?>">
                <i class="fas fa-pills"></i>
                <span>Pharmacy & Inventory Control</span>
            </a>
            
            <a class="nav-link" href="<?= base_url('database') ?>">
                <i class="fas fa-database"></i>
                <span>Centralized Database</span>
            </a>
            
            <a class="nav-link" href="<?= base_url('reports') ?>">
                <i class="fas fa-chart-bar"></i>
                <span>Reports & Analytics Dashboard</span>
            </a>
            
            <a class="nav-link" href="<?= base_url('security') ?>">
                <i class="fas fa-shield-alt"></i>
                <span>Role-Based User Access & Data Security</span>
            </a>
        </nav>
    </div>
</div>
