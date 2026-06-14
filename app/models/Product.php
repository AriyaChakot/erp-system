<?php
require_once __DIR__ . '/BaseModel.php';

class Product extends BaseModel {
    protected $table = 'products';

    public function search(string $q): array {
        return $this->findAll(
            "name LIKE ? OR code LIKE ? OR category LIKE ?",
            ["%$q%", "%$q%", "%$q%"]
        );
    }

    public function getLowStock(int $threshold = 10): array {
        return $this->findAll("stock_qty <= ? AND status = 'active'", [$threshold], 'stock_qty ASC');
    }
}
