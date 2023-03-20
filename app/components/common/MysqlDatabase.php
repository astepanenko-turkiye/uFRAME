<?php

namespace Application\Components\common;

class MysqlDatabase implements Database {

    public static $connection;

    public function __construct($config=null) {

        if( empty(self::$connection) ) {

            if(empty($config)) $config=CONFIG["mysql"];

            self::$connection=mysqli_connect(
                $config['host']
                , $config['username']
                , $config['password']
                , $config['dbname']
                , $config['port']
            );

            if(!self::$connection) throw new \Exception(mysqli_connect_error());

            self::$connection->set_charset($config['charset']);

        }

    }

    public function startTransaction() {

        self::$connection->autocommit(false);

    }

    public function endTransaction() {

        self::$connection->commit();

        self::$connection->autocommit(true);

    }

    public function query($query) {

        $result=self::$connection->query($query);

        if(!$result) throw new \Exception(self::$connection->error, self::$connection->errno);

        return $this->lastInsertId();

    }

    public function lastInsertId() {

        return self::$connection->insert_id;

    }

    public function affectedRows() {

        return self::$connection->affected_rows;

    }

    public function fetchOne($query) {

        $r=self::$connection->query($query);

        $row=$r->fetch_array(MYSQLI_ASSOC);

        $r->free();

        return $row;

    }

    /*
     * to be used only in a loop
     */
    public function fetchAll($query): \Generator {

        $r=self::$connection->query($query);

        if($r===false) {
            throw new \Exception(mysqli_error(self::$connection),mysqli_errno(self::$connection));
        }

        while($row=$r->fetch_array(MYSQLI_ASSOC)) yield $row;

        $r->free();

    }

    public function escapeString($str=null) {

        return $str === null ? "NULL" : "'".self::$connection->real_escape_string($str)."'";

    }

    public function escapeIdentifier($str) {

        return '`'.self::$connection->real_escape_string($str).'`';

    }

}