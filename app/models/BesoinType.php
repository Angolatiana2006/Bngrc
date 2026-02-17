<?php

namespace app\models;

use app\config\Db;

class BesoinType
{
    public static function getById(int $id): ?array
    {
        $db = Db::getInstance();

        $row = $db->fetchRow(
            "SELECT * FROM besoins_types WHERE id = ?",
            [$id]
        );

        return $row ? $row->getData() : null;
    }

    public static function getAll(): array
    {
        $db = Db::getInstance();

        $rows = $db->fetchAll(
            "SELECT * FROM besoins_types ORDER BY type, name ASC"
        );

        return array_map(fn($row) => $row->getData(), $rows);
    }

    public static function getByType(string $type): array
    {
        $db = Db::getInstance();

        $rows = $db->fetchAll(
            "SELECT * FROM besoins_types WHERE type = ? ORDER BY name ASC",
            [$type]
        );

        return array_map(fn($row) => $row->getData(), $rows);
    }

    public static function insert(array $data): void
    {
        $db = Db::getInstance();

        $db->runQuery(
            "INSERT INTO besoins_types (type, name) VALUES (?, ?)",
            [$data['type'], $data['name']]
        );
    }

    public static function update(int $id, array $data): void
    {
        $db = Db::getInstance();

        $db->runQuery(
            "UPDATE besoins_types SET type = ?, name = ? WHERE id = ?",
            [$data['type'], $data['name'], $id]
        );
    }

    public static function delete(int $id): void
    {
        $db = Db::getInstance();

        $db->runQuery(
            "DELETE FROM besoins_types WHERE id = ?",
            [$id]
        );
    }

    public static function deleteAll(): void
    {
        $db = Db::getInstance();
        $db->exec("DELETE FROM besoins_types");
    }


    public static function getByNameAndType(string $name, $type): ?array
    {
        $db = Db::getInstance();
        
       
        if (is_numeric($type)) {
            
            $typeRow = $db->fetchRow(
                "SELECT type FROM besoins_types WHERE id = ?",
                [$type]
            );
            
            if (!$typeRow) {
                return null;
            }
            
            $typeValue = $typeRow->getData()['type'];
        } else {
            
            $typeValue = $type;
        }
        
        
        $row = $db->fetchRow(
            "SELECT * FROM besoins_types WHERE name = ? AND type = ?",
            [$name, $typeValue]
        );
        
        return $row ? $row->getData() : null;
    }

    
    public static function getDistinctTypes(): array
    {
        $db = Db::getInstance();

        $rows = $db->fetchAll(
            "SELECT DISTINCT type FROM besoins_types ORDER BY type"
        );

        return array_map(fn($row) => $row->getData()['type'], $rows);
    }
}