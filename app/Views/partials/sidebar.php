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
        $role = session()->get('role');
        $menuItems = [
            'dashboard' => [
                'url' => 'dashboard',
                'icon' => 'tachometer-alt',
                'text' => 'Dashboard',
                'roles' => ['admin', 'doctor', 'nurse', 'receptionist', 'lab_staff', 'pharmacist', 'accountant', 'it_staff']
            ],
            'patients' => [
                'url' => 'admin/patients/registration',
                'icon' => 'user-plus',
                'text' => 'Patient Registration & EHR',
                'roles' => ['admin', 'doctor', 'nurse', 'receptionist']
            ],
            'scheduling' => [
                'url' => 'scheduling',
                'icon' => 'calendar-alt',
                'text' => 'Scheduling',
                'roles' => ['admin', 'receptionist', 'doctor']
            ],
            'appointments' => [
                'url' => 'appointments',
                'icon' => 'calendar-check',
                'text' => 'Appointments',
                'roles' => ['admin', 'receptionist', 'doctor']
            ],
            'billing' => [
                'url' => 'admin/billing',
                'icon' => 'file-invoice-dollar',
                'text' => 'Billing & Payment Processing',
                'roles' => ['admin', 'accountant']
            ],
            'laboratory' => [
                'url' => 'laboratory',
                'icon' => 'flask',
                'text' => 'Laboratory & Diagnostic',
                'roles' => ['admin', 'doctor', 'lab_staff']
            ],
            'pharmacy' => [
                'url' => 'pharmacy',
                'icon' => 'pills',
                'text' => 'Pharmacy Management',
                'roles' => ['admin', 'pharmacist']
            ],
            'database' => [
                'url' => 'database',
                'icon' => 'database',
                'text' => 'Database',
                'roles' => ['admin', 'it_staff']
            ],
            'reports' => [
                'url' => 'reports',
                'icon' => 'chart-bar',
                'text' => 'Reports & Analytics',
                'roles' => ['admin', 'accountant']
            ],
            'security' => [
                'url' => 'security',
                'icon' => 'shield-alt',
                'text' => 'User Access & Security',
                'roles' => ['admin', 'it_staff']
            ]
        ];
        ?>
        
        <nav class="nav flex-column">
            <?php
            // Loop through each menu item
            foreach ($menuItems as $key => $item) {
                // Check if current user's role has access to this menu item
                $hasAccess = in_array($role, $item['roles']);
                if (!$hasAccess) {
                    continue; // Skip if no access
                }

                // Check if this is the current active page
                $isCurrentPage = strpos($currentUrl, $item['url']) !== false;
                $isHomeDashboard = uri_string() === '' && $key === 'dashboard';
                $isActive = $isCurrentPage || $isHomeDashboard;
                
                // Set active class if this is the current page
                $activeClass = $isActive ? 'active' : '';
                ?>
                <a class="nav-link <?= $activeClass ?>" 
                href="<?= base_url($item['url']) ?>">
                    <i class="fas fa-<?= $item['icon'] ?>"></i>
                    <span><?= $item['text'] ?></span>
                </a>
            <?php } // End of foreach loop ?>
        </nav>
    </div>
</div>
