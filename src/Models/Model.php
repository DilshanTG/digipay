<?php

namespace App\Models;

use App\Database;
use App\Services\Logger;
use PDO;

abstract class Model
{
    protected string $table;
    protected string $primaryKey = 'id';
    protected array $fillable = [];
    protected array $casts = [];
    protected array $attributes = [];
    public bool $timestamps = true;

    public function __construct(array $attributes = [])
    {
        $this->fill($attributes);
    }

    public function fill(array $attributes): self
    {
        foreach ($attributes as $key => $value) {
            if (in_array($key, $this->fillable) || empty($this->fillable)) {
                $this->attributes[$key] = $this->castAttribute($key, $value);
            }
        }
        return $this;
    }

    protected function castAttribute(string $key, $value)
    {
        if (!isset($this->casts[$key])) {
            return $value;
        }

        $cast = $this->casts[$key];

        if ($cast === 'array' || $cast === 'json') {
            return is_string($value) ? json_decode($value, true) : $value;
        }

        if ($cast === 'boolean' || $cast === 'bool') {
            return (bool) $value;
        }

        if (str_starts_with($cast, 'decimal')) {
            return $value;
        }

        return $value;
    }

    public function __get($key)
    {
        // Check for accessor method (e.g., getCustomerNameAttribute)
        $accessor = 'get' . str_replace('_', '', ucwords($key, '_')) . 'Attribute';
        if (method_exists($this, $accessor)) {
            return $this->$accessor();
        }

        return $this->attributes[$key] ?? null;
    }

    public function __set($key, $value)
    {
        $this->attributes[$key] = $this->castAttribute($key, $value);
    }

    public static function db(): PDO
    {
        return Database::getPdo();
    }

    public static function all(): array
    {
        $instance = new static();
        $stmt = self::db()->query("SELECT * FROM {$instance->table}");
        $results = $stmt->fetchAll();

        return array_map(fn($row) => new static($row), $results);
    }

    public static function find($id): ?self
    {
        $instance = new static();
        $stmt = self::db()->prepare("SELECT * FROM {$instance->table} WHERE {$instance->primaryKey} = ? LIMIT 1");
        $stmt->execute([$id]);
        $result = $stmt->fetch();

        return $result ? new static($result) : null;
    }

    public static function where(string $column, $value): ?self
    {
        $instance = new static();

        // Sanitize column name to prevent SQL injection
        $column = preg_replace('/[^a-zA-Z0-9_]/', '', $column);
        if (empty($column)) {
            return null;
        }

        $stmt = self::db()->prepare("SELECT * FROM {$instance->table} WHERE `{$column}` = ? LIMIT 1");
        $stmt->execute([$value]);
        $result = $stmt->fetch();

        return $result ? new static($result) : null;
    }

    public static function whereAll(string $column, $value): array
    {
        $instance = new static();

        // Sanitize column name to prevent SQL injection
        $column = preg_replace('/[^a-zA-Z0-9_]/', '', $column);
        if (empty($column)) {
            return [];
        }

        $stmt = self::db()->prepare("SELECT * FROM {$instance->table} WHERE `{$column}` = ?");
        $stmt->execute([$value]);
        $results = $stmt->fetchAll();

        return array_map(fn($row) => new static($row), $results);
    }

    public function save(): bool
    {
        $now = date('Y-m-d H:i:s');

        if (isset($this->attributes[$this->primaryKey])) {
            return $this->update();
        }

        if ($this->timestamps) {
            $this->attributes['created_at'] = $now;
            $this->attributes['updated_at'] = $now;
        }

        return $this->attemptDbOperation(function() {
            $columns = array_keys($this->attributes);
            $placeholders = array_fill(0, count($columns), '?');

            // Wrap column names in backticks to handle reserved words
            $quotedColumns = array_map(fn($col) => "`{$col}`", $columns);

            $sql = sprintf(
                "INSERT INTO %s (%s) VALUES (%s)",
                $this->table,
                implode(', ', $quotedColumns),
                implode(', ', $placeholders)
            );

            $values = array_map(fn($key) => $this->prepareForDb($key), $columns);

            $stmt = self::db()->prepare($sql);
            $result = $stmt->execute($values);

            if ($result) {
                $this->attributes[$this->primaryKey] = self::db()->lastInsertId();
            }

            return $result;
        });
    }

    public function update(array $attributes = []): bool
    {
        if (!isset($this->attributes[$this->primaryKey])) {
            return false;
        }

        // If attributes provided, fill them first
        if (!empty($attributes)) {
            $this->fill($attributes);
        }

        if ($this->timestamps) {
            $this->attributes['updated_at'] = date('Y-m-d H:i:s');
        }

        return $this->attemptDbOperation(function() {
            $sets = [];
            $values = [];

            foreach ($this->attributes as $key => $value) {
                if ($key !== $this->primaryKey) {
                    $sets[] = "`{$key}` = ?";
                    $values[] = $this->prepareForDb($key);
                }
            }

            $values[] = $this->attributes[$this->primaryKey];

            $sql = sprintf(
                "UPDATE %s SET %s WHERE %s = ?",
                $this->table,
                implode(', ', $sets),
                $this->primaryKey
            );

            $stmt = self::db()->prepare($sql);
            return $stmt->execute($values);
        });
    }

    protected function attemptDbOperation(callable $operation)
    {
        try {
            return $operation();
        } catch (\PDOException $e) {
            // SQLSTATE[42S22]: Column not found
            if ($e->getCode() === '42S22' || strpos($e->getMessage(), 'Unknown column') !== false) {
                if (preg_match("/Unknown column '([^']+)'/", $e->getMessage(), $matches)) {
                    $column = $matches[1];
                    Logger::info("Auto-fixing missing column '$column' in table '{$this->table}'");
                    
                    // Add column with safe default (NULL)
                    // If sandbox_mode or is_active, use TINYINT
                    $type = "TEXT NULL";
                    if (str_contains($column, 'mode') || str_contains($column, 'active') || str_contains($column, '_at')) {
                         $type = "TINYINT(1) DEFAULT 0";
                         if (str_contains($column, '_at')) $type = "TIMESTAMP NULL DEFAULT NULL";
                    }

                    try {
                        self::db()->exec("ALTER TABLE {$this->table} ADD COLUMN `{$column}` {$type}");
                        // Retry the operation
                        return $operation();
                    } catch (\Exception $schemaEx) {
                        Logger::error("Failed to auto-add column: " . $schemaEx->getMessage());
                    }
                }
            }
            throw $e;
        }
    }

    protected function prepareForDb(string $key)
    {
        $value = $this->attributes[$key];

        if (isset($this->casts[$key])) {
            $cast = $this->casts[$key];

            if ($cast === 'array' || $cast === 'json') {
                return is_array($value) ? json_encode($value) : $value;
            }

            if ($cast === 'boolean' || $cast === 'bool') {
                return $value ? 1 : 0;
            }
        }

        return $value;
    }

    public function delete(): bool
    {
        // Prioritize Primary Key
        if (isset($this->attributes[$this->primaryKey])) {
            $key = $this->primaryKey;
            $val = $this->attributes[$this->primaryKey];
        } 
        // Fallback to order_id if available (common in this app)
        elseif (isset($this->attributes['order_id'])) {
            $key = 'order_id';
            $val = $this->attributes['order_id'];
        } else {
            return false;
        }

        $sql = sprintf(
            "DELETE FROM %s WHERE %s = ?",
            $this->table,
            $key
        );

        $stmt = self::db()->prepare($sql);
        return $stmt->execute([$val]);
    }

    public static function create(array $attributes): self
    {
        $instance = new static($attributes);
        if (!$instance->save()) {
            throw new \Exception("Failed to create " . static::class . " record.");
        }
        return $instance;
    }

    public function toArray(): array
    {
        $array = [];
        foreach ($this->attributes as $key => $value) {
            // Use __get to trigger accessors
            $array[$key] = $this->__get($key);
        }
        return $array;
    }
}
