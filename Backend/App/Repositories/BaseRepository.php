<?php

require_once __DIR__ . '/../../Config/Database.php';
use PDO;

abstract class BaseRepository {

    protected PDO $db;

    protected string $table;

    protected array $sortableColumns = ['id', 'created_at', 'updated_at'];

    public function __construct(){
        $this->db = Database::getConnection();
    }

    public function findAll(array $options = []): array
    {
        $limit  = (int)($options['limit']  ?? (int)($_ENV['PAGINATION_DEFAULT_LIMIT'] ?? 15));
        $offset = (int)($options['offset'] ?? 0);
        $sort   = $this->sanitizeSort($options['sort']  ?? 'id');
        $order  = strtoupper($options['order'] ?? 'ASC') === 'DESC' ? 'DESC' : 'ASC';
        $where  = $options['where'] ?? '';
        $params = $options['params'] ?? [];

        $whereClause = $where ? "WHERE {$where}" : '';

        $sql = "SELECT * FROM {$this->table}
                {$whereClause}
                ORDER BY {$sort} {$order}
                LIMIT :limit OFFSET :offset";

        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->bindValue(':limit',  $limit,  PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function paginate(int $page, int $limit, array $options = []): array
    {
        $limit  = min($limit, (int)($_ENV['PAGINATION_MAX_LIMIT'] ?? 100));
        $offset = ($page - 1) * $limit;

        $options['limit']  = $limit;
        $options['offset'] = $offset;

        $items = $this->findAll($options);
        $total = $this->count($options['where'] ?? '', $options['params'] ?? []);

        return Paginator::make($items, $total, $page, $limit);
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM {$this->table} WHERE id = :id LIMIT 1"
        );
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function create(array $data): array
    {
        $data = $this->timestamps($data, 'create');
        $columns = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));

        $stmt = $this->db->prepare(
            "INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})"
        );
        $stmt->execute($data);

        $id = (int)$this->db->lastInsertId();
        return $this->findById($id) ?? $data;
    }

    public function update(int $id, array $data): ?array
    {
        if (!$this->findById($id)) {
            return null;
        }

        $data = $this->timestamps($data, 'update');
        $setClause = implode(', ', array_map(
            fn($col) => "{$col} = :{$col}",
            array_keys($data)
        ));

        $stmt = $this->db->prepare(
            "UPDATE {$this->table} SET {$setClause} WHERE id = :id"
        );
        $data[':id'] = $id;
        $stmt->execute($data);

        return $this->findById($id);
    }

    public function delete(int $id): bool
    {
        if (!$this->findById($id)) {
            return false;
        }

        $stmt = $this->db->prepare(
            "DELETE FROM {$this->table} WHERE id = :id"
        );
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    protected function sanitizeSort(string $column): string
    {
        return in_array($column, $this->sortableColumns, true)
            ? $column
            : 'id';
    }

        protected function timestamps(array $data, string $operation): array
    {
        $now = date('Y-m-d H:i:s');
        if ($operation === 'create') {
            $data['created_at'] = $now;
        }
        $data['updated_at'] = $now;
        return $data;
    }

}
    