<?php

declare(strict_types=1);

use ErpStocco\Presentation\Controllers;

/** @var Router $router */

// Auth
$router->post('/api/auth/login', [Controllers\AuthController::class, 'login']);
$router->post('/api/auth/register', [Controllers\AuthController::class, 'register']);
$router->get('/api/auth/me', [Controllers\AuthController::class, 'me']);

// Dashboard
$router->get('/api/dashboard', [Controllers\DashboardController::class, 'index']);

// Products
$router->get('/api/products', [Controllers\ProductController::class, 'index']);
$router->get('/api/products/low-stock', [Controllers\ProductController::class, 'lowStock']);
$router->get('/api/products/{id}', [Controllers\ProductController::class, 'show']);
$router->post('/api/products', [Controllers\ProductController::class, 'store']);
$router->put('/api/products/{id}', [Controllers\ProductController::class, 'update']);
$router->delete('/api/products/{id}', [Controllers\ProductController::class, 'destroy']);

// Categories
$router->get('/api/categories', [Controllers\CategoryController::class, 'index']);
$router->get('/api/categories/{id}', [Controllers\CategoryController::class, 'show']);
$router->post('/api/categories', [Controllers\CategoryController::class, 'store']);
$router->put('/api/categories/{id}', [Controllers\CategoryController::class, 'update']);
$router->delete('/api/categories/{id}', [Controllers\CategoryController::class, 'destroy']);

// Customers
$router->get('/api/customers', [Controllers\CustomerController::class, 'index']);
$router->get('/api/customers/{id}', [Controllers\CustomerController::class, 'show']);
$router->post('/api/customers', [Controllers\CustomerController::class, 'store']);
$router->put('/api/customers/{id}', [Controllers\CustomerController::class, 'update']);
$router->delete('/api/customers/{id}', [Controllers\CustomerController::class, 'destroy']);

// Suppliers
$router->get('/api/suppliers', [Controllers\SupplierController::class, 'index']);
$router->get('/api/suppliers/{id}', [Controllers\SupplierController::class, 'show']);
$router->post('/api/suppliers', [Controllers\SupplierController::class, 'store']);
$router->put('/api/suppliers/{id}', [Controllers\SupplierController::class, 'update']);
$router->delete('/api/suppliers/{id}', [Controllers\SupplierController::class, 'destroy']);

// Sales
$router->get('/api/sales', [Controllers\SaleController::class, 'index']);
$router->get('/api/sales/{id}', [Controllers\SaleController::class, 'show']);
$router->post('/api/sales', [Controllers\SaleController::class, 'store']);
$router->put('/api/sales/{id}', [Controllers\SaleController::class, 'update']);
$router->delete('/api/sales/{id}', [Controllers\SaleController::class, 'destroy']);

// Purchases
$router->get('/api/purchases', [Controllers\PurchaseController::class, 'index']);
$router->get('/api/purchases/{id}', [Controllers\PurchaseController::class, 'show']);
$router->post('/api/purchases', [Controllers\PurchaseController::class, 'store']);
$router->put('/api/purchases/{id}', [Controllers\PurchaseController::class, 'update']);
$router->delete('/api/purchases/{id}', [Controllers\PurchaseController::class, 'destroy']);

// Financial Accounts
$router->get('/api/accounts', [Controllers\AccountController::class, 'index']);
$router->get('/api/accounts/balance', [Controllers\AccountController::class, 'balance']);
$router->get('/api/accounts/{id}', [Controllers\AccountController::class, 'show']);
$router->post('/api/accounts', [Controllers\AccountController::class, 'store']);
$router->put('/api/accounts/{id}', [Controllers\AccountController::class, 'update']);
$router->delete('/api/accounts/{id}', [Controllers\AccountController::class, 'destroy']);

// Transactions
$router->get('/api/transactions', [Controllers\TransactionController::class, 'index']);
$router->get('/api/transactions/report', [Controllers\TransactionController::class, 'report']);
$router->get('/api/transactions/{id}', [Controllers\TransactionController::class, 'show']);
$router->post('/api/transactions', [Controllers\TransactionController::class, 'store']);
$router->put('/api/transactions/{id}', [Controllers\TransactionController::class, 'update']);
$router->delete('/api/transactions/{id}', [Controllers\TransactionController::class, 'destroy']);

// Employees
$router->get('/api/employees', [Controllers\EmployeeController::class, 'index']);
$router->get('/api/employees/{id}', [Controllers\EmployeeController::class, 'show']);
$router->post('/api/employees', [Controllers\EmployeeController::class, 'store']);
$router->put('/api/employees/{id}', [Controllers\EmployeeController::class, 'update']);
$router->delete('/api/employees/{id}', [Controllers\EmployeeController::class, 'destroy']);

// Attendance
$router->get('/api/attendance', [Controllers\AttendanceController::class, 'index']);
$router->get('/api/attendance/report/{employeeId}', [Controllers\AttendanceController::class, 'report']);
$router->get('/api/attendance/{id}', [Controllers\AttendanceController::class, 'show']);
$router->post('/api/attendance', [Controllers\AttendanceController::class, 'store']);
$router->put('/api/attendance/{id}', [Controllers\AttendanceController::class, 'update']);
$router->delete('/api/attendance/{id}', [Controllers\AttendanceController::class, 'destroy']);

// Payroll
$router->get('/api/payroll', [Controllers\PayrollController::class, 'index']);
$router->get('/api/payroll/by-period', [Controllers\PayrollController::class, 'byPeriod']);
$router->get('/api/payroll/{id}', [Controllers\PayrollController::class, 'show']);
$router->post('/api/payroll', [Controllers\PayrollController::class, 'store']);
$router->put('/api/payroll/{id}', [Controllers\PayrollController::class, 'update']);
$router->delete('/api/payroll/{id}', [Controllers\PayrollController::class, 'destroy']);
