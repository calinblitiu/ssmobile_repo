@extends('admin.master')
@section('title','Broadcast a notification |')
@section('contentHeader')
  <h1>
    Broadcast a notification
  </h1>
@endsection

@section('content')
<style type="text/css">
  label{ margin-right:10px; }
  #preview_message{
    min-height:30px;
    padding: 15px; 
    margin-bottom:20px; 
    border:1px solid; 
    border-radius: 4px;
    background: #D9EDF7;
    color: #31708F;
    border-color: #BCE8F1;
  }
  .blurry{
    display: none;
    height:100%;
    width:100%;
    background-color:#333;
    margin:auto;
    position:fixed;
    -webkit-filter: blur(3px);
    -moz-filter: blur(3px);
    -o-filter: blur(3px);
    -ms-filter: blur(3px);
    filter: blur(3px);
    z-index:1000;
    opacity:0.5;
  }
  .loader {
      border: 16px solid #f3f3f3; /* Light grey */
      border-top: 16px solid #3498db; /* Blue */
      border-radius: 50%;
      width: 120px;
      height: 120px;
      animation: spin 2s linear infinite;
      position: fixed;
      z-index: 999;
      overflow: show;
      margin: auto;
      top: 0;
      left: 0;
      bottom: 0;
      right: 0;
  }

  @keyframes spin {
      0% { transform: rotate(0deg); }
      100% { transform: rotate(360deg); }
  }
</style>
  <!-- Default box -->
  <div class="box">
    <div class="box-header with-border">
      <h3 class="box-title">Notification details</h3>
      <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse">
          <i class="fa fa-minus"></i></button>
        <button type="button" class="btn btn-box-tool" data-widget="remove" data-toggle="tooltip" title="Remove">
          <i class="fa fa-times"></i></button>
      </div>
    </div>
    <div class="box-body" style="background: #eee; margin:10px;">
      <!--form action="{{ secure_url('moderator/user/sendBroadcast') }}" method="post"-->
        {{ csrf_field() }}
        <div class="form-group">
          <label>Message</label>
          <textarea id="notify_title" type="text" class="form-control editor" name="title" placeholder="Message" required maxlength="190"></textarea>
        </div>

        <div class="col-md-12">
          <div class="row">
            <div class="col-md-3">
              <div class="form-group">
                <label>User Group</label>
                <select id="notify_group">
                  <option value=0>All</option>
                  <option value=1>Verified User</option>
                  <option value=2>Unverified User</option>
                  <option value=3>Banned User</option>
                </select>
              </div>
            </div>
            <div class="col-md-8">
              <div class="form-group">
                <label>Allow user to close</label>
                <input type="checkbox" id="notify_type" name="notify_type">
              </div>
            </div>
          </div>
          <div class="col-md-12">
            <div class="form-group">
                <div class="col-md-4">
                  <div class="col-md-6">
                    <button id="bg_selector" class="jscolor {valueElement:'bgcolor-value', onFineChange:'setTextColor()'}">
                      Background
                    </button>
                  </div>
                  <div class="col-md-6">
                      <input id="bgcolor-value" name='bgcolor' value="d9edf7">
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="col-md-6">
                    <button id="color_selector" class="jscolor {valueElement:'txtcolor-value', onFineChange:'setTextColor()'}">
                      Text color
                    </button>
                  </div>
                  <div class="col-md-6">
                      <input id="txtcolor-value" name='txtcolor' value="31708f">
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="col-md-6">
                    <button id="border_selector" class="jscolor {valueElement:'bordercolor-value', onFineChange:'setTextColor()'}">
                      Border color
                    </button>
                  </div>
                  <div class="col-md-6">
                      <input id="bordercolor-value" name='bordercolor' value="bce8f1">
                  </div>
                </div>
            </div>
          </div>
        </div>

        <div class="form-group" style="display:none;">
          <label>Message</label>
          <textarea id="notify_body" class="form-control editor" name="body" rows="3" placeholder="add body text" disabled></textarea>
        </div>
        <div>
          <label>Preview</label>
          <div id="preview_message">
            Message
          </div>
        </div>
        <div class="form-group">
          <label></label>
          <button type="button" class="btn btn-primary" onclick="openNotification(this)">Broadcast</button>
        </div>
      </form>
    </div>
    <div class="col-md-12">
      <div class="panel panel-rss">
        <div class="panel-body">
          <div class="row clearfix" style="margin-top: 20px;">
            <table class="table table-hover table-striped">
              <thead>
                <th widht=5%>ID</th>
                <th width=65%>Subject</th>
                <th width=20%>Date</th>
                <th width=5%>Type</th>
                <th width=5%></th>
              </thead>

          <?php $i = 1; ?>
              @foreach($messages as $message)
                <tr>
                  <td> {{ $i++ }} </td>
                  <td  style="word-break: break-all; word-wrap: break-word;"> {!! $message->title !!} </td>
                  <td> {{ $message->created_at }} </td>
                  <td> @if( $message->type == 2 ) Allowed @else Static @endif</td>
                  <td> <a href="#" onclick="deleteBroad(this, {{ $message->id }})" data-href={{ secure_url('moderator/user/delBroadcast') }}><i class="fa fa-trash"></i></a></td>
                </tr>
              @endforeach
            </table>
          </div>
        </div>
      </div>
    </div>


    <!-- /.box-body -->
    <div class="box-footer"></div>
    <!-- /.box-footer-->
  </div>
  <!-- /.box -->
@endsection
<div class="blurry">
  <div class="loader"></div>
</div>
@section('footerScripts')
  <script src="//cdn.jsdelivr.net/alertifyjs/1.9.0/alertify.min.js"></script>
  <script src="{{ secure_asset('js/jscolor.js') }}"></script>
  
  <!-- CSS -->
  <link rel="stylesheet" href="//cdn.jsdelivr.net/alertifyjs/1.9.0/css/alertify.min.css"/>
  <script>
    function setTextColor() {
        bgcolor = '#' + $('#bgcolor-value').val().toString();
        $('#bgcolor-value').css( 'color', bgcolor );
        txtcolor = '#' + $('#txtcolor-value').val().toString();
        $('#txtcolor-value').css( 'color', txtcolor );
        border_selector = '#' + $('#bordercolor-value').val().toString();
        $('#bordercolor-value').css( 'color', border_selector );

        $('#preview_message').css( {'color': txtcolor, 'background':bgcolor, 'border-color': border_selector });

        $('#preview_message').html( CKEDITOR.instances['notify_title'].getData() );
    }

    function openNotification( ev ) {
      title = CKEDITOR.instances['notify_title'].getData();
      body  = $('#notify_body').val();
      group = $('#notify_group').val();
      type  = $('#notify_type').is(':checked') ? 2 : 3;
        bgcolor = '#' + $('#bgcolor-value').val().toString();
        txtcolor = '#' + $('#txtcolor-value').val().toString();
        border_selector = '#' + $('#bordercolor-value').val().toString();

      color_info = { "bgcolor": bgcolor, "txtcolor":txtcolor, "border_selector": border_selector };

      if( title == "" ){
          alertify.error('Fill out fields.');
          return;
      }

      $(".blurry").show();
      $.post(
        '{{ secure_url('moderator/user/sendBroadcast') }}',
        {"_token": "{{ csrf_token() }}", "title": title, "body": body, "group": group, "type": type, "colorInfo": color_info },
        function (data, status) {
          console.log( data );
          $('#notify_title').val('');
          $('#notify_body').val('');
          $(".blurry").hide();
          alertify.success('Notification has been broadcasted to verified users');
        });
    }

    function deleteBroad(el, messageId)
    {
        swal({
          title: "Are you sure?",
          text: "",
          type: "warning",
          showCancelButton: true,
          confirmButtonColor: "#DD6B55",
          confirmButtonText: "Yes, delete it!",
          closeOnConfirm: false
        },
        function () {
          $.post(
            $(el).attr('data-href'),
            {"_token": "{{ csrf_token() }}", id: messageId },
            function (data, status) {
              console.log(data)
            });
          $(el).closest('tr').slideUp('slow');
          swal("Deleted!", "Broadcast has been deleted.", "success");
        });    
    }
  </script>
  <script src="//cdn.ckeditor.com/4.6.2/full/ckeditor.js"></script>
  
  <script>
    $(function($){
      CKEDITOR.replace( 'notify_title' );
    });
    
  </script>
@endsection