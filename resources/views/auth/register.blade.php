@extends('layouts.noSidebar')

@section('content')
  <style>
    .main-content {
      background-color: #FFF;
    }

    .form-group.required .control-label:after {
      content:" *";
      color:red;
    }
    .panel-body{
      padding-bottom: 20px!important;
    }
  </style>
  <div class="container">
    <div class="row">
      <div class="col-md-8 col-md-offset-2">
        <div class="panel panel-rss">
          <div class="panel-heading">Register</div>
          <div class="panel-body">
            <form class="form-horizontal" role="form" method="POST" action="{{ secure_url('register') }}">
              {{ csrf_field() }}

              <div class="form-group">
                <div class="col-md-8 col-md-offset-4">
                  <a class="btn btn-rss btnRedditLogin" href="{{ secure_url('redditLogin') }}">
                    <i class="fa fa-reddit-alien" aria-hidden="true"></i> Login With Reddit
                  </a>
                </div>
              </div>

              <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }} required">
                <label for="name" class="col-md-4 control-label">Username</label>
                
                <div class="col-md-6">
                  <input id="name" type="text" class="form-control" name="name" placeholder="4-8 alpha-numeric characters" value="{{ old('name') }}" required autofocus>
                  @if ($errors->has('name'))
                    <span class="help-block">
                      <strong>{{ $errors->first('name') }}</strong>
                    </span>
                  @endif
                </div>
              </div>
              
              <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                <label for="email" class="col-md-4 control-label">E-Mail Address</label>
                
                <div class="col-md-6">
                  <input id="email" type="email" class="form-control" name="email" placeholder="optional" value="{{ old('email') }}">
                  
                  @if ($errors->has('email'))
                    <span class="help-block">
                      <strong>{{ $errors->first('email') }}</strong>
                    </span>
                  @endif
                </div>
              </div>
              
              <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }} required">
                <label for="password" class="col-md-4 control-label">Password</label>
                
                <div class="col-md-6">
                  <input id="password" type="password" class="form-control" name="password" placeholder="minimum 6 characters alpha-numeric"
                  pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{6,}" title="Must contain at least one number and one uppercase and lowercase letter, and at least 6 or more characters" required>
                  
                  @if ($errors->has('password'))
                    <span class="help-block">
                      <strong>{{ $errors->first('password') }}</strong>
                    </span>
                  @endif
                </div>
              </div>
              
              <div class="form-group required">
                <label for="password-confirm" class="col-md-4 control-label">Confirm Password</label>
                
                <div class="col-md-6">
                  <input id="password-confirm" type="password" class="form-control required" name="password_confirmation" required>
                </div>
              </div>
  
              <div class="form-group{{ $errors->has('g-recaptcha-response') ? ' has-error' : '' }} required">
                <div class="col-md-6 col-md-offset-4">
                  {!! app('captcha')->display() !!}
                  {!! $errors->first('g-recaptcha-response', '<p class="help-block">:message</p>') !!}
                </div>
              </div>
              
                <div class="col-md-6 col-md-offset-4">
                  <button type="submit" class="btn btn-rss">
                    Register
                  </button>
                  <span><a href="{{ secure_url('login') }}">Have an account?</a></span>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
