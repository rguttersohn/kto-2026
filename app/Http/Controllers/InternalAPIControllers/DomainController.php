<?php

namespace App\Http\Controllers\InternalAPIControllers;

use App\Http\Controllers\Controller;
use App\Services\DomainService;
use App\Http\Resources\DomainResource;

class DomainController extends Controller
{
    public function index(){

        $domains = DomainService::queryDomains();

        return response()->json([

            'data' => DomainResource::collection($domains)

        ]);

    }
}
