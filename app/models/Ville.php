<?php

namespace app\models;

use app\config\Db;

class Ville
{
    public static function getById(int $id): ?array
    {
        $db = Db::getInstance();

        $row = $db->fetchRow(
            "SELECT * FROM villes WHERE id = ?",
            [$id]
        );

        return $row ? $row->getData() : null;
    }

    public static function getAll(): array
    {
        $db = Db::getInstance();

        $rows = $db->fetchAll(
            "SELECT * FROM villes ORDER BY name ASC"
        );

        return array_map(fn($row) => $row->getData(), $rows);
    }

    public static function insert(array $data): void
    {
        $db = Db::getInstance();

        $db->runQuery(
            "INSERT INTO villes (name) VALUES (?)",
            [$data['name']]
        );
    }

    public static function update(int $id, array $data): void
    {
        $db = Db::getInstance();

        $db->runQuery(
            "UPDATE villes SET name = ? WHERE id = ?",
            [$data['name'], $id]
        );
    }

    public static function delete(int $id): void
    {
        $db = Db::getInstance();

        $db->runQuery(
            "DELETE FROM villes WHERE id = ?",
            [$id]
        );
    }

    public static function deleteAll(): void
    {
        $db = Db::getInstance();
        $db->exec("DELETE FROM villes");
    }
}