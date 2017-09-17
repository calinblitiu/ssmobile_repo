@extends('admin.master')
@section('title','Channels |')
@section('contentHeader')
  <h1>Channels List</h1>
@endsection

@section('content')
  <!-- Default box -->
  <div class="box">
    <div class="box-header with-border">
      <h3 class="box-title">Channels published</h3>
      <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse">
          <i class="fa fa-minus"></i></button>
        <button type="button" class="btn btn-box-tool" data-widget="remove" data-toggle="tooltip" title="Remove">
          <i class="fa fa-times"></i></button>
      </div>
    </div>
    <div class="box-body">
      <table class="table table-hover table-bordered table-responsive">
        <thead>
        <tr>
          <th>ID</th>
          <th width="40%">Channel Name</th>
          <th>Description</th>
          <th>Acquire rights</th>
          <th><i class="fa fa-pencil-square-o" aria-hidden="true"></i></th>
          <th>Action</th>
        </tr>
        </thead>
        <tbody>
        @foreach($allChannels as $channel)
          <tr>
            <td>{{ $channel->channel_id }}</td>
            <td>{{ $channel->channel_name }}</td>
            {{-- <td>{{ $channel->description }}</td> --}}
            {{-- <td>{{ $channel->acquire_rights }}</td> --}}
            <form action="{{ secure_url('moderator/channel/updateChannelDate/'.$channel->channel_id) }}" method="POST">
              {{ csrf_field() }}
              <td>
                <div class='input-group'>
                  <input type='text' class="form-control" name="description" required value="{{ $channel->description }}" />
                </div>
              </td>
              <td>
                <div class='input-group'>
                  <input type='text' class="form-control" name="acquire_rights" required value="{{ $channel->acquire_rights }}" />
                </div>
              </td>
              <td>
                <button class="btn btn-default" type="submit"><i class="fa fa-floppy-o" aria-hidden="true"></i></button>
              </td>
            </form>
            <td>
              <a href="javascript:void(0);" class="btn btn-danger" onclick="deleteEvent(this);" data-href="{{ secure_url('moderator/channel/delete/'.$channel->channel_id) }}">Delete</a>
            </td>
          </tr>
        @endforeach
        </tbody>
      </table>
    </div>
    <!-- /.box-body -->
    <div class="box-footer">
    </div>
    <!-- /.box-footer-->
  </div>
  <!-- /.box -->
@endsection

@section('footerScripts')
  <link rel="stylesheet" type="text/css" href="//cdn.datatables.net/v/bs-3.3.7/dt-1.10.13/fc-3.2.2/fh-3.1.2/r-2.1.0/datatables.min.css"/>
  <script type="text/javascript" src="//cdn.datatables.net/v/bs-3.3.7/dt-1.10.13/fc-3.2.2/fh-3.1.2/r-2.1.0/datatables.min.js"></script>
  <script src="{{ secure_asset('js/jquery.popconfirm.js') }}" type="text/javascript"></script>

  <script src="{{ secure_asset('js/moment.js') }}"></script>
  <script src="{{ secure_asset('plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js') }}"></script>
  <link rel="stylesheet" href="{{ secure_asset('plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css') }}">

  <script>
    // $('.datetimepicker').datetimepicker({
    //   'format':'YYYY-MM-DD HH:mm'
    // });
    // $(function ($) {
    //   $('.datatables').DataTable({
    //     searching: false,
    //     columnDefs: [
    //       {targets: [0, 10], orderable: false},
    //       {
    //         targets: 9, render: function (data, type, row) {
    //         if (data == '1') {
    //           return 'Yes';
    //         } else {
    //           return 'No';
    //         }
    //       }
    //       }
    //     ]
    //   });
      
    //   $('[data-toggle=confirmation]').popConfirm({
    //     placement: "left"
    //   });
    // });
    
    function deleteEvent(el) {
      swal({
          title: "Are you sure?",
          text: "You will not be able to recover this event!",
          type: "warning",
          showCancelButton: true,
          confirmButtonColor: "#DD6B55",
          confirmButtonText: "Yes, delete it!",
          closeOnConfirm: false
        },
        function () {
          $.post(
            $(el).attr('data-href'),
            {"_token": "{{ csrf_token() }}"},
            function (data, status) {
              console.log(data)
            });
          $(el).closest('tr').slideUp('slow');
          swal("Deleted!", "Channel has been deleted.", "success");
        });
    }
  </script>
@endsection