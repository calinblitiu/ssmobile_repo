@extends('master')

@section('content')
  <style>
    .main-content {
      background-color: #FFF;
    }
    @media(max-height:732px){
      @media(min-height:630px){
        .footer, #footer{
          position: static!important;
        }
      }
    }
  </style>
  <div class="container">
    <div class="row">
      <div class="col-md-8 col-md-offset-2">
        <div class="panel panel-rss">
          <div class="panel-heading">Login</div>
          <div class="panel-body">
            @if (session('error'))
              <div class="alert alert-danger" style="margin: 10px;">
                {{ session('error') }}
              </div>
            @endif
            <form class="form-horizontal" role="form" method="POST" action="{{ secure_url('login') }}">
              {{ csrf_field() }}
              
              {{--<div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                <label for="email" class="col-md-4 control-label">E-Mail Address</label>
                
                <div class="col-md-6">
                  <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" required autofocus>
                  
                  @if ($errors->has('email'))
                    <span class="help-block">
                      <strong>{{ $errors->first('email') }}</strong>
                    </span>
                  @endif
                </div>
              </div>--}}
              <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                <label for="name" class="col-md-4 control-label">User Name</label>

                <div class="col-md-6">
                  <input id="name" type="text" class="form-control" name="name" value="{{ old('name') }}" required autofocus>

                  @if ($errors->has('name'))
                    <span class="help-block">
                      <strong>{{ $errors->first('name') }}</strong>
                    </span>
                  @endif
                </div>
              </div>
              
              <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                <label for="password" class="col-md-4 control-label">Password</label>
                
                <div class="col-md-6">
                  <input id="password" type="password" class="form-control" name="password" required>
                  
                  @if ($errors->has('password'))
                    <span class="help-block">
                      <strong>{{ $errors->first('password') }}</strong>
                    </span>
                  @endif
                </div>
              </div>
              
              <div class="form-group">
                <div class="col-md-6 col-md-offset-4">
                  <div class="checkbox">
                    <label>
                      {{--<input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}> Remember Me--}}
                      <input type="checkbox" name="remember" {{ (isset($_COOKIE['name']) && isset($_COOKIE['password'])) ? 'checked' : '' }}> Remember Me
                    </label>
                  </div>
                </div>
              </div>
  
              <div class="form-group{{ $errors->has('g-recaptcha-response') ? ' has-error' : '' }}">
                <div class="col-md-6 col-md-offset-4">
                  {!! app('captcha')->display() !!}
                  {!! $errors->first('g-recaptcha-response', '<p class="help-block">:message</p>') !!}
                </div>
              </div>
              
              <div class="form-group">
                <div class="col-md-8 col-md-offset-4">
                  <button type="submit" class="btn btn-rss">
                    Login
                  </button>
                  
                  <a class="btn btn-link" href="{{ secure_url('register') }}">New user?</a>
                </div>
                <div class="col-md-8 col-md-offset-4" style="padding-left: 0px; padding-top: 10px;">
                  <a class="btn btn-link" href="{{ secure_url('password/reset') }}">
                    Forgot Your Password?
                  </a>
                </div>
              </div>

              <div class="form-group">
                <div class="col-md-8 col-md-offset-4">
                  <a class="btn btn-rss btnRedditLogin" href="{{ secure_url('redditLogin') }}">
                    <i class="fa fa-reddit-alien" aria-hidden="true"></i> Login With Reddit
                  </a>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
