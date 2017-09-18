@extends('admin.master')
@section('title','Approve Stream |')
@section('contentHeader')
  <h1>Approve streams</h1>
@endsection

@section('content')
  <!-- Default box -->
  <div class="box">
    <div class="box-header with-border">
      <h3 class="box-title">Streams waiting for approval</h3>
      <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse">
          <i class="fa fa-minus"></i></button>
        <button type="button" class="btn btn-box-tool" data-widget="remove" data-toggle="tooltip" title="Remove">
          <i class="fa fa-times"></i></button>
      </div>
    </div>
    <div class="box-body">
      
      <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
        @foreach($events as $event)
          <div class="panel panel-info">
            <div class="panel-heading" role="tab" id="heading_{{ $event->event_id }}">
              <h4 class="panel-title">
                <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse_{{ $event->event_id }}" aria-expanded="true"
                   aria-controls="collapse_{{ $event->event_id }}">
                  {{ $event->home_team.' vs '.$event->away_team }} [ <span class="text-red">{{ $event->s_count }}</span> ]
                </a>
              </h4>
            </div>
            <div id="collapse_{{ $event->event_id }}" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading_{{ $event->event_id }}">
              <div class="panel-body">
                <table class="table table-responsive">
                  <tr>
                    <th>Event ID</th>
                    <td>{{ $event->event_id }}</td>
                    <th>Event time (UTC)</th>
                    <td>{{ $event->start_date }}</td>
                    <th>Home team</th>
                    <td>{{ $event->home_team }}</td>
                    <th>Away team</th>
                    <td>{{ $event->away_team }}</td>
                  </tr>
                </table>
                
                <table class="table table-bordered table-hover table-responsive datatables" id="stream_{{ $event->event_id }}">
                  <thead>
                  <tr>
                    <th>URL</th>
                    <th>Domian</th>
                    <th>User</th>
                    <th>Lang</th>
                    <th>Q</th>
                    <th>Type</th>
                    <th>M</th>
                    <th>Pixel</th>
                    <th>Ads</th>
                    <th>NSFW</th>
                    <th>OtherInfo</th>
                    <th>Action</th>
                  </tr>
                  </thead>
                  <tbody>
                  <?php
                  $streams = DB::table('streams AS s')
                    ->leftJoin('users AS u', 'u.id', 's.user_id')
                    ->where('s.event_id', $event->event_id)
                    ->where('s.approved',0)
                    ->select('s.*', 'u.name')
                    ->get();
                  ?>
                  @foreach( $streams as $stream)
                    <tr>
                      <td><a href="{{ $stream->url }}" target="_blank">URL</a></td>
                      <td><a href="{{ $stream->domain }}" target="_blank">Domain</a> </td>
                      <td>{{ $stream->name }}</td>
                      <td>{{ $stream->language }}</td>
                      <td>{{ $stream->quality }}</td>
                      <td>{{ $stream->stream_type }}</td>
                      <td>{{ $stream->compatibility }}</td>
                      <td>{{ $stream->pixel_ratio }}</td>
                      <td>{{ $stream->ad_number }}</td>
                      <td>{{ $stream->nsfw }}</td>
                      <td>{{ $stream->other_info }}</td>
                      <td>
                        <button data-href="{{ secure_url('moderator/stream/approve/'.$stream->stream_id) }}" class="btn btn-success" data-toggle="confirmation" data-singleton="true"
                                onclick="streamAction(this,'{{ $stream->stream_id }}');">
                          <i class="fa fa-check-square-o" aria-hidden="true"></i>
                        </button>
                        <button data-href="{{ secure_url('moderator/stream/delete/'.$stream->stream_id) }}" class="btn btn-danger" data-toggle="confirmation" data-singleton="true"
                                onclick="streamAction(this,'{{ $stream->stream_id }}');">
                          <i class="fa fa-trash" aria-hidden="true"></i>
                        </button>
                    </tr>
                  @endforeach
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        @endforeach
      </div>
    </div>
    <!-- /.box-body -->
    <div class="box-footer">
    </div>
    <!-- /.box-footer-->
  </div>
  <!-- /.box -->
@endsection

@section('footerScripts')
  {{--<link rel="stylesheet" type="text/css" href="//cdn.datatables.net/v/bs-3.3.7/dt-1.10.13/fc-3.2.2/fh-3.1.2/r-2.1.0/datatables.min.css"/>--}}
  {{--<script type="text/javascript" src="//cdn.datatables.net/v/bs-3.3.7/dt-1.10.13/fc-3.2.2/fh-3.1.2/r-2.1.0/datatables.min.js"></script>--}}
  <link rel="stylesheet" type="text/css" href="{{ secure_asset('css/datatables.min.css') }}">
  <script type="text/javascript" src="{{secure_asset('js/datatables.min.js')}}"></script>
  <script src="{{ secure_url('js/jquery.popconfirm.min.js') }}" type="text/javascript"></script>
  <script>
    $(function ($) {
      $('.datatables').DataTable({
        searching: false,
        columnDefs: [
          {targets: [0, 10], orderable: false},
          {
            targets: 9, render: function (data, type, row) {
            if (data == '1') {
              return 'Yes';
            } else {
              return 'No';
            }
          }
          }
        ]
      });
      
      $('[data-toggle=confirmation]').popConfirm({
        placement: "left"
      });
    });
    
    function streamAction(el, streamId) {
      $.post(
        el.attr('data-href'),
        {"_token": "{{ csrf_token() }}"},
        function (data, status) {
          console.log(data)
        });
      el.closest('tr').slideUp('slow');
    }
  </script>
@endsection