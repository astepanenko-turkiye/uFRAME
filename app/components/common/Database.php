<?php

namespace Application\Components\common;

interface Database {

    public function startTransaction();

    public function endTransaction();

    public function query($query);

    public function lastInsertId();

    public function affectedRows();

    public function fetchOne($query);

    /* to be used only in a loop */
    public function fetchAll($query): \Generator;

    public function escapeString($str=null);

    public function escapeIdentifier($str);

}