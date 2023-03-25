<?php

namespace Application\Components\telegram;

class Telegram {

    private static $_unlockWaitTimeout=60;

    private static $_lockFile=BASE_DIR."app_cache".DS."Telegram.lock";

    private static $_lockFilePointer;

    public static function locked() {

        $counter=0;

        self::$_lockFilePointer=fopen(self::$_lockFile,"w+");

        do {

            if($counter > 0) sleep(1);

            if($counter > self::$_unlockWaitTimeout) break;

            $counter++;

            $flockResult=flock(self::$_lockFilePointer,LOCK_EX | LOCK_NB);

        } while(!$flockResult);

        return $flockResult;

    }

    public static function unlock() {

        if(self::$_lockFilePointer) {

            flock(self::$_lockFilePointer,LOCK_UN);

            fclose(self::$_lockFilePointer);

        }

    }

    public static $counter=0;
    public static $isTest=false;
    public static $verbose=false;
    public static $enableLogging=false;
    private static $lastResult=false;

    public static function getLastResponse() {
        return self::$lastResult;
    }

    private static function _send($key,$url,$a,$header) {

        $response=false;

        $url="https://api.telegram.org/{$key}/{$url}";

        if(self::$verbose) {
            echo var_export(array($url,$a),1).PHP_EOL;
        }

        if(self::$enableLogging) {
            file_put_contents(BASE_DIR."app_logs".DS."TG_LOG.LOG",date("Y-m-d H:i:s")."\t".var_export(array($url,$a),1).PHP_EOL,FILE_APPEND);
        }

        if(self::$counter>0) usleep(500000);

        $ch=curl_init();
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_POST,1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,$a);
        curl_setopt($ch, CURLOPT_HTTPHEADER,[$header]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,0);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT,3);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);

        if(!self::$isTest) {
            $result=curl_exec($ch);
            curl_close($ch);

            self::$lastResult=$result;
            self::$lastResult=json_decode(self::$lastResult,1);

            $response=!empty(self::$lastResult["ok"]);
        }

        if(self::$verbose) {
            echo var_export($result,1).PHP_EOL;
        }

        if(self::$enableLogging) {
            file_put_contents(BASE_DIR."app_logs".DS."TG_LOG.LOG",date("Y-m-d H:i:s")."\t".var_export($result,1).PHP_EOL,FILE_APPEND);
        }

        self::$counter++;

        return $response;

    }

    private static function chunk_split_unicode($str,$l=255,$e="\r\n") {
        $a=array_chunk(preg_split("//u",$str,-1,PREG_SPLIT_NO_EMPTY),$l);
        foreach($a as $k=>$v) $a[$k]=implode("",$v);
        return $a;
    }

    public static function send($options,$text_wrap_start_tag="",$text_wrap_end_tag="") {

        $response=false;

        try {

            if(!is_array($options)) {
                $text=$options; $options=[];
                $options["text"]=$text;
                $options["text_wrap_start_tag"]=$text_wrap_start_tag;
                $options["text_wrap_end_tag"]=$text_wrap_end_tag;
            }

            if(empty($config) || !is_object($config)) $config=CONFIG["telegram"];

            $text=                  array_key_exists("text",$options) ?                 $options["text"] :                  null;
            $text_wrap_start_tag=   array_key_exists("text_wrap_start_tag",$options) ?  $options["text_wrap_start_tag"] :   "";
            $text_wrap_end_tag=     array_key_exists("text_wrap_end_tag",$options) ?    $options["text_wrap_end_tag"] :     "";
            $custom_field=          array_key_exists("custom_field",$options) ?         $options["custom_field"] :          [];
            $filename=              array_key_exists("filename",$options) ?             $options["filename"] :              null;
            $filetype=              array_key_exists("filetype",$options) ?             $options["filetype"] :              null;
            $caption=               array_key_exists("caption",$options) ?              $options["caption"] :               null;
            $reply_markup=          array_key_exists("reply_markup",$options) ?         $options["reply_markup"] :          [];
            $chat_id=               array_key_exists("chat_id",$options) ?              $options["chat_id"] :               $config["chat_id_info"];
            $key=                   array_key_exists("key",$options) ?                  $options["key"] :                   $config["key"];
            $verbose=               array_key_exists("verbose",$options) ?              $options["verbose"] :               null;
            $parse_mode=            array_key_exists("parse_mode",$options) ?           $options["parse_mode"] :            "HTML";
            $disable_web_page_preview=array_key_exists("disable_web_page_preview",$options) ? $options["disable_web_page_preview"] : true;

            if($verbose) self::$verbose=true;

            $file=array_key_exists("debug",$options) && array_key_exists("file",$options["debug"]) ? $options["debug"]["file"] : null;
            $line=array_key_exists("debug",$options) && array_key_exists("line",$options["debug"]) ? $options["debug"]["line"] : null;

            if(!is_null($file) && !is_null($line)) {
                self::$enableLogging=true;
            }

            $a=[
                "chat_id"=>$chat_id,
                "disable_web_page_preview"=>$disable_web_page_preview
            ];

            if($parse_mode) {
                $a=array_merge($a,["parse_mode"=>$parse_mode]);
            }

            if($reply_markup) {
                $a=array_merge($a,["reply_markup"=>json_encode($reply_markup)]);
            }

            $url="sendMessage";
            $header="Content-type: application/x-www-form-urlencoded";

            switch($filetype) {

                case "photo":

                    if(is_array($filename) && count($filename)>1) {

                        $url="sendMediaGroup";
                        $header="Content-type: multipart/form-data";

                        $upload_file_type="photo";
                        $media=[];

                        foreach($filename as $k=>$fn) {

                            $fname=$upload_file_type."_".$k;

                            $file=new \SplFileInfo($fn);
                            $fn=$file->getPathname();

                            $mime=mime_content_type($fn);

                            $a=array_merge($a,[$fname=>new \CurlFile($fn,$mime,$fname)]);

                            $media[]=[
                                "type"=>$upload_file_type,
                                "media"=>"attach://".$fname,
                                "caption"=>$caption
                            ];

                        }

                        $a=array_merge($a,["media"=>json_encode($media)]);

                    } else {

                        $url="sendPhoto";
                        $header="Content-type: multipart/form-data";

                        if($custom_field) $a=array_merge($a,$custom_field);

                        $filename=is_array($filename) ? current($filename) : $filename;

                        $file=new \SplFileInfo($filename);

                        $fullpath=$file->getPathname();
                        $fname=$file->getFileName();

                        $file=new \CurlFile($fullpath,mime_content_type($fullpath),$fname);

                        $a=array_merge($a,["photo"=>$file,"caption"=>$caption]);

                    }

                    self::_send($key,$url,$a,$header);

                    break;

                case "document":

                    $url="sendDocument";
                    $header="Content-type: multipart/form-data";

                    if($custom_field) $a=array_merge($a,$custom_field);

                    $file=new \SplFileInfo($filename);

                    $fullpath=$file->getPathname();
                    $fname=$file->getFileName();

                    $file=new \CurlFile($fullpath,mime_content_type($fullpath),$fname);

                    $a=array_merge($a,["document"=>$file,"caption"=>$caption]);

                    self::_send($key,$url,$a,$header);

                    break;

                default:

                    $text=trim($text);

                    if($text==="") return false;

                    if($custom_field) $a=array_merge($a,$custom_field);

                    $strings=self::chunk_split_unicode($text,4010); unset($text);

                    foreach($strings as $k=>$fetched_text) {

                        $fetched_text=$text_wrap_start_tag.$fetched_text.$text_wrap_end_tag;

                        $a=array_merge($a,["text"=>$fetched_text]);

                        $a=http_build_query($a);

                        self::_send($key,$url,$a,$header);
                    }

                    break;
            }

        } catch(\Exception $e) {
            file_put_contents(BASE_DIR."app_logs".DS."TG_LOG.LOG",date("Y-m-d H:i:s")."***ERROR!***:\t".var_export($e->getMessage(),1).PHP_EOL,FILE_APPEND);
        }

        return $response;

    }

    public static function sendNonBlocking($options) {

        if(!is_array($options)) {
            $options=[
                "text"=>$options
            ];
        }

        $options["text"]="<i>".date("Y-m-d H:i:s")."</i>: ".$options["text"];

        $path=(new \SplFileInfo(__FILE__))->getRealPath();

        $exec_string='bash -c "exec php '.$path.' '.base64_encode(Telegram::class).' getNonBlockingAndSend '.base64_encode(serialize($options)).' > /dev/null 2>&1 &"';

        exec($exec_string);

    }

    public static function getNonBlockingAndSend($options=null) {

        if(is_null($options)) return false;

        $options=unserialize(base64_decode($options));

        if(Telegram::locked()) {

            Telegram::send($options);

            Telegram::unlock();

        }

        return true;

    }

}

if(!empty($argv)) {

    if(!defined("DS")) {
        require_once realpath(dirname(__FILE__,4)).DIRECTORY_SEPARATOR."app".DIRECTORY_SEPARATOR."config".DIRECTORY_SEPARATOR."config.php";
    }

    $inbound_arguments=$argv;

    array_shift($inbound_arguments);

    $className=array_shift($inbound_arguments);
    $className=base64_decode($className);

    $method=array_shift($inbound_arguments);
    $data=array_shift($inbound_arguments);

    if($className===Telegram::class && method_exists(Telegram::class,$method)) {

        Telegram::$method($data);

    }

}