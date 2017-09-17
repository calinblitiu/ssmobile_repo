@extends('admin.master')
@section('title','User messages |')
@section('contentHeader')
  <h1>
    User Messages
  </h1>
@endsection

@section('content')
<link href="{{ secure_asset('css/semantic.min.css') }}" rel="stylesheet">
  <div class="row">
    <div class="col-md-12">
      <div class="row">
        <div class="col-md-12">
          <div class="panel panel-rss">
            <div class="panel-heading">Messages</div>
            <div class="panel-body">
        <div class="row clearfix" style="margin-top: 20px;">
                <table class="table table-hover table-striped">
                  <tr>
                    <th widht=5%>ID</th>
                    <th widht=20%>From</th>
                    <th width=20%>Subject</th>
                    <th width=35%>Body</th>
                    <th width=20%>Date</th>
                    <th width=7%></th>
                  </tr>

            <?php $i = 1; ?>
                  @foreach($messages as $message)
                    @if ( $message->action == 0 )
                      <?php $unread = "unread"; ?>
                    @else
                      <?php $unread = ""; ?>
                    @endif
                    <tr class="{{ $unread }}">
                      <td> {{ $i++ }} </td>
                      <td> {{ $message->username }}</td>
                      <td> {{ $message->title }} </td>
                      <td style="cursor:pointer;">
                        <div data-toggle="tooltip" data-placement="top" data-original-title="{{ $message->message }}">
                        <?php
                          echo str_replace('<br>', " ", nl2br( mb_substr( $message->message, 0, 50 )));
                          if( strlen($message->message) > 51 )
                            echo "...";
                        ?>
                        </div>
                      </td>
                      <td> {{ $message->created_at }} </td>
                      <td>
                        @if( $unread )
                        <a href="javascript:void(0)" onclick="markAsRead(this, {{ $message->id }})" title="Mark as Read">
                          <i class="fa fa-check-square-o"></i>
                        </a>
                        @endif
                        @if( $message->type == 1 )
                        <a href="javascript:void(0)" onclick="reply(this, {{ $message->id }})" title="Reply">
                          <i class="fa fa-mail-reply"></i>
                        </a>
                        @endif
                      </td>
                    </tr>
                  @endforeach
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="clearfix"></div>
  </div>
@endsection
@section('footerScripts')
  <script src="{{ secure_asset('js/semantic.min.js') }}"></script>
  <script src="//cdn.jsdelivr.net/alertifyjs/1.9.0/alertify.min.js"></script>

  <!-- CSS -->
  <link rel="stylesheet" href="//cdn.jsdelivr.net/alertifyjs/1.9.0/css/alertify.min.css"/>
  <link rel="stylesheet" href="//cdn.jsdelivr.net/alertifyjs/1.9.0/css/alertify.min.css"/>
  <link rel="stylesheet" href="//cdn.jsdelivr.net/alertifyjs/1.9.0/css/themes/default.min.css"/>
  <link rel="stylesheet" href="//cdn.jsdelivr.net/alertifyjs/1.9.0/css/themes/bootstrap.min.css"/>

  <script>
    $(function () {
      $('[data-toggle="tooltip"]').tooltip();
    })

    function markAsRead( el, messageId )
    {
      $.ajax({
          url: "/profile/messages/read",
          type: "post",
          data: {"_token": "{{ csrf_token() }}", "messageId": messageId },
          cache: false,
          success: function (data) {
            swal({title: "Done", type: "success"});
            $(el).parent().parent().removeClass('unread');
            $(el).html('');
          }
        });

    }
    function markAsRead_unshow( el, messageId )
    {
      $.ajax({
          url: "/profile/messages/read",
          type: "post",
          data: {"_token": "{{ csrf_token() }}", "messageId": messageId },
          cache: false,
          success: function (data) {
          }
        });

    }

    function reply( el, messageId )
    {
      alertify.prompt('Reply', 'Reply to this user.', ''
        , function (evt, value) {
          console.log( value );
          $.post(
            '{{ url('/profile/messages/reply') }}',
            {"_token": "{{ csrf_token() }}", "messageId": messageId, "body": value},
            function (data, status) {
              console.log(data)
            alertify.success('Message sent to admin')
            });
        }
        , function () {
          alertify.error('Cancel')
        });
    }

  </script>
@endsection

<style>
  .unread{ background-color: #d9edf7 !important; color: #31708f;}
</style>
