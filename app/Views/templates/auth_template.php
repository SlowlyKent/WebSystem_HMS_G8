<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Page title -->
    <title>Login - St.Peter Hospital Management System</title> 
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">   
    <link rel="stylesheet" href="<?= base_url('public/assets/css/style.css') ?>?v=<?= filemtime(FCPATH . 'assets/css/style.css') ?>">
</head>
<body class="auth-page">
    <!-- Main content area - CodeIgniter will inject page content here -->
    <main>
        <?= $this->renderSection('content') ?>
    </main>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
