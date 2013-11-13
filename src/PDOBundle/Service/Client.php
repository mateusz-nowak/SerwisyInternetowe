<?php

namespace PDOBundle\Service;

use PDO;

class Client
{
    const FETCH_ALL = 1;
    const FETCH_SINGLE = 2;

    /* @var $pdo \PDO */
    protected $pdo;

    public function __construct($type, $host, $port, $username, $password, $dbName)
    {
        $this->pdo = new PDO(
            sprintf('%s:host=%s;port=%s;dbname=%s', $type, $host, $port, $dbName), $username, $password
        );

        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public function map($query, array $parameters, $class, $fetch = self::FETCH_ALL)
    {
        $stmt = $this->pdo->prepare($query);
        $stmt->setFetchMode(PDO::FETCH_CLASS, $class);
        $stmt->execute($parameters);

        if ($fetch == self::FETCH_ALL) {
            return $stmt->fetchAll();
        } else {
            return $stmt->fetch();
        }
    }

    public function executeQuery($query, array $parameters = array())
    {
        $stmt = $this->pdo->prepare($query);

        $this->pdo->beginTransaction();
        $stmt->execute(array_values($parameters));

        $this->pdo->commit();
    }

    public function update($tableName, $data, $where = '')
    {
        $sql = sprintf('UPDATE `%s` SET ', $tableName);

        $attrs = array();
        foreach ($data as $k => $v) {
            $attrs[] = sprintf('%s = ?', $k);
        }

        $sql .= join(', ', $attrs);

        if ($where) {
            $sql .= ' WHERE ' . $where;
        }

        $stmt = $this->pdo->prepare($sql);

        $this->pdo->beginTransaction();
        $stmt->execute(array_values($data));

        $this->pdo->commit();

        return $this->pdo->lastInsertId();
    }

    public function insert($tableName, $data)
    {
        $sql = sprintf('INSERT INTO `%s` (%s) VALUES (%s)', $tableName,
            join(',', array_keys($data)),
            rtrim(str_repeat('?,', count($data)), ',')
        );

        $stmt = $this->pdo->prepare($sql);

        $this->pdo->beginTransaction();
        $stmt->execute(array_values($data));

        $this->pdo->commit();

        return $this->pdo->lastInsertId();
    }
}
