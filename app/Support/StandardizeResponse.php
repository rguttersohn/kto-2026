<?php

namespace App\Support;
use Illuminate\Support\Facades\Response;
use Illuminate\Database\Eloquent\Collection;

class StandardizeResponse {

    public static function APIResponse(
        bool $error_status = true,
        string $error_message = 'success',
        array | Collection $data = [],
        int $status_code = 200
    ){


        return Response::json([
            'error' => [
                'status' => $error_status, 
                'message' => $error_message
            ],
            'data' => $data
            ], $status_code);
    
    }
}