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
        <?php 
        $currentUrl = current_url();
        $menuItems = [
            'dashboard' => [
                'url' => 'dashboard',
                'icon' => 'tachometer-alt',
                'text' => 'Dashboard'
            ],
            'patients' => [
                'url' => 'admin/patients/registration',
                'icon' => 'user-plus',
                'text' => 'Patient Registration & EHR'
            ],
            'scheduling' => [
                'url' => 'scheduling',
                'icon' => 'calendar-alt',
                'text' => 'Scheduling'
            ],
            'billing' => [
                'url' => 'billing',
                'icon' => 'file-invoice-dollar',
                'text' => 'Billing & Payment Processing'
            ],
            'laboratory' => [
                'url' => 'laboratory',
                'icon' => 'flask',
                'text' => 'Laboratory & Diagnostic Management'
            ],
            'pharmacy' => [
                'url' => 'pharmacy',
                'icon' => 'pills',
                'text' => 'Pharmacy & Inventory Control'
            ],
            'database' => [
                'url' => 'database',
                'icon' => 'database',
                'text' => 'Centralized Database'
            ],
            'reports' => [
                'url' => 'reports',
                'icon' => 'chart-bar',
                'text' => 'Reports & Analytics Dashboard'
            ],
            'security' => [
                'url' => 'security',
                'icon' => 'shield-alt',
                'text' => 'Role-Based User Access & Data Security'
            ]
        ];
        ?>
        
        <nav class="nav flex-column">
            <?php foreach ($menuItems as $key => $item): ?>
                <?php 
                $isActive = (strpos($currentUrl, $item['url']) !== false) || 
                           (uri_string() === '' && $key === 'dashboard');
                ?>
                <a class="nav-link <?= $isActive ? 'active' : '' ?>" href="<?= base_url($item['url']) ?>">
                    <i class="fas fa-<?= $item['icon'] ?>"></i>
                    <span><?= $item['text'] ?></span>
                </a>
            <?php endforeach; ?>
        </nav>
    </div>
</div>
