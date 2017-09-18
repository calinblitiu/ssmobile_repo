@extends('admin.master')
@section('title','Approve Stream |')
@section('contentHeader')
  <h1>Approve streams</h1>
@endsection

@section('content')

<style>
    .recommend { color: red; }
    .highlight{ background: #c9f7e2 !important; }
    .highlight .recommend { color:green; }
</style>
  <!-- Default box -->
  <div class="box">
    <div class="box-header with-border">
      <h3 class="box-title">Streams approved and published</h3>
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
                    <th>Rating</th>
                    <th>Domian</th>
                    <th>User</th>
                    <th>Lang</th>
                    <th>Q</th>
                    <th>Type</th>
                    <th>M</th>
                    <th>Pixel</th>
                    <th>Ads</th>
                    <th>NSFW</th>
                    <th>Action</th>
                  </tr>
                  </thead>
                  <tbody>
                  <?php
                  $streams = DB::table('streams AS s')
                    ->leftJoin('users AS u', 'u.id', 's.user_id')
                    ->where('s.event_id', $event->event_id)
                    ->where('s.approved', 1)
                    ->select('s.*', 'u.name')
                    ->get();
                  ?>

                  @foreach( $streams as $stream)
                    <?php
                      if( $stream->mod_recommended )
                        $highlight = "highlight";
                      else
                        $highlight = "";
                    ?>
                    <tr class="{{ $highlight }}">
                      <td><a href="{{ $stream->url }}" target="_blank">URL</a></td>
                      <td>
                        <span>
                          
                          <i class="fa fa-thumbs-up text-success"
                             aria-hidden="true"></i>{{ \App\Evaluation::where(['stream_id' => $stream->stream_id, 'eval_type' => 1])->count() }}
                          
                        </span> :
                        <span>
                          <a href="{{ secure_url('moderator/stream/reports/'.$stream->stream_id) }}">
                          <i class="fa fa-exclamation-triangle text-danger"
                             aria-hidden="true">
                            
                          </i>
                            {{ \App\Evaluation::where(['stream_id' => $stream->stream_id, 'eval_type' => 0])->count() }}
                            </a>
                        </span>
                      </td>
                      <td>
                          @if( $stream->stream_type == "Acestream" || $stream->stream_type == 'sopcast')
                            {{$stream->stream_type}}
                          @else
                            {{ $stream->domain }}
                            <a href="javascript:void(0);" onclick="banDomainAction(this,'{{ $stream->stream_id }}')" title="Ban this Domain" data-href="{{ secure_url('moderator/stream/banDomain/'.$stream->stream_id) }}">
                              <i class="fa fa-ban" style="color: red" aria-hidden="true"></i>
                            </a>
                          @endif
                      </td>
                      <td>{{ $stream->name }}</td>
                      <td>{{ $stream->language }}</td>
                      <td>{{ $stream->quality }}</td>
                      <td>{{ $stream->stream_type }}</td>
                      <td>{{ $stream->compatibility }}</td>
                      <td>{{ $stream->ad_number }}</td>
                      <td>{{ $stream->nsfw }}</td>
                      <td>
                        <button data-href="{{ secure_url('moderator/stream/delete/'.$stream->stream_id) }}" class="btn btn-danger"
                                onclick="streamAction(this,'{{ $stream->stream_id }}');">
                          <i class="fa fa-trash" aria-hidden="true"></i>
                        </button>
                      </td>
                      <td>
                        <a href="javascript:void(0);" onclick="recommend(this,'{{ $stream->stream_id }}')" title="Recommend">
                          <i class="fa fa-hand-o-up recommend" aria-hidden="true"></i>
                        </a>
                      </td>
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
      swal({
          title: "Are you sure?",
          text: "You will not be able to recover this stream!",
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
          swal("Deleted!", "Stream has been deleted.", "success");
        });
    }

    function recommend(el, stream ){
        var url = "{{ secure_url('recommend') }}";
        $.ajax({
          url: url,
          type: "post",
          data: {"_token": "{{ csrf_token() }}", "stream": stream},
          cache: false,
          success: function (data) {
            var tr = el.parentElement.parentElement;
            swal({title: "Successfully recommended.", type: "success"});
            tr.className += " highlight";
          }
        });
      }

    function banDomainAction(el, streamId) {
      swal({
          title: "Are you sure?",
          text: "",
          type: "warning",
          showCancelButton: true,
          confirmButtonColor: "#DD6B55",
          confirmButtonText: "Yes, Ban it!",
          closeOnConfirm: false
        },
        function () {
          $.get(
            $(el).attr('data-href'),
            {"_token": "{{ csrf_token() }}"},
            function (data, status) {
              console.log(data)
            });
          // $(el).closest('tr').slideUp('slow');
          swal("Banned!", "Successfully", "success");
        });
    }
  </script>
@endsection