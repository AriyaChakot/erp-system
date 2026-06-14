<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/Invoice.php';
require_once __DIR__ . '/../models/JournalEntry.php';
require_once __DIR__ . '/../models/Expense.php';
require_once __DIR__ . '/../models/Customer.php';
require_once __DIR__ . '/../models/Vendor.php';

class AccountingController extends BaseController {
    private Invoice      $invoiceModel;
    private JournalEntry $jeModel;
    private Expense      $expenseModel;

    public function __construct() {
        $this->invoiceModel = new Invoice();
        $this->jeModel      = new JournalEntry();
        $this->expenseModel = new Expense();
    }

    public function index(): void {
        $this->requireAuth();
        $stats    = $this->invoiceModel->getStats();
        $expStats = $this->expenseModel->getStats();
        $this->render('accounting/index', [
            'stats'        => $stats,
            'expStats'     => $expStats,
            'monthlyRev'   => $this->invoiceModel->getMonthlyRevenue((int)date('Y')),
            'flash'        => $this->getFlash(),
            'pageTitle'    => 'ภาพรวมบัญชี',
            'activePage'   => 'accounting',
        ]);
    }

    public function invoices(): void {
        $this->requireAuth();
        $type   = $this->input('type');
        $status = $this->input('status');
        $this->render('accounting/invoices', [
            'invoices'  => $this->invoiceModel->findAllWithParty($type, $status),
            'type'      => $type,
            'status'    => $status,
            'flash'     => $this->getFlash(),
            'pageTitle' => 'Invoice',
            'activePage'=> 'invoices',
        ]);
    }

    public function createInvoice(): void {
        $this->requireAuth();
        $type = $this->input('type') ?: 'sale';
        $this->render('accounting/invoice_form', [
            'invoice'   => null,
            'type'      => $type,
            'vendors'   => (new Vendor())->getActive(),
            'invNumber' => $this->invoiceModel->generateInvoiceNumber($type),
            'pageTitle' => 'สร้าง Invoice',
            'activePage'=> 'invoices',
        ]);
    }

    public function storeInvoice(): void {
        $this->requireAuth();
        $type     = $this->input('invoice_type') ?: 'sale';
        $subtotal = (float)$this->input('subtotal');
        $vatRate  = (float)($this->input('vat_rate') ?: 7);
        $vatAmt   = round($subtotal * $vatRate / 100, 2);
        $total    = $subtotal + $vatAmt;

        $data = [
            'invoice_number' => $this->input('invoice_number'),
            'invoice_type'   => $type,
            'customer_name'  => $type === 'sale'     ? $this->input('customer_name') : null,
            'vendor_id'      => $type === 'purchase' ? ((int)$this->input('vendor_id') ?: null) : null,
            'issue_date'     => $this->input('issue_date') ?: date('Y-m-d'),
            'due_date'       => $this->input('due_date')   ?: date('Y-m-d', strtotime('+30 days')),
            'subtotal'       => $subtotal,
            'vat_rate'       => $vatRate,
            'vat_amount'     => $vatAmt,
            'total'          => $total,
            'paid_amount'    => 0,
            'status'         => 'sent',
            'notes'          => $this->input('notes'),
        ];

        $id = $this->invoiceModel->insert($data);

        // Auto journal entry: Dr. AR / Cr. Revenue + Cr. VAT (for sale invoices)
        if ($type === 'sale') {
            $arId  = $this->jeModel->getAccountIdByCode('1200');
            $revId = $this->jeModel->getAccountIdByCode('4000');
            $vatId = $this->jeModel->getAccountIdByCode('2200');
            if ($arId && $revId && $vatId) {
                $this->jeModel->createEntry([
                    'entry_number'   => $this->jeModel->generateEntryNumber(),
                    'reference_type' => 'invoice',
                    'reference_id'   => $id,
                    'entry_date'     => $data['issue_date'],
                    'description'    => 'Invoice ' . $data['invoice_number'],
                    'status'         => 'posted',
                    'created_by'     => null,
                    'posted_by'      => null,
                ], [
                    ['account_id' => $arId,  'debit'  => $total,    'credit' => 0,        'description' => 'ลูกหนี้'],
                    ['account_id' => $revId, 'debit'  => 0,         'credit' => $subtotal,'description' => 'รายได้'],
                    ['account_id' => $vatId, 'debit'  => 0,         'credit' => $vatAmt,  'description' => 'VAT'],
                ]);
            }
        }

        $this->setFlash('success', 'สร้าง Invoice เรียบร้อยแล้ว');
        $this->redirect('/accounting/invoice/' . $id);
    }

    public function viewInvoice(int $id): void {
        $this->requireAuth();
        $inv = $this->invoiceModel->findByIdWithPayments($id);
        if (!$inv) { $this->setFlash('danger', 'ไม่พบ Invoice'); $this->redirect('/accounting/invoices'); }
        $this->render('accounting/invoice_view', [
            'invoice'   => $inv,
            'flash'     => $this->getFlash(),
            'pageTitle' => 'Invoice ' . $inv['invoice_number'],
            'activePage'=> 'invoices',
        ]);
    }

    public function recordPayment(int $id): void {
        $this->requireAuth();
        $inv = $this->invoiceModel->findById($id);
        if (!$inv) { $this->setFlash('danger', 'ไม่พบ Invoice'); $this->redirect('/accounting/invoices'); }

        $amount = (float)$this->input('amount');
        if ($amount <= 0) {
            $this->setFlash('danger', 'กรุณาระบุจำนวนเงิน');
            $this->redirect('/accounting/invoice/' . $id);
        }

        $payData = [
            'payment_number' => $this->invoiceModel->generatePaymentNumber(),
            'payment_date'   => $this->input('payment_date') ?: date('Y-m-d'),
            'amount'         => $amount,
            'payment_method' => $this->input('payment_method') ?: 'cash',
            'reference_no'   => $this->input('reference_no'),
            'notes'          => $this->input('notes'),
            'created_by'     => null,
        ];
        $this->invoiceModel->recordPayment($id, $payData);

        // Journal entry: Dr. Cash / Cr. AR
        if ($inv['invoice_type'] === 'sale') {
            $cashId = $this->jeModel->getAccountIdByCode('1100');
            $arId   = $this->jeModel->getAccountIdByCode('1200');
            if ($cashId && $arId) {
                $this->jeModel->createEntry([
                    'entry_number'   => $this->jeModel->generateEntryNumber(),
                    'reference_type' => 'payment',
                    'reference_id'   => $id,
                    'entry_date'     => $payData['payment_date'],
                    'description'    => 'ชำระ ' . $inv['invoice_number'],
                    'status'         => 'posted',
                    'created_by'     => null,
                    'posted_by'      => null,
                ], [
                    ['account_id' => $cashId, 'debit' => $amount, 'credit' => 0,      'description' => 'รับเงิน'],
                    ['account_id' => $arId,   'debit' => 0,       'credit' => $amount,'description' => 'ชำระลูกหนี้'],
                ]);
            }
        }

        $this->setFlash('success', 'บันทึกการชำระเงินเรียบร้อยแล้ว');
        $this->redirect('/accounting/invoice/' . $id);
    }

    public function expenses(): void {
        $this->requireAuth();
        $status = $this->input('status');
        $this->render('accounting/expenses', [
            'expenses'  => $this->expenseModel->findAllWithAccount($status),
            'stats'     => $this->expenseModel->getStats(),
            'categories'=> Expense::getCategories(),
            'status'    => $status,
            'flash'     => $this->getFlash(),
            'pageTitle' => 'ค่าใช้จ่าย',
            'activePage'=> 'expenses',
        ]);
    }

    public function createExpense(): void {
        $this->requireAuth();
        $accounts = $this->expenseModel->getExpenseAccounts();
        $this->render('accounting/expense_form', [
            'expense'    => null,
            'accounts'   => $accounts,
            'categories' => Expense::getCategories(),
            'expNumber'  => $this->expenseModel->generateExpenseNumber(),
            'pageTitle'  => 'บันทึกค่าใช้จ่าย',
            'activePage' => 'expenses',
        ]);
    }

    public function storeExpense(): void {
        $this->requireAuth();
        $data = [
            'expense_number' => $this->input('expense_number'),
            'category'       => $this->input('category'),
            'description'    => $this->input('description'),
            'amount'         => (float)$this->input('amount'),
            'expense_date'   => $this->input('expense_date') ?: date('Y-m-d'),
            'account_id'     => (int)$this->input('account_id') ?: null,
            'receipt_no'     => $this->input('receipt_no'),
            'notes'          => $this->input('notes'),
            'status'         => 'pending',
            'created_by'     => null,
        ];
        if ($data['amount'] <= 0 || empty($data['description'])) {
            $this->setFlash('danger', 'กรุณากรอกข้อมูลให้ครบถ้วน');
            $this->redirect('/accounting/expense');
        }
        $this->expenseModel->insert($data);
        $this->setFlash('success', 'บันทึกค่าใช้จ่ายเรียบร้อยแล้ว');
        $this->redirect('/accounting/expenses');
    }

    public function approveExpense(int $id): void {
        $this->requireAdmin();
        $exp = $this->expenseModel->findById($id);
        if ($exp && $exp['status'] === 'pending') {
            $this->expenseModel->update($id, ['status' => 'approved', 'approved_by' => null]);

            // Journal entry: Dr. Expense account / Cr. Cash
            $expAccId  = $exp['account_id'] ?? $this->jeModel->getAccountIdByCode('5200');
            $cashId    = $this->jeModel->getAccountIdByCode('1100');
            if ($expAccId && $cashId) {
                $this->jeModel->createEntry([
                    'entry_number'   => $this->jeModel->generateEntryNumber(),
                    'reference_type' => 'expense',
                    'reference_id'   => $id,
                    'entry_date'     => $exp['expense_date'],
                    'description'    => $exp['description'],
                    'status'         => 'posted',
                    'created_by'     => null,
                    'posted_by'      => null,
                ], [
                    ['account_id' => $expAccId, 'debit' => $exp['amount'], 'credit' => 0,               'description' => 'ค่าใช้จ่าย'],
                    ['account_id' => $cashId,   'debit' => 0,              'credit' => $exp['amount'],  'description' => 'จ่ายเงิน'],
                ]);
            }
            $this->setFlash('success', 'อนุมัติค่าใช้จ่ายเรียบร้อยแล้ว');
        }
        $this->redirect('/accounting/expenses');
    }

    public function profitLoss(): void {
        $this->requireAuth();
        $year  = (int)($this->input('year')  ?: date('Y'));
        $month = (int)($this->input('month') ?: date('n'));
        $rows  = $this->jeModel->getProfitLoss($year, $month);

        $revenue  = [];
        $expenses = [];
        foreach ($rows as $r) {
            if ($r['account_type'] === 'revenue') $revenue[]  = $r;
            else                                   $expenses[] = $r;
        }
        $totalRevenue  = array_sum(array_column($revenue,  'net'));
        $totalExpense  = array_sum(array_column($expenses, 'net'));
        $netProfit     = $totalRevenue - abs($totalExpense);

        $this->render('accounting/profit_loss', [
            'revenue'      => $revenue,
            'expenses'     => $expenses,
            'totalRevenue' => $totalRevenue,
            'totalExpense' => abs($totalExpense),
            'netProfit'    => $netProfit,
            'year'         => $year,
            'month'        => $month,
            'pageTitle'    => 'กำไร/ขาดทุน',
            'activePage'   => 'pl',
        ]);
    }

    public function journalEntries(): void {
        $this->requireAuth();
        $this->render('accounting/journals', [
            'entries'   => $this->jeModel->findAllWithSummary(),
            'flash'     => $this->getFlash(),
            'pageTitle' => 'Journal Entries',
            'activePage'=> 'accounting',
        ]);
    }

    public function viewJournal(int $id): void {
        $this->requireAuth();
        $je = $this->jeModel->findByIdWithLines($id);
        if (!$je) { $this->setFlash('danger', 'ไม่พบ Journal Entry'); $this->redirect('/accounting/journals'); }
        $this->render('accounting/journal_view', [
            'je'        => $je,
            'pageTitle' => 'Journal ' . $je['entry_number'],
            'activePage'=> 'accounting',
        ]);
    }
}
