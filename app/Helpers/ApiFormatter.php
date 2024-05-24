<?php

namespace App\Helpers;

class ApiFormatter
{
    protected static $response = [
        'status_code' => null,
        'message' => null,
        'data' => null
    ];

    public static function createApi($status_code = null, $message = null, $data = null)
    {
        self::$response['status_code'] = $status_code;
        self::$response['message'] = $message;
        self::$response['data'] = $data;

        return response()->json(self::$response, self::$response['status_code'])->header("Access-Control-Allow-Origin",  "*");
    }
}
