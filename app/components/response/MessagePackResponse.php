<?php

namespace Application\Components\response;

class MessagePackResponse extends HttpResponse {

    public $success=true;
    public $message="OK";
    public $affected_rows=0;
    public $execute_time=0;

    protected $content_type='Content-Type: application/x-msgpack';

    public function flush() {

        if(!isset($this->body)) {

            $this->body=[
                "success"=>$this->success,
                "message"=>$this->message
            ];

        }

        $this->body=msgpack_pack($this->body);

        parent::flush();

    }

}