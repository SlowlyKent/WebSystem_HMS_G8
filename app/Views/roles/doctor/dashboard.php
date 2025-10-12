<?= $this->extend('layouts/template') ?>

<?= $this->section('content') ?>
<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-success text-white">
                    <h1 class="card-title mb-0">Doctor Dashboard</h1>
                </div>
                <div class="card-body">
                    <p class="lead">Welcome, <strong><?= esc($user['name']) ?></strong>!</p>
                    
                    <div class="row g-4 mb-4">
                        <div class="col-md-6">
                            <div class="card border-info">
                                <div class="card-body text-center">
                                    <h5 class="card-title text-info">Today's Appointments</h5>
                                    <h2 class="display-4 text-info">—</h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card border-warning">
                                <div class="card-body text-center">
                                    <h5 class="card-title text-warning">Pending Reports</h5>
                                    <h2 class="display-4 text-warning">—</h2>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Recent Patient Activity</h5>
                        </div>
                        <div class="card-body">
                            <p class="text-muted">No recent patient activity to show.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
