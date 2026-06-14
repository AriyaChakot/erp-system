<?php
require_once __DIR__ . '/BaseModel.php';

class Employee extends BaseModel {
    protected $table = 'employees';

    public function search(string $q): array {
        return $this->findAll(
            "name LIKE ? OR email LIKE ? OR department LIKE ? OR position LIKE ?",
            ["%$q%", "%$q%", "%$q%", "%$q%"]
        );
    }
}
