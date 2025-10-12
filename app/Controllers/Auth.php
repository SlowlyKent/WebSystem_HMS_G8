<?php
namespace App\Controllers;

use App\Models\UserModels;
use CodeIgniter\Controller;

class Auth extends Controller
{
    public function register()
    {
        helper(['form']);
        $session = session();
        $model = new UserModels();
        
        if ($this->request->getMethod() === 'POST') {
            $rules = [
                'name' => 'required|min_length[3]|max_length[100]',
                'email' => 'required|valid_email|is_unique[users.email]',
                'password' => 'required|min_length[6]',
                'password_confirm' => 'matches[password]'
            ];
            
            if ($this->validate($rules)) {
                $data = [
                    'name' => $this->request->getPost('name'),
                    'email' => $this->request->getPost('email'),
                    'password' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
                    'role' => 'student'
                ];
                
                // Save user to database
                if ($model->insert($data)) {
                    $session->setFlashdata('success', 'Registration successful. Please login.');
                    return redirect()->to('/login');
                } else {
                    // Get the last error for debugging
                    $errors = $model->errors();
                    $errorMessage = 'Registration failed. ';
                    if (!empty($errors)) {
                        $errorMessage .= implode(', ', $errors);
                    } else {
                        $errorMessage .= 'Please try again.';
                    }
                    $session->setFlashdata('error', $errorMessage);
                }
            }
        }
        
        echo view('auth/register', [
            'validation' => $this->validator
        ]);
    }

    public function login()
    {
        // Redirect if already logged in
        if (session()->get('isLoggedIn')) {
            return redirect()->to('/dashboard');
        }
        
        helper(['form']);
        $session = session();
        $model = new UserModels();

        if ($this->request->getMethod() === 'POST') {
            $rules = [
                'email' => 'required|valid_email',
                'password' => 'required'
            ];
            if ($this->validate($rules)) {
                $email = $this->request->getPost('email');
                $password = $this->request->getPost('password');
                $user = $model->where('email', $email)->first();
                // Session is Saving the user data
                if ($user && password_verify($password, $user['password'])) {
                    // Create full name from first_name and last_name
                    $fullName = trim(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? ''));
                    if (empty($fullName)) {
                        $fullName = $user['username'] ?? $user['email'];
                    }
                    
                    $session->set([
                        'user_id'    => $user['id'],
                        'username'   => $user['username'],
                        'email'      => $user['email'],
                        'name'       => $fullName,
                        'first_name' => $user['first_name'],
                        'last_name'  => $user['last_name'],
                        'role_id'    => $user['role_id'],
                        'role'       => $this->getRoleNameById($user['role_id']),
                        'isLoggedIn' => true
                    ]);
                    $session->setFlashdata('success', 'Welcome, ' . $fullName . '!');
                    
                    // Redirect everyone to unified dashboard (as per teacher requirements)
                    return redirect()->to(base_url('dashboard'));
                } else {
                    $session->setFlashdata('error', 'Invalid login credentials.');
                }
            }
        }
        echo view('auth/login', [
            'validation' => $this->validator
        ]);
    }

    public function logout()
    {
        // Destroy the current session
        session()->destroy();
        
        // Set logout message and redirect
        session()->setFlashdata('success', 'You have been logged out successfully.');
        return redirect()->to(base_url('login'));
    }

    public function dashboard()
    {
        // Check if user is logged in
        if (!session()->get('isLoggedIn')) {
            session()->setFlashdata('error', 'Please login to access the dashboard.');
            return redirect()->to(base_url('login'));
        }

        // User is logged in, show dashboard
        $data = [
            'user' => [
                'name' => session()->get('name'),
                'email' => session()->get('email'),
                'role' => session()->get('role')
            ]
        ];

        return view('auth/dashboard', $data);
    }

    /**
     * Convert role_id to role name
     */
    private function getRoleNameById($roleId)
    {
        $roles = [
            1 => 'admin',
            2 => 'doctor', 
            3 => 'nurse',
            4 => 'it_staff',
            5 => 'teacher',
            6 => 'student'
        ];
        
        return $roles[$roleId] ?? 'nurse';
    }
}
