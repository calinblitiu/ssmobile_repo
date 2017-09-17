@extends('master')
@section('title','User Public profile - ')
@section('content')

<div class="row">
    <div class="col-md-offset-2 col-md-8">
        <div class="row panel panel-rss">
            <div class="panel-heading">Public Profile Info</div>
            <div class="panel-body">
                <div class="col-md-5">
                    <div class="user-image">
                        @if (file_exists('images/avatar' . '/' . $user->id . '.jpg'))
                            <img src="{{ secure_url('images/avatar') . '/' . $user->id . '.jpg' }}">
                        @else
                            <img src="{{ secure_url('images/noimage/no-image.png') }}">
                        @endif
                    </div>
                </div>
                <div class="col-md-7">
                    <div class="row">
                        <h2>{{ $user->name }}</h2>
                    </div>
                    <div class="row">{{ $user->country }}</div>
                    <div class="row">
                        <div class="comment-count">
                            {{ $user_comment_count }} <span>post</span>
                        </div>
                        <div class="comment-star"></div>
                    </div>
                    @if(Auth::check())
                    <div class="row">
                        <form class="form-horizontal" id="user_message" role="form" method="POST" action="{{ secure_url('profile/messages/sendPrivateMessage') }}" data-parsley-validate>
                            {{ csrf_field() }}
                            <input type="hidden" name="to" value="{{$user->id}}">
                            <div class="form-group">
                                <label>Send Private Message</label>
                                <input id="input-1" type="text"  name="body">
                            </div>
                            <div class="form-group">
                                <button type="submit" class="btn btn-rss">Send</button>
                            </div>
                        </form>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

<style>
    .panel-body .user-image img {
        max-width: 250px;
        width: 100%;
        border-radius: 50%;
    }
</style>
