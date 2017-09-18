@extends('admin.master')
@section('title','Approve Stream |')
@section('contentHeader')
  <h1>Stream Evaluations</h1>
@endsection

@section('content')
  <!-- Default box -->
  <div class="box">
    <div class="box-header with-border">
      <h3 class="box-title">Streams Evaluations</h3>
      <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse">
          <i class="fa fa-minus"></i></button>
        <button type="button" class="btn btn-box-tool" data-widget="remove" data-toggle="tooltip" title="Remove">
          <i class="fa fa-times"></i></button>
      </div>
    </div>
    <div class="box-body">
      <table class="table table-responsive table-hover table-bordered">
        <thead>
        <tr>
          <th>Date</th>
          <th>User</th>
          <th>Note</th>
        </tr>
        </thead>
        <tbody>
        @foreach($reports as $report)
          <tr>
            <td>{{ $report->created_at }}</td>
            <td>{{ $report->users->name }}</td>
            <td>{{ $report->comment }}</td>
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
  {{--<link rel="stylesheet" type="text/css" href="//cdn.datatables.net/v/bs-3.3.7/dt-1.10.13/fc-3.2.2/fh-3.1.2/r-2.1.0/datatables.min.css"/>--}}
  {{--<script type="text/javascript" src="//cdn.datatables.net/v/bs-3.3.7/dt-1.10.13/fc-3.2.2/fh-3.1.2/r-2.1.0/datatables.min.js"></script>--}}
  <link rel="stylesheet" type="text/css" href="{{ secure_asset('css/datatables.min.css') }}">
  <script type="text/javascript" src="{{secure_asset('js/datatables.min.js')}}"></script>
  <script src="{{ secure_asset('js/jquery.popconfirm.min.js') }}" type="text/javascript"></script>
@endsection