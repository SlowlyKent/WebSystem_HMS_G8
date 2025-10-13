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
                        <li>
                            <a class="nav-link btn btn-outline-light btn-sm ms-2 text-dark" 
                                href="<?= base_url('logout'); ?>"
                                style="transition: all 0.3s;">
                                    <i class="fas fa-sign-out-alt me-1"></i> Logout
                            </a>
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


