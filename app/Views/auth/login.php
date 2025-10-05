<?= $this->extend('templates/auth_template') ?>

<?= $this->section('content') ?>
<!-- Main login container -->
<div class="container-fluid vh-100 d-flex align-items-center justify-content-center">
    <div class="row w-100 justify-content-center">
        <div class="col-12 col-sm-8 col-md-6 col-lg-4 col-xl-4">
            <!-- Login form -->
            <div class="card login-form shadow-lg border-0">
                <div class="card-body p-4">
                    <div class="text-center mb-4">
                        <h1 class="logo mb-2">St.Peter Hospital</h1>
                        <p class="welcome-text text-muted">Welcome back</p>
                    </div>
                    
                    <!-- Login form - submits to auth/login -->
                    <form action="<?= base_url('auth/login') ?>" method="post">
                        <!-- Email input field -->
                        <div class="mb-3">
                            <label for="email" class="form-label fw-medium">Email Address</label>
                            <input type="email" 
                                   id="email" 
                                   name="email" 
                                   class="form-control form-input" 
                                   placeholder="Enter your email" 
                                   required>
                        </div>
                        
                        <!-- Password input field -->
                        <div class="mb-3">
                            <label for="password" class="form-label fw-medium">Password</label>
                            <input type="password" 
                                   id="password" 
                                   name="password" 
                                   class="form-control form-input" 
                                   placeholder="Enter your password" 
                                   required>
                        </div>
                        

                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <!-- Remember me checkbox -->
                            <div class="form-check">
                                <input type="checkbox" 
                                       id="remember" 
                                       name="remember" 
                                       class="form-check-input checkbox">
                                <label for="remember" class="form-check-label checkbox-label">Remember me</label>
                            </div>
                            <!-- Forgot password link -->
                            <a href="#" class="forgot-password text-decoration-none">Forgot password?</a>
                        </div>
                        
                        <button type="submit" class="btn signin-btn w-100 mb-3">Sign In</button>
                        
                        <div class="text-center signup-link">
                            <span class="text-muted">Don't have an account? </span>
                            <!-- Sign up link -->
                            <a href="#" class="signup-text text-decoration-none">Sign up</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
