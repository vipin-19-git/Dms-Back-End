<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;

class Controller extends BaseController
{
   protected function respondWithToken($token)
    {
      
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
             'user'=>auth()->User(),
             'status'=>200,
             'success'=>true
              
        ]);
    }
}

