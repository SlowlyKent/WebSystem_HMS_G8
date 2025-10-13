<?= $this->extend('layouts/template') ?>

<?= $this->section('content') ?>
<div class="container mt-4">
    <?php if(session()->getFlashdata('success')): ?>
        <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
    <?php endif; ?>
    
    <!-- Welcome Message -->


    <?php
      // Wrapper: load role-specific partials
      $role = $user['role'] ?? session('role');

      switch ($role) {
        case 'admin':
          echo view('roles/admin/dashboard', ['user' => $user]);
          break;
        case 'doctor':
          echo view('roles/doctor/dashboard', ['user' => $user]);
          break;
        case 'nurse':
          echo view('roles/nurse/dashboard', ['user' => $user]);
          break;
        case 'it_staff':
          echo view('roles/it_staff/dashboard', ['user' => $user]);
          break;
        case 'receptionist':
          echo view('roles/receptionists/dashboard', ['user' => $user]);
          break;
        case 'accountant':
          echo view('roles/accountants/dashboard', ['user' => $user]);
          break;
        case 'lab_staff':
          echo view('roles/lab_staff/dashboard', ['user' => $user]);
          break;
        case 'pharmacist':
          echo view('roles/pharmacists/dashboard', ['user' => $user]);
          break;
        default:
          // Redirect to login if role is unknown
          return redirect()->to('/auth/login')->with('error', 'Unauthorized access. Please login with valid credentials.');
      }
    ?>
</div>
<?= $this->endSection() ?>