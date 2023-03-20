<?php

namespace Application\Components\response;

class JsonResponse extends HttpResponse {

    public $success=true;
    public $message="OK";
    public $affected_rows=0;
    public $execute_time=0;

    protected $content_type='Content-Type: application/json';

    public function flush() {

        if(!isset($this->body)) {

            $this->body=[
                "success"=>$this->success,
                "message"=>$this->message,
                "execute_time"=>microtime(1)-START_TIME,
            ];

        }

        $this->body=json_encode($this->body,JSON_UNESCAPED_UNICODE);

        parent::flush();

    }

}