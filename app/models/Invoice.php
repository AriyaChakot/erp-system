<?php
require_once __DIR__ . '/BaseModel.php';

class Invoice extends BaseModel {
    protected $table = 'invoices';

    public function findAllWithParty(string $type = '', string $status = ''): array {
        $where  = '1=1';
        $params = [];
        if ($type)   { $where .= ' AND i.invoice_type = ?'; $params[] = $type; }
        if ($status) { $where .= ' AND i.status = ?'; $params[] = $status; }
        $sql = "SELECT i.*,
                       COALESCE(i.customer_name, o.customer_name) AS customer_name,
                       v.name AS vendor_name
                FROM invoices i
                LEFT JOIN orders o  ON i.order_id   = o.id
                LEFT JOIN vendors v ON i.vendor_id   = v.id
                WHERE $where
                ORDER BY i.created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function findByIdWithPayments(int $id): ?array {
        $stmt = $this->db->prepare(
            "SELECT i.*,
                    COALESCE(i.customer_name, o.customer_name) AS customer_name,
                    v.name AS vendor_name
             FROM invoices i
             LEFT JOIN orders o  ON i.order_id   = o.id
             LEFT JOIN vendors v ON i.vendor_id   = v.id
             WHERE i.id = ?"
        );
        $stmt->execute([$id]);
        $inv = $stmt->fetch();
        if (!$inv) return null;

        $pStmt = $this->db->prepare(
            "SELECT p.*, e.name AS created_by_name
             FROM payments p
             LEFT JOIN employees e ON p.created_by = e.id
             WHERE p.invoice_id = ?
             ORDER BY p.payment_date DESC"
        );
        $pStmt->execute([$id]);
        $inv['payments'] = $pStmt->fetchAll();
        return $inv;
    }

    public function recordPayment(int $invoiceId, array $data): void {
        $this->db->beginTransaction();
        try {
            $this->db->prepare(
                "INSERT INTO payments (payment_number, invoice_id, payment_date, amount, payment_method, reference_no, notes, created_by)
                 VALUES (?,?,?,?,?,?,?,?)"
            )->execute([
                $data['payment_number'],
                $invoiceId,
                $data['payment_date'],
                $data['amount'],
                $data['payment_method'],
                $data['reference_no'] ?? null,
                $data['notes'] ?? null,
                $data['created_by'] ?? null,
            ]);

            // Update paid_amount and status
            $this->db->prepare(
                "UPDATE invoices SET paid_amount = paid_amount + ? WHERE id = ?"
            )->execute([$data['amount'], $invoiceId]);

            $inv = $this->findById($invoiceId);
            $newStatus = $inv['paid_amount'] >= $inv['total'] ? 'paid'
                       : ($inv['paid_amount'] > 0 ? 'partial' : $inv['status']);
            $this->update($invoiceId, ['status' => $newStatus]);

            $this->db->commit();
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function getReceivables(): array {
        return $this->findAllWithParty('sale', '') + [];
    }

    public function getStats(): array {
        $sql = "SELECT
                    COUNT(*) AS total,
                    COALESCE(SUM(CASE WHEN invoice_type='sale' AND status NOT IN ('paid','cancelled') THEN total - paid_amount END), 0) AS total_receivable,
                    COALESCE(SUM(CASE WHEN invoice_type='purchase' AND status NOT IN ('paid','cancelled') THEN total - paid_amount END), 0) AS total_payable,
                    COALESCE(SUM(CASE WHEN invoice_type='sale' AND status='paid' THEN total END), 0) AS total_received,
                    COUNT(CASE WHEN status='overdue' OR (status NOT IN ('paid','cancelled') AND due_date < CURDATE()) THEN 1 END) AS overdue_count
                FROM invoices";
        return $this->db->query($sql)->fetch();
    }

    public function getMonthlyRevenue(int $year): array {
        $stmt = $this->db->prepare(
            "SELECT MONTH(issue_date) AS month, COALESCE(SUM(total),0) AS revenue
             FROM invoices
             WHERE YEAR(issue_date) = ? AND invoice_type = 'sale' AND status = 'paid'
             GROUP BY MONTH(issue_date)
             ORDER BY month"
        );
        $stmt->execute([$year]);
        $rows = $stmt->fetchAll();
        $monthly = array_fill(1, 12, 0);
        foreach ($rows as $r) $monthly[(int)$r['month']] = (float)$r['revenue'];
        return $monthly;
    }

    public function generateInvoiceNumber(string $type = 'sale'): string {
        $prefix = $type === 'sale' ? 'INV' : 'PINV';
        $date   = date('Ymd');
        $stmt   = $this->db->prepare("SELECT COUNT(*) FROM invoices WHERE invoice_number LIKE ?");
        $stmt->execute(["$prefix-$date-%"]);
        $seq = (int)$stmt->fetchColumn() + 1;
        return "$prefix-$date-" . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }

    public function generatePaymentNumber(): string {
        $date = date('Ymd');
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM payments WHERE payment_number LIKE ?");
        $stmt->execute(["PAY-$date-%"]);
        $seq = (int)$stmt->fetchColumn() + 1;
        return 'PAY-' . $date . '-' . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }
}
