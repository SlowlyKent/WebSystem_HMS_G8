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

 // Admin Routes
 $routes->group('admin', static function ($routes) {
     $routes->get('patients/registration', 'Admin\Patients::registration');
     $routes->post('patients/registration', 'Admin\Patients::store');
 });