<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HealthController extends Controller
{

    public function hello() {

        $hello = "hello";
        return response()->json([
            'message' => 'Hello World!',
            'status' => 'ok',
        ]);

    }
}
