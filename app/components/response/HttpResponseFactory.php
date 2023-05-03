<?php

namespace Application\Components\response;

class HttpResponseFactory {

    public static function get($type="application/json") {

        switch($type) {

            case "application/x-msgpack":

                return MessagePackResponse::class;

                break;

            default:

                return JsonResponse::class;

                break;

        }

    }

}