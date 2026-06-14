<?php
require_once __DIR__ . '/BaseModel.php';

class Customer extends BaseModel {
    protected $table = 'customers';

    public function search(string $q): array {
        return $this->findAll(
            "name LIKE ? OR email LIKE ? OR phone LIKE ? OR company LIKE ?",
            ["%$q%", "%$q%", "%$q%", "%$q%"]
        );
    }
}
