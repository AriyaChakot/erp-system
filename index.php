<?php
session_start();
require_once __DIR__ . '/config/database.php';

// Simple router
$uri  = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$base = parse_url(BASE_URL, PHP_URL_PATH);
$path = '/' . ltrim(substr($uri, strlen($base)), '/');
$path = rtrim($path, '/') ?: '/';

$method   = $_SERVER['REQUEST_METHOD'];
$segments = array_values(array_filter(explode('/', $path)));

$controller = $segments[0] ?? 'dashboard';
$action     = $segments[1] ?? 'index';
$id         = isset($segments[2]) ? (int)$segments[2] : null;

function loadController(string $name): object {
    $file = __DIR__ . "/app/controllers/{$name}Controller.php";
    if (!file_exists($file)) {
        http_response_code(404);
        die("404 Not Found");
    }
    require_once $file;
    $class = $name . 'Controller';
    return new $class();
}

switch ($controller) {
    // ── Auth ──────────────────────────────────────────────────
    case 'login':
        $c = loadController('Auth');
        $method === 'POST' ? $c->loginPost() : $c->login();
        break;

    case 'signup':
        $c = loadController('Auth');
        $method === 'POST' ? $c->signupPost() : $c->signup();
        break;

    case 'logout':
        loadController('Auth')->logout();
        break;

    case 'pending':
        loadController('Auth')->pending();
        break;

    // ── Dashboard ─────────────────────────────────────────────
    case '':
    case 'dashboard':
        $c = loadController('Dashboard');
        $c->index();
        break;

    // ── ERP modules ───────────────────────────────────────────
    case 'products':
    case 'customers':
    case 'employees':
        $nameMap = ['products' => 'Product', 'customers' => 'Customer', 'employees' => 'Employee'];
        $c = loadController($nameMap[$controller]);
        if ($method === 'POST' && $action === 'store')      { $c->store(); }
        elseif ($method === 'POST' && $action === 'update') { $c->update($id); }
        elseif ($action === 'create')                       { $c->create(); }
        elseif ($action === 'edit')                         { $c->edit($id); }
        elseif ($action === 'delete')                       { $c->delete($id); }
        else                                                { $c->index(); }
        break;

    case 'orders':
        $c = loadController('Order');
        if ($method === 'POST' && $action === 'store')       { $c->store(); }
        elseif ($method === 'POST' && $action === 'status')  { $c->updateStatus($id); }
        elseif ($action === 'create')                        { $c->create(); }
        elseif ($action === 'view')                          { $c->view($id); }
        elseif ($action === 'delete')                        { $c->delete($id); }
        else                                                 { $c->index(); }
        break;

    // ── Admin ─────────────────────────────────────────────────
    case 'admin':
        $c = loadController('Admin');
        if ($action === 'users')                                          { $c->users(); }
        elseif ($action === 'role'        && $method === 'POST')          { $c->updateRole(); }
        elseif ($action === 'delete-user' && $method === 'POST')          { $c->deleteUser(); }
        elseif ($action === 'notifications')                              { $c->notifications(); }
        elseif ($action === 'reply'       && $method === 'POST')          { $c->reply(); }
        else                                                              { $c->index(); }
        break;

    // ── Issue Reports ─────────────────────────────────────────
    case 'report':
        $c = loadController('Report');
        if ($method === 'POST' && $action === 'store') { $c->store(); }
        elseif ($action === 'create')                  { $c->create(); }
        else                                           { $c->index(); }
        break;

    // ── Inventory ─────────────────────────────────────────────
    case 'inventory':
        $c = loadController('Inventory');
        if      ($action === 'warehouses')                                  { $c->warehouses(); }
        elseif  ($action === 'create-warehouse')                            { $c->createWarehouse(); }
        elseif  ($action === 'store-warehouse'  && $method === 'POST')      { $c->storeWarehouse(); }
        elseif  ($action === 'edit-warehouse')                              { $c->editWarehouse($id); }
        elseif  ($action === 'update-warehouse' && $method === 'POST')      { $c->updateWarehouse($id); }
        elseif  ($action === 'stock')                                       { $c->stockItems(); }
        elseif  ($action === 'movements')                                   { $c->movements(); }
        elseif  ($action === 'adjust')                                      { $c->adjustStock(); }
        elseif  ($action === 'store-adjustment' && $method === 'POST')      { $c->storeAdjustment(); }
        elseif  ($action === 'transfer')                                    { $c->transfer(); }
        elseif  ($action === 'store-transfer'   && $method === 'POST')      { $c->storeTransfer(); }
        elseif  ($action === 'batches')                                     { $c->batches(); }
        elseif  ($action === 'low-stock')                                   { $c->lowStock(); }
        elseif  ($action === 'valuation')                                   { $c->valuation(); }
        else                                                                { $c->index(); }
        break;

    // ── Purchasing ────────────────────────────────────────────
    case 'purchasing':
        $c = loadController('Purchasing');
        if      ($action === 'vendors')                                     { $c->vendors(); }
        elseif  ($action === 'create-vendor')                               { $c->createVendor(); }
        elseif  ($action === 'store-vendor'     && $method === 'POST')      { $c->storeVendor(); }
        elseif  ($action === 'edit-vendor')                                 { $c->editVendor($id); }
        elseif  ($action === 'update-vendor'    && $method === 'POST')      { $c->updateVendor($id); }
        elseif  ($action === 'create')                                      { $c->create(); }
        elseif  ($action === 'store'            && $method === 'POST')      { $c->store(); }
        elseif  ($action === 'view')                                        { $c->view($id); }
        elseif  ($action === 'approve'          && $method === 'POST')      { $c->approve($id); }
        elseif  ($action === 'receive')                                     { $c->receive($id); }
        elseif  ($action === 'store-receipt'    && $method === 'POST')      { $c->storeReceipt($id); }
        elseif  ($action === 'cancel'           && $method === 'POST')      { $c->cancel($id); }
        else                                                                { $c->index(); }
        break;

    // ── Accounting ────────────────────────────────────────────
    case 'accounting':
        $c = loadController('Accounting');
        if      ($action === 'invoices')                                    { $c->invoices(); }
        elseif  ($action === 'create-invoice')                              { $c->createInvoice(); }
        elseif  ($action === 'store-invoice'    && $method === 'POST')      { $c->storeInvoice(); }
        elseif  ($action === 'invoice')                                     { $c->viewInvoice($id); }
        elseif  ($action === 'record-payment'   && $method === 'POST')      { $c->recordPayment($id); }
        elseif  ($action === 'expenses')                                    { $c->expenses(); }
        elseif  ($action === 'create-expense')                              { $c->createExpense(); }
        elseif  ($action === 'store-expense'    && $method === 'POST')      { $c->storeExpense(); }
        elseif  ($action === 'approve-expense'  && $method === 'POST')      { $c->approveExpense($id); }
        elseif  ($action === 'pl')                                          { $c->profitLoss(); }
        elseif  ($action === 'journals')                                    { $c->journalEntries(); }
        elseif  ($action === 'journal')                                     { $c->viewJournal($id); }
        else                                                                { $c->index(); }
        break;

    // ── HR & Payroll ──────────────────────────────────────────
    case 'hr':
        $c = loadController('HR');
        if      ($action === 'leaves')                                      { $c->leaveRequests(); }
        elseif  ($action === 'my-leaves')                                   { $c->myLeaves(); }
        elseif  ($action === 'leave')                                       { $c->createLeave(); }
        elseif  ($action === 'store-leave'      && $method === 'POST')      { $c->storeLeave(); }
        elseif  ($action === 'approve-leave'    && $method === 'POST')      { $c->approveLeave($id); }
        elseif  ($action === 'reject-leave'     && $method === 'POST')      { $c->rejectLeave($id); }
        elseif  ($action === 'overtime')                                    { $c->overtime(); }
        elseif  ($action === 'ot')                                          { $c->createOT(); }
        elseif  ($action === 'store-ot'         && $method === 'POST')      { $c->storeOT(); }
        elseif  ($action === 'approve-ot'       && $method === 'POST')      { $c->approveOT($id); }
        elseif  ($action === 'payroll')                                     { $c->payroll(); }
        elseif  ($action === 'create-period')                               { $c->createPeriod(); }
        elseif  ($action === 'store-period'     && $method === 'POST')      { $c->storePeriod(); }
        elseif  ($action === 'period')                                      { $c->viewPayroll($id); }
        elseif  ($action === 'process'          && $method === 'POST')      { $c->processPayroll($id); }
        elseif  ($action === 'slip')                                        { $c->viewSlip($id); }
        elseif  ($action === 'approve-payroll'  && $method === 'POST')      { $c->approvePayroll($id); }
        else                                                                { $c->index(); }
        break;

    default:
        http_response_code(404);
        echo "<h1>404 - Page Not Found</h1>";
}
