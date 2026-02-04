<?php

namespace App\Http\Controllers\InternalAPIControllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    
    public function logout(Request $request){

        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        $response = response()->json([
            'message' => 'Logged out.'
        ]);

        return $response;
    
    }
}
