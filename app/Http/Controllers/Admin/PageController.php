<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use \App\Page;

class PageController extends Controller
{
  
  public function getPage($pageId)
  {
    $page = Page::find($pageId);
    return view('admin.page')->withPage($page);
  }
  
  public function store(Request $request)
  {
    $page = Page::find($request->pageId);
    $page->title = $request->title;
    $page->body = $request->body;
    $page->save();
    return back()->with('done','page updated');
    
  }
}
