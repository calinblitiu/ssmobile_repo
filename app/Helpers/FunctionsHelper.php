<?php
use Illuminate\Http\Request;

function isLocalDev() {
    // $request = new Request;
    // $host = $request->server('HTTP_HOST');

    $host = isset($_SERVER['HTTP_HOST'])?$_SERVER['HTTP_HOST']:'';
    
    if ($host == 'localhost:8000') return true;
    return false;
}

