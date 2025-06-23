<?php

namespace App\Http\Controllers\PageControllers;


use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Inertia\Inertia;

class IndexController extends Controller
{
    public function index(){
        return Inertia::render('Index');
    }
}
