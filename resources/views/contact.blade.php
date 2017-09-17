@extends('master')
@section('title',$page->title.' -')
@section('content')
  <style>
    .pageContent{
      background-color: #f1f1f1;
      padding: 15px;
    }
    ul.parsley-errors-list{
      color:red;
      padding-left: 0px;
    }
    ul.parsley-errors-list li{
      list-style: none;
    }
  </style>
  <div class="row">
    <div class="col-md-12 col-md-offset-0">
      <div class="pageContent">
        <h3>{{ $page->title }}</h3>
        @if (session('done'))
          <div class="alert alert-info">
            {{ session('done') }}
          </div>
        @endif
        {!! $page->body !!}
        <div class="col-md-8 col-md-offset-2" style="margin-top: 15px">
          <form class="form-horizontal" role="form" action="{{ secure_url('contact-us') }}" id="contactForm" method="post" data-parsley-validate>
            {{ csrf_field() }}
            <div class="form-group {{ $errors->has('password') ? ' has-error' : '' }} required">
              <label class="col-sm-3 control-label">Name</label>
              <div class="col-sm-9">
                <input type="text" name="name" class="form-control" required placeholder="Your name">
                @if ($errors->has('name'))
                  <span class="help-block">
                      <strong>{{ $errors->first('name') }}</strong>
                    </span>
                @endif
              </div>
            </div>
            <div class="form-group {{ $errors->has('password') ? ' has-error' : '' }} required">
              <label class="col-sm-3 control-label">Email</label>
              <div class="col-sm-9">
                <input type="email" name="email" class="form-control" placeholder="Email address">
                {{--<input type="email" name="email" class="form-control" required placeholder="Email address" data-parsley-type="email">--}}
                @if ($errors->has('email'))
                  <span class="help-block">
                      <strong>{{ $errors->first('email') }}</strong>
                    </span>
                @endif
              </div>
            </div>
            <div class="form-group {{ $errors->has('password') ? ' has-error' : '' }} required">
              <label for="" class="col-sm-3 control-label">Subject</label>
              <div class="col-sm-9">
                <select class="form-control" name="subject" required title="Choose one of the following...">
                  <option value="General">General</option>
                  <option value="Suggestion">Suggestion</option>
                  <option value="bug">Bug</option>
                  <option value="DMCA">DMCA</option>
                </select>
              </div>
            </div>
            <div class="form-group {{ $errors->has('password') ? ' has-error' : '' }} required">
              <label class="col-sm-3 control-label">Message</label>
              <div class="col-sm-9">
                <textarea name="message" class="form-control" cols="30" rows="10" required></textarea>
                @if ($errors->has('message'))
                  <span class="help-block">
                      <strong>{{ $errors->first('message') }}</strong>
                    </span>
                @endif
              </div>
            </div>
            <div class="form-group{{ $errors->has('g-recaptcha-response') ? ' has-error' : '' }}">
              <div class="col-md-6 col-md-offset-3">
                {!! app('captcha')->display() !!}
                {!! $errors->first('g-recaptcha-response', '<p class="help-block">:message</p>') !!}
              </div>
            </div>
            <div class="form-group">
              <div class="col-sm-offset-3 col-sm-9">
                <button type="submit" class="btn btn-rss">Send</button>
              </div>
            </div>
          </form>
        </div>
        <div class="clearfix"></div>
      </div>
    </div>
  </div>
@endsection