<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PermissionController extends ResourceController
{
    public static $modelName = 'Permission';
    public static $middlewareCustom = ['auth:sanctum'];
}
