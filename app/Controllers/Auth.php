<?php

namespace App\Controllers;

use App\Models\UserModels;
use CodeIgniter\Controller;

class Auth extends Controller
{
    public function login()
    {
        // Redirect kung naka-login na
        if (session()->get('isLoggedIn')) {
            return redirect()->to('/dashboard');
        }

        helper(['form']);
        $session = session();
        $model   = new UserModels();

        if ($this->request->getMethod() === 'post') {
            $rules = [
                'email'    => 'required|valid_email',
                'password' => 'required'
            ];

            if ($this->validate($rules)) {
                $email    = $this->request->getPost('email');
                $password = $this->request->getPost('password');

                // hanapin user sa DB
                $user = $model->where('email', $email)->first();

                if ($user) {
                    // check status muna
                    if ($user['status'] !== 'active') {
                        $session->setFlashdata('error', 'Your account is ' . $user['status'] . '. Please contact admin.');
                        return redirect()->back()->withInput();
                    }

                    // check password
                    if (password_verify($password, $user['password'])) {
                        $session->set([
                            'user_id'    => $user['id'],
                            'username'   => $user['username'],
                            'email'      => $user['email'],
                            'first_name' => $user['first_name'],
                            'last_name'  => $user['last_name'],
                            'role_id'    => $user['role_id'],
                            'isLoggedIn' => true
                        ]);

                        return redirect()->to(base_url('dashboard'))
                            ->with('success', 'Welcome back, ' . $user['first_name'] . '!');
                    }
                }

                $session->setFlashdata('error', 'Invalid email or password.');
            }
        }

        return view('auth/login', [
            'validation' => $this->validator ?? null
        ]);
    }
}