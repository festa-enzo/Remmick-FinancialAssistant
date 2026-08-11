<?php

class Response {

    public static function cors(){

        header('Access-Control-Allow-Origin: http://planer.remmick.com');

        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization');
        header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Max-Age: 3600');
    }

    public static function json($data, $status = 200) {
        self::cors();
        http_response_code($status);
        header('Content-Type: application/json');

        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }


}