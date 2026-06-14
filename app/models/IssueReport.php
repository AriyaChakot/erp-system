<?php
require_once __DIR__ . '/BaseModel.php';

class IssueReport extends BaseModel {
    protected $table = 'issue_reports';

    public function findByUser(int $userId): array {
        return $this->findAll('user_id = ?', [$userId], 'created_at DESC');
    }

    public function countByUser(int $userId): int {
        return $this->count('user_id = ?', [$userId]);
    }

    public function findAllWithUsers(): array {
        $stmt = $this->db->prepare("
            SELECT r.*, u.name AS user_name, u.email AS user_email
            FROM issue_reports r
            JOIN user_table u ON r.user_id = u.id
            ORDER BY
                CASE r.status WHEN 'open' THEN 0 WHEN 'in_progress' THEN 1 ELSE 2 END,
                r.created_at DESC
        ");
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
