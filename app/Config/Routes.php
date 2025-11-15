<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

 $routes->get('/', 'Home::index');
 $routes->get('home', 'Home::index');
 $routes->get('about', 'Home::about');
 $routes->get('contact', 'Home::contact');
 
 // Authentication Routes
 $routes->get('register', 'Auth::register');
 $routes->post('register', 'Auth::register');
 $routes->get('login', 'Auth::login');
 $routes->post('login', 'Auth::login');
 $routes->get('logout', 'Auth::logout');
 
 // Dashboards Routes
 $routes->get('dashboard', 'Auth::dashboard');

 // Scheduling (Admin/Doctor)
$routes->get('scheduling', 'Admin\Scheduling::index');
$routes->post('scheduling', 'Admin\Scheduling::store');

 // Appointments (Admin/Receptionist)
 $routes->get('appointments', 'Admin\Appointments::index');
 $routes->post('appointments', 'Admin\Appointments::store');
 $routes->post('appointments/(:num)/status', 'Admin\Appointments::updateStatus/$1');
 // Singular alias
 $routes->get('appointment', 'Admin\\Appointments::index');
 $routes->post('appointment', 'Admin\\Appointments::store');

 // Laboratory Routes (Admin/Doctor/Lab Staff)
 $routes->get('laboratory', 'Admin\Laboratory::index');
 $routes->post('laboratory/request', 'Admin\Laboratory::createRequest');
 $routes->post('laboratory/sample/(:num)', 'Admin\Laboratory::collectSample/$1');
 $routes->get('laboratory/results/(:num)', 'Admin\Laboratory::viewResults/$1');
 $routes->post('laboratory/results/(:num)', 'Admin\Laboratory::enterResults/$1');
 $routes->post('laboratory/verify/(:num)', 'Admin\Laboratory::verifyResults/$1');

 // Admin Routes
 $routes->group('admin', static function ($routes) {
     // Patient routes
     $routes->get('patients/registration', 'Admin\Patients::registration');
     $routes->post('patients/registration', 'Admin\Patients::store');
     $routes->get('patients/view/(:num)', 'Admin\Patients::view/$1');
     $routes->get('patients/edit/(:num)', 'Admin\Patients::edit/$1');
     $routes->post('patients/update/(:num)', 'Admin\Patients::update/$1');
     
     // Billing routes
     $routes->get('billing', 'Admin\Billing::index');
     $routes->post('billing', 'Admin\Billing::store');
     $routes->get('billing/create', 'Admin\Billing::create');
     $routes->get('billing/(:num)', 'Admin\Billing::view/$1');
     $routes->post('billing/(:num)/pay', 'Admin\Billing::pay/$1');
     $routes->post('billing/pay', 'Admin\Billing::quickPay');
     $routes->post('billing/(:num)/status/(:segment)', 'Admin\Billing::updateStatus/$1/$2');
     $routes->get('billing/(:num)/receipt', 'Admin\Billing::generateReceipt/$1');
     
     // Admissions routes
     $routes->get('admissions', 'Admin\Admissions::index');
     $routes->post('admissions/admit', 'Admin\Admissions::admit');
     $routes->post('admissions/discharge/(:num)', 'Admin\Admissions::discharge/$1');
     
     // Pharmacy routes
     $routes->get('pharmacy', 'Admin\Pharmacy::index');
     $routes->post('pharmacy/prescription', 'Admin\Pharmacy::createPrescription');
     $routes->post('pharmacy/dispense/(:num)', 'Admin\Pharmacy::dispense/$1');
 });