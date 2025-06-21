<?php

namespace App\Support;
use Illuminate\Support\Facades\Response;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Resources\Json\JsonResource;

class StandardizeResponse {

    public static function internalAPIResponse(
        bool $error_status = false,
        string $error_message = 'success',
        array | Collection | JsonResource $data = [],
        int $status_code = 200
    ){


        if ($data instanceof JsonResource) {
            $data = $data->response()->getData(true);
        }

        return Response::json([
            'error' => [
                'status' => $error_status, 
                'message' => $error_message
            ],
            'data' => $data
            ], $status_code);
    
    }
}