<?php

namespace App\Http\Controllers;

use App\Contact;
use Illuminate\Http\Request;
use \App\Page;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use App\Mail\Contactus;

class PagesController extends BaseController
{
  public function __construct()
  {
    parent::__construct();
  }
  
  public function rules()
  {
    $page = Page::find(1);
    return view('rules')->withPage($page);
  }
  
  public function contactUs()
  {
    $page = Page::find(2);
    return view('contact')->withPage($page);
  }
  
  public function dmca()
  {
    $page = Page::find(3);
    return view('dmca')->withPage($page);
  }

  public function faq()
  {
    $page = Page::find(4);
    return view('dmca')->withPage($page);
  }
  
  public function submitStream()
  {
    echo 'submit stream page';
  }
  public function donate() 
  {
    return view('donate');
  }
  
  public function sendContactUs(Request $request)
  {
    $this->validate($request, [
      'name' => 'required',
      'email' => 'required',
      'message' => 'required',
      'g-recaptcha-response' => 'required|captcha'
    ]);
    
    $contact = new Contact;
    $contact->name = $request->name;
    $contact->email = $request->email;
    $contact->subject = $request->subject;
    $contact->body = $request->message;
    $contact->save();
    $this->mail([
      'name' => $request->name,
      'email' => $request->email,
      'subject' => $request->subject,
      'body' => $request->message
    ]);
    return back()->with('done', 'The message has been sent successfully, thank you!');
  }
  
  private function mail($data)
  {
    Mail::to('contact@soccerstreams.net')->send(new Contactus($data));
  }
}
