@extends('admin.master')
@section('title','Dashboard |')
@section('contentHeader')
  <h1>Activity log</h1>
@endsection

@section('content')
  <!-- Default box -->
  <div class="box">
    <div class="box-header with-border">
      <h3 class="box-title">System activity log</h3>
      <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse">
          <i class="fa fa-minus"></i></button>
        <button type="button" class="btn btn-box-tool" data-widget="remove" data-toggle="tooltip" title="Remove">
          <i class="fa fa-times"></i></button>
      </div>
    </div>
    <div class="box-body">
      <table class="table table-bordered table-responsive table-hover">
        <thead>
        <tr>
          <th width="20%">Time</th>
          <th width="20%">Actor</th>
          <th>Action</th>
          <th width="10%">SQLType</th>
        </tr>
        </thead>
        <tbody>
        @foreach($logs as $log)
          <tr>
            <td>{{ $log->created_at }}</td>
            <td>{{ $log->actorName->name }}</td>
            <td>{{ $log->action }}</td>
            <td>
              @if($log->sql_type==2)
                Approve
              @elseif($log->sql_type==3)
                Delete
              @else
                UNKNOWN
              @endif
            </td>
          </tr>
        @endforeach
        </tbody>
      </table>
    </div>
    <!-- /.box-body -->
    <div class="box-footer"></div>
    <!-- /.box-footer-->
  </div>
  <!-- /.box -->
@endsection