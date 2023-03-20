<?php

namespace Application\Components\common;

class Common {

    private static $db;

    public static function __callStatic($name,$args=null) {

        if($name==="db") {

            if( empty(self::$db) ) {

                self::$db=new MysqlDatabase();

            }

            return self::$db;

        }

        return null;

    }

}