<?php

namespace Application\Components\common;

class File {

    protected static $handles=[];

    public static function getHandle($filename,$continue_on_not_existing=false) {

        if(!file_exists($filename)) {

            if(!$continue_on_not_existing) return false;

        } else {

            clearstatcache(1,$filename);

        }

        $key=md5($filename);

        if( empty(static::$handles[$key]) ) static::$handles[$key]=new static($filename);

        return static::$handles[$key];

    }

    protected $filename, $hs=[], $hs_cursor=[], $hs_eof_pos=[];

    private function __construct($filename) {

        $this->filename=$filename;

    }

    public function __destruct() {

        foreach($this->hs as $h) fclose($h);

    }

    public function iterate() {

        if( empty($this->hs["r"]) ) {

            $this->hs["r"]=fopen($this->filename,"r");

            fseek($this->hs["r"],0,SEEK_END);

            $this->hs_eof_pos["r"]=ftell( $this->hs["r"] );

            fseek($this->hs["r"], 0);

        }

        while( ($line=fgets( $this->hs["r"] )) !== false ) {

            $this->hs_cursor["r"]=ftell( $this->hs["r"] );

            yield trim($line);

        }

        fseek($this->hs["r"], 0);

    }

    public function deleteIfEOF() {

        if( $this->hs_eof_pos["r"]===$this->hs_cursor["r"] ) {

            unlink($this->filename);

        }

    }

    public function write($line) {

        if( empty($this->hs["a"]) ) $this->hs["a"]=fopen($this->filename,"a");

        $result=fwrite($this->hs["a"],$line.PHP_EOL);

        $this->hs_cursor["a"]=ftell( $this->hs["a"] );

        return $result;

    }

    public function clear() {

        if( !empty($this->hs["a"]) ) { $h=fopen($this->filename,"w"); fclose($h); }

        if( !empty($this->hs["r"]) ) { $h=fopen($this->filename,"w"); fclose($h); }

    }

}