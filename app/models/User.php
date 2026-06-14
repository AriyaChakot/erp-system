<?php
require_once __DIR__ . '/BaseModel.php';

class User extends BaseModel {
    protected $table = 'user_table';

    public function findByEmail(string $email): ?array {
        $stmt = $this->db->prepare("SELECT * FROM user_table WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch() ?: null;
    }

    public function emailExists(string $email, int $excludeId = 0): bool {
        $sql = "SELECT COUNT(*) FROM user_table WHERE email = ? AND id != ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$email, $excludeId]);
        return (int)$stmt->fetchColumn() > 0;
    }
}
