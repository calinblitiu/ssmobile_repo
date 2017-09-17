<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ModeratorController extends Controller
{
  public function login()
  {
    return view('admin.login');
  }
  
  public function doLogin(Request $request)
  {
  }
  
  public function dashboard()
  {
    return view('admin.dashboard');
  }
}
