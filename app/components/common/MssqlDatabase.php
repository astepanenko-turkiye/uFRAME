<?php

namespace Application\Components\common;

class MssqlDatabase implements Database {

    public static $connection;
    private \PDOStatement $pdo_statement;

    public function __construct($config=null) {

        if( empty(self::$connection) ) {

            if(empty($config)) $config=CONFIG["mssql"];

            $dsn="dblib:host={$config["host"]}:{$config["port"]};dbname={$config["dbname"]}";

            $connection=new \PDO(
                $dsn,$config["username"],$config["password"]
            );

            $connection->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE,\PDO::FETCH_ASSOC);

            $connection->query("SET ANSI_NULLS, CONCAT_NULL_YIELDS_NULL, ANSI_WARNINGS, ANSI_PADDING ON");

            self::$connection=$connection;

        }

    }

    public function startTransaction() {
        return self::$connection->beginTransaction();
    }

    public function endTransaction() {
        return self::$connection->commit();
    }

    public function query($query) {
        $this->pdo_statement=self::$connection->query($query);
        return $this->pdo_statement;
    }

    public function lastInsertId() {
        return self::$connection->lastInsertId();
    }

    public function affectedRows() {
        if(!$this->pdo_statement) throw new \PDOException("no pdo_statement");
        return $this->pdo_statement->rowCount();
    }

    public function fetchOne($query) {
        $this->pdo_statement=$this->query($query);
        return $this->pdo_statement->fetch();
    }

    public function fetchAll($query): \Generator {
        $this->pdo_statement=$this->query($query);
        while($row=$this->pdo_statement->fetch()) {
            yield $row;
        }
    }

    public function escapeString($str = null) {
        return self::$connection->quote($str);
    }

    public function escapeIdentifier($str) {
        return '`'.self::$connection->quote($str).'`';
    }
}