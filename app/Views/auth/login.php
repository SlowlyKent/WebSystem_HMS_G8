<?= $this->extend('layouts/auth') ?>

<?= $this->section('content') ?>
<div class="auth-container">
    <div class="auth-wrapper">
        <!-- Left Side - Healthcare Information -->
        <div class="auth-left">
            <!-- Brand Header -->
            <div class="brand-header">
                <div class="brand-icon">
                    <i class="fas fa-hospital-alt" style="color: white; font-size: 14px;"></i>
                </div>
                <span class="brand-text">St. Peter</span>
            </div>

            <!-- Main Content -->
            <h1 class="main-title">
                Healthcare<br>
                <span class="highlight">Management</span>
            </h1>
            <p class="subtitle">
                Streamlined medical administration for modern<br>
                healthcare professionals
            </p>

            <!-- Feature Cards -->
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-user-md"></i>
                    </div>
                    <h3 class="feature-title">Patient Care</h3>
                    <p class="feature-desc">Comprehensive records</p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <h3 class="feature-title">Scheduling</h3>
                    <p class="feature-desc">Smart appointments</p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-chart-bar"></i>
                    </div>
                    <h3 class="feature-title">Analytics</h3>
                    <p class="feature-desc">Data insights</p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h3 class="feature-title">Security</h3>
                    <p class="feature-desc">HIPAA compliant</p>
                </div>
            </div>
        </div>

        <!-- Right Side - Login Form -->
        <div class="auth-right">
            <div class="login-header">
                <div class="login-icon">
                    <i class="fas fa-sign-in-alt"></i>
                </div>
                <h2 class="login-title">Welcome Back</h2>
                <p class="login-subtitle">Please sign in to continue</p>
            </div>

            <!-- Display any flash messages -->
            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger mb-3">
                    <?= session()->getFlashdata('error') ?>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success mb-3">
                    <?= session()->getFlashdata('success') ?>
                </div>
            <?php endif; ?>

            <form action="<?= base_url('login') ?>" method="post">
                <div class="form-group">
                    <label for="email" class="form-label">Email Address</label>
                    <input type="email" 
                           id="email" 
                           name="email" 
                           class="form-control" 
                           placeholder="doctor@stpeter.com"
                           value="<?= old('email') ?>"
                           required>
                </div>

                <div class="form-group">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" 
                           id="password" 
                           name="password" 
                           class="form-control" 
                           placeholder="Enter your password"
                           required>
                </div>

                <div class="form-options">
                    <div class="form-check">
                        <input type="checkbox" 
                               id="remember" 
                               name="remember" 
                               class="form-check-input">
                        <label for="remember" class="form-check-label">Remember me</label>
                    </div>
                    
                </div>

                <button type="submit" class="btn-signin">
                    <i class="fas fa-arrow-right me-2"></i>Sign In
                </button>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
