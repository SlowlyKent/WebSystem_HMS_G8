    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark" style="background-color: #2563EB;">
        <div class="container">
            <a class="navbar-brand fw-bold" href="<?= base_url(''); ?>">
                <?php if (session()->get('isLoggedIn')): ?>
                    Welcome, <?= session()->get('name') ?>
                <?php else: ?>
                    Learning Management System
                <?php endif; ?>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <?php if (session()->get('isLoggedIn')): ?>
                        <?php $role = strtolower(session('role') ?? ''); ?>
                        <?php if ($role === 'admin'): ?>
                            <li><a class="nav-link" href="<?= base_url('dashboard'); ?>">Admin Dashboard</a></li>
                            <li><a class="nav-link" href="<?= base_url('admin/users'); ?>">Manage Users</a></li>
                            <li><a class="nav-link" href="<?= base_url('admin/settings'); ?>">Settings</a></li>
                        <?php elseif ($role === 'teacher'): ?>
                            <li><a class="nav-link" href="<?= base_url('dashboard'); ?>">Teacher Dashboard</a></li>
                            <li><a class="nav-link" href="<?= base_url('teacher/courses'); ?>">My Courses</a></li>
                            <li><a class="nav-link" href="<?= base_url('teacher/grades'); ?>">Grades</a></li>
                        <?php elseif ($role === 'student'): ?>
                            <li><a class="nav-link" href="<?= base_url('dashboard'); ?>">Student Dashboard</a></li>
                            <li><a class="nav-link" href="<?= base_url('student/enrollments'); ?>">My Enrollments</a></li>
                            <li><a class="nav-link" href="<?= base_url('student/assignments'); ?>">Assignments</a></li>
                        <?php else: ?>
                            <li><a class="nav-link" href="<?= base_url('dashboard'); ?>">Dashboard</a></li>
                        <?php endif; ?>
                        <li>
                            <a class="nav-link" href="<?= base_url('logout'); ?>">Logout</a>
                        </li>
                    <?php else: ?>
                        <li><a class="nav-link" href="<?= base_url(''); ?>">Home</a> </li>
                        <li><a class="nav-link" href="<?= base_url('about'); ?>">About</a></li>
                        <li><a class="nav-link" href="<?= base_url('contact'); ?>">Contact</a></li>
                        <li> <a class="nav-link" href="<?= base_url('login'); ?>">Login</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>


