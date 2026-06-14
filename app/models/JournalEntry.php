<?php
require_once __DIR__ . '/BaseModel.php';

class JournalEntry extends BaseModel {
    protected $table = 'journal_entries';

    public function findAllWithSummary(): array {
        $sql = "SELECT je.*,
                       COALESCE(SUM(jl.debit), 0) AS total_debit,
                       e.name AS created_by_name
                FROM journal_entries je
                LEFT JOIN journal_lines jl ON je.id = jl.entry_id
                LEFT JOIN employees e ON je.created_by = e.id
                GROUP BY je.id
                ORDER BY je.created_at DESC
                LIMIT 200";
        return $this->db->query($sql)->fetchAll();
    }

    public function findByIdWithLines(int $id): ?array {
        $stmt = $this->db->prepare(
            "SELECT je.*, e.name AS created_by_name
             FROM journal_entries je
             LEFT JOIN employees e ON je.created_by = e.id
             WHERE je.id = ?"
        );
        $stmt->execute([$id]);
        $je = $stmt->fetch();
        if (!$je) return null;

        $lines = $this->db->prepare(
            "SELECT jl.*, coa.code AS account_code, coa.name AS account_name, coa.account_type
             FROM journal_lines jl
             JOIN chart_of_accounts coa ON jl.account_id = coa.id
             WHERE jl.entry_id = ?
             ORDER BY jl.id"
        );
        $lines->execute([$id]);
        $je['lines'] = $lines->fetchAll();

        $je['total_debit']  = array_sum(array_column($je['lines'], 'debit'));
        $je['total_credit'] = array_sum(array_column($je['lines'], 'credit'));
        return $je;
    }

    public function createEntry(array $data, array $lines): int {
        $totalDebit  = array_sum(array_column($lines, 'debit'));
        $totalCredit = array_sum(array_column($lines, 'credit'));
        if (round($totalDebit, 2) !== round($totalCredit, 2)) {
            throw new Exception('Journal entry ไม่สมดุล: Debit ' . $totalDebit . ' ≠ Credit ' . $totalCredit);
        }

        $this->db->beginTransaction();
        try {
            $entryId = $this->insert($data);
            foreach ($lines as $line) {
                $this->db->prepare(
                    "INSERT INTO journal_lines (entry_id, account_id, description, debit, credit)
                     VALUES (?,?,?,?,?)"
                )->execute([
                    $entryId,
                    $line['account_id'],
                    $line['description'] ?? null,
                    (float)($line['debit']  ?? 0),
                    (float)($line['credit'] ?? 0),
                ]);
            }
            $this->db->commit();
            return $entryId;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function getProfitLoss(int $year, int $month): array {
        $dateFrom = sprintf('%d-%02d-01', $year, $month);
        $dateTo   = date('Y-m-t', strtotime($dateFrom));

        $stmt = $this->db->prepare(
            "SELECT coa.account_type,
                    coa.code, coa.name,
                    SUM(jl.credit - jl.debit) AS net
             FROM journal_lines jl
             JOIN journal_entries je  ON jl.entry_id   = je.id
             JOIN chart_of_accounts coa ON jl.account_id = coa.id
             WHERE je.status = 'posted'
               AND je.entry_date BETWEEN ? AND ?
               AND coa.account_type IN ('revenue','expense')
             GROUP BY coa.id, coa.account_type, coa.code, coa.name
             ORDER BY coa.account_type DESC, coa.code"
        );
        $stmt->execute([$dateFrom, $dateTo]);
        return $stmt->fetchAll();
    }

    public function generateEntryNumber(): string {
        $year = date('Y');
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM journal_entries WHERE entry_number LIKE ?");
        $stmt->execute(["JE-$year-%"]);
        $seq = (int)$stmt->fetchColumn() + 1;
        return 'JE-' . $year . '-' . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }

    public function getAccountIdByCode(string $code): ?int {
        $stmt = $this->db->prepare("SELECT id FROM chart_of_accounts WHERE code = ?");
        $stmt->execute([$code]);
        $row = $stmt->fetch();
        return $row ? (int)$row['id'] : null;
    }
}
