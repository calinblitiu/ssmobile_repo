@extends('master')
@section('title','User profile - ')
@section('content')
    <div class="row">
        @include('userMenu')
        <div class="col-md-8">
            <div class="row">
                <div class="col-md-12">
                    @if(\Illuminate\Support\Facades\Auth::check() && \Illuminate\Support\Facades\Auth::user()->verified==0 && \Illuminate\Support\Facades\Auth::user()->verification_token !="1" && Auth::user()->email != "")
                        <div class="alert alert-info">
                            Your account has not been verified. Please click <a
                                    href="{{ secure_url('sendVerification') }}"><b>HERE</b></a> to re-send the
                            verification email if you have not received one.
                            You may
                            find the email in spam folder
                        </div>
                    @endif

                    <div class="panel panel-rss">
                        <div class="panel-heading">Change Your Infomation</div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <h2>{{ Auth::user()->name }}</h2>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-5 col-md-5">
                                    <div class="user-image">
                                        @if (file_exists('images/avatar' . '/' . Auth::User()->id . '.jpg'))
                                            <img src="{{ secure_url('images/avatar') . '/' . Auth::User()->id . '.jpg?' . microtime(true) }}">
                                        @else
                                            <img src="{{ secure_url('images/noimage/no-image.png') }}">
                                        @endif
                                    </div>
                                </div>

                                @if (session('done'))
                                    <div class="alert alert-success" style="margin: 10px;">
                                        {{ session('done') }}
                                    </div>
                                @endif

                                @if (count($errors) > 0)
                                    <div class="alert alert-danger">
                                        <ul>
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                <div class="col-sm-7 col-md-7">
                                    <div class="row margin-top-30">
                                        <div class="col-md-12">
                                            <div class="row">
                                                <div class="col-md-8">
                                                    <form class="form-horizontal" id="avatar_upload" role="form" method="POST" enctype="multipart/form-data" action="{{ secure_url('profile/changeAvatar') }}" data-parsley-validate>
                                                        {{ csrf_field() }}
                                                        <input id="input-1" type="file" class="file" name="image">
                                                    </form>
                                                </div>
                                            </div>

                                            <div class="row margin-top-30 country-type">
                                                <div class="col-md-8">
                                                    <label>Country: </label><span></span>
                                                </div>
                                            </div>
                                        
                                            <div class="row">
                                                <div class="col-md-8">
                                                    <form class="form-horizontal" id="user_country" role="form" method="POST" action="{{ secure_url('profile/userCountry') }}">
                                                        {{ csrf_field() }}
                                                        <div class="country-box">
                                                            <div class="bfh-selectbox bfh-countries" data-country="{{ $user->country }}" data-name="country" data-flags="true">
                                                                <a class="bfh-selectbox-toggle" role="button" data-toggle="bfh-selectbox" href="#">
                                                                    <span class="bfh-selectbox-option input-medium" data-option=""></span>
                                                                    <b class="caret"></b>
                                                                </a>
                                                                <div class="bfh-selectbox-options">
                                                                    <div role="listbox">
                                                                        <ul role="option"></ul>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="form-group margin-top-30">
                                                            <button type="submit" class="btn btn-rss">Submit</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if(!Auth::user()->social_login)
                        <div class="panel panel-rss">
                            <div class="panel-heading">Change Password</div>
                            @if (session('done'))
                                <div class="alert alert-success" style="margin: 10px;">
                                    {{ session('done') }}
                                </div>
                            @endif
                            @if (session('info'))
                                <div class="alert alert-info" style="margin: 10px;">
                                    {{ session('info') }}
                                </div>
                            @endif
                            @if (count($errors) > 0)
                                <div class="alert alert-danger">
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            @if (session('error'))
                                <div class="alert alert-danger" style="margin: 10px;">
                                    {{ session('error') }}
                                </div>
                            @endif
                            <div class="panel-body">

                                {{--<h4>Change Password</h4>
                                <br/>--}}
                                <div class="row">
                                    <div class="col-md-12">
                                        <form class="form-horizontal" id="changePasswordForm" role="form" method="POST"
                                              action="{{ secure_url('profile/changePassword') }}" data-parsley-validate>
                                            {{ csrf_field() }}
                                            <div class="row">
                                                <div class="col-md-9">
                                                    <div class="row">
                                                        <label for="current-password" class="col-sm-4 control-label">Current
                                                            Password</label>
                                                        <div class="col-sm-8">
                                                            <div class="form-group">
                                                                <input type="password" class="form-control" id="current-password"
                                                                       name="current-password" placeholder="Password">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <label for="password" class="col-sm-4 control-label">New Password</label>
                                                        <div class="col-sm-8">
                                                            <div class="form-group">
                                                                <input type="password" class="form-control" id="password"
                                                                       name="password"
                                                                       placeholder="Password"
                                                                       pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}"
                                                                       title="Must contain at least one number and one uppercase and lowercase letter, and at least 6 or more characters"
                                                                       required>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <label for="password_confirmation" class="col-sm-4 control-label">Re-enter
                                                            Password</label>
                                                        <div class="col-sm-8">
                                                            <div class="form-group">
                                                                <input type="password" class="form-control" id="password_confirmation"
                                                                       name="password_confirmation" placeholder="Re-enter Password"
                                                                       required>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="form-group">
                                                    <div class="col-sm-offset-5 col-sm-6">
                                                        <button type="submit" class="btn btn-rss">Submit</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif


                    <div class="panel panel-rss">
                        <div class="panel-heading">Change Your Email Address and Verify</div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <form class="form-horizontal" role="form" method="POST"
                                          action="{{ secure_url('profile/changeEmailAddress') }}" data-parsley-validate>
                                        {{ csrf_field() }}
                                        <div class="row">
                                            <div class="col-md-9">
                                                <div class="row">
                                                    <label for="email" class="col-sm-4 control-label">Email Address</label>
                                                    <div class="col-sm-8">
                                                        <div class="form-group">
                                                            <input type="email" class="form-control" id="email"
                                                                   name="email" placeholder="Email" required value="@if(Auth::user()->email) {{Auth::user()->email}} @endif">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group">
                                                <div class="col-sm-offset-5 col-sm-6">
                                                    <button type="submit" class="btn btn-rss">Submit</button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="clearfix"></div>
    </div>
@endsection
@section('scripts')
    <script>
        function notificationAction(el) {
            console.log(el);
            $.post(
                    '{{ secure_url('profile/notificationAction') }}',
                    {"_token": "{{ csrf_token() }}", "id": el, "action": 1},
                    function (data, status) {
                        console.log(data)
                    });
        }
    </script>
@endsection