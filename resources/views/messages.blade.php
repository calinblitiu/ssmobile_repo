@extends('master')
@section('title','User profile - ')
@section('content')
<link href="{{ secure_asset('css/semantic.min.css') }}" rel="stylesheet">
  <div class="row">
    @include('userMenu')
    <div class="col-md-9">
      <div class="row">
        <div class="col-md-12">
          <div class="panel panel-rss">
            <div class="panel-heading">Messages</div>
            <div class="panel-body">
				<div class="row clearfix" style="margin-top: 20px;">
	            	<table class="table table-hover table-striped table-bordered">
	            		<tr>
	            			<th>No</th>
	            			<th>From</th>
	            			<th class="mobile-view-message-hide">Subject</th>
	            			<th class="mobile-view-message-hide">Body</th>
	            			<th class="mobile-view-message-date">Date</th>
	            			<th></th>
	            		</tr>

						<?php $i = 1; ?>
						@if( count($messages) == 0 )
							<td colspan="6" align="center"> No message </td>
						@endif
	            		@foreach($messages as $message)
	            			@if( $message->type==1 || $message->type==2)
		            			@if ( $message->action == 0 )
		            				<?php $unread = "unread"; ?>
		            			@else
		            				<?php $unread = ""; ?>
		            			@endif
		            			<tr class="{{ $unread }}">
                        <td>
                            <a href="<?php echo $message->link . '#' . $message->comment_id ?>" onclick="markAsRead_unshow(this, {{ $message->id }})">{{ $i++ }}</a>
                        </td>
	            				  <td>
                            <a href="<?php echo $message->link . '#' . $message->comment_id ?>" onclick="markAsRead_unshow(this, {{ $message->id }})">{{ $message->username }}</a>
                        </td>
		            				<td class="mobile-view-message-hide">
                            <a href="<?php echo $message->link . '#' . $message->comment_id ?>" onclick="markAsRead_unshow(this, {{ $message->id }})">{{ $message->title }}</a>
                        </td>
		            				<td class="mobile-view-message-hide" style="cursor:pointer;">
                            <a href="<?php echo $message->link . '#' . $message->comment_id ?>" onclick="markAsRead_unshow(this, {{ $message->id }})">
      		            					<?php
      		            						echo str_replace('<br>', " ", nl2br( mb_substr( $message->message, 0, 50 )));
      		            						if( strlen($message->message) > 51 )
      		            							echo "...";
      		            					?>
                            </a>
		            				</td>
		            				<td>
                            <a href="<?php echo $message->link . '#' . $message->comment_id ?>" onclick="markAsRead_unshow(this, {{ $message->id }})">{{ $message->created_at }}</a>
                        </td>
		            				<td><a href="<?php echo $message->link . '#' . $message->comment_id ?>">
		            					@if( $unread )
		            					<a href="javascript:void(0)" onclick="markAsRead(this, {{ $message->id }})" title="Mark as Read">
		            						<i class="fa fa-check-square-o"></i>
		            					</a>
		            					@endif
                          @if( $message->type == 2 )
                            <a href="<?php echo '/publicProfile/' . $message->actor_id ?>" title="Reply">
                              <i class="fa fa-mail-reply"></i>
                            </a>
                            <!-- <a href="javascript:void(0)" onclick="replyPrivate(this, {{ $message->id }}, {{ $message->actor_id }})" title="Reply">
                              <i class="fa fa-mail-reply"></i>
                            </a> -->
                          @endif
		            				</td>
		            			</tr>
		            		@endif
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
@section('scripts')
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

    function reply( el, messageId )
    {
		alertify.prompt('Reply', 'You can reply to admin.', ''
        , function (evt, value) {
        	if( !value ){
				alertify.error('Empty message.');
        		return;
        	}
          $.post(
            '{{ secure_url('/profile/messages/reply') }}',
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
