@extends('admin.master')
@section('title','[MOD] User list |')
@section('contentHeader')
  <h1>Users</h1>
@endsection

@section('content')
  <!-- Default box -->
  <div class="box collapsed-box box-primary">
    <div class="box-header with-border">
      <h3 class="box-title">Verified Users</h3>
      <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse">
          <i class="fa fa-plus"></i></button>
        <button type="button" class="btn btn-box-tool" data-widget="remove" data-toggle="tooltip" title="Remove">
          <i class="fa fa-times"></i></button>
      </div>
    </div>
    <div class="box-body">
      <table class="table table-bordered table-responsive table-hover">
        <thead>
        <th width="10%">ID</th>
        <th>Name</th>
        <th>Email</th>
        <th>Action</th>
        </thead>
        <tbody>
        @foreach($verifiedUsers as $user)
          <tr>
            <td>{{ $user->id }}</td>
            <td>{{ $user->name }}</td>
            <td>{{ $user->email }}</td>
            <td class="text-center">
              <a href="javascript:void(0);" onclick="openNotification('{{ $user->id }}','{{ \Illuminate\Support\Facades\Auth::id() }}')" class="btn btn-success">Notification</a>
              <a class="btn btn-warning" href="{{ secure_url('moderator/user/verify/'.$user->id.'/0') }}">Unverify</a>
              <a class="btn btn-danger" href="{{ secure_url('moderator/user/ban/'.$user->id.'/1') }}">Ban</a>
              @if($user->role!=1 && \Illuminate\Support\Facades\Auth::user()->role>1)
                <a class="btn btn-warning" href="{{ secure_url('moderator/user/sponsor/'.$user->id.'/1') }}">Sponsor</a>
                <a class="btn btn-primary" href="{{ secure_url('moderator/user/moderator/'.$user->id.'/1') }}">Moderator</a>
                <a class="btn btn-primary" href="{{ secure_url('moderator/user/admin/'.$user->id.'/1') }}">Admin</a>
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
  
  <!-- Default box -->
  <div class="box collapsed-box box-warning">
    <div class="box-header with-border">
      <h3 class="box-title">Reqular Users</h3>
      <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse">
          <i class="fa fa-plus"></i></button>
        <button type="button" class="btn btn-box-tool" data-widget="remove" data-toggle="tooltip" title="Remove">
          <i class="fa fa-times"></i></button>
      </div>
    </div>
    <div class="box-body">
      <table class="table table-bordered table-responsive table-hover">
        <thead>
        <th width="10">ID</th>
        <th>Name</th>
        <th>Email</th>
        <th>Action</th>
        </thead>
        <tbody>
        @foreach($regularUsers as $user)
          <tr>
            <td>{{ $user->id }}</td>
            <td>{{ $user->name }}</td>
            <td>{{ $user->email }}</td>
            <td class="text-center">
              <a href="javascript:void(0);" onclick="openNotification('{{ $user->id }}','{{ \Illuminate\Support\Facades\Auth::id() }}')" class="btn btn-success">Notification</a>
              <a class="btn btn-warning" href="{{ secure_url('moderator/user/verify/'.$user->id.'/1') }}">Verify</a>
              @if( $user->approved )
                <a class="btn btn-warning" href="{{ secure_url('moderator/user/approve/'.$user->id.'/0') }}">Unapprove</a>
              @else
                <a class="btn btn-warning" href="{{ secure_url('moderator/user/approve/'.$user->id.'/1') }}">Approve</a>
              @endif
              <a class="btn btn-danger" href="{{ secure_url('moderator/user/ban/'.$user->id.'/1') }}">Ban</a>
              @if($user->role!=1 && \Illuminate\Support\Facades\Auth::user()->role>1)
                <a class="btn btn-primary" href="{{ secure_url('moderator/user/moderator/'.$user->id.'/1') }}">Moderator</a>
                <a class="btn btn-primary" href="{{ secure_url('moderator/user/admin/'.$user->id.'/1') }}">Admin</a>
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
  
  @if(\Illuminate\Support\Facades\Auth::user()->role>1)
    
    <!-- Default box -->
    <div class="box collapsed-box box-success">
      <div class="box-header with-border">
        <h3 class="box-title">Sponsor Users</h3>
        <div class="box-tools pull-right">
          <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse">
            <i class="fa fa-plus"></i></button>
          <button type="button" class="btn btn-box-tool" data-widget="remove" data-toggle="tooltip" title="Remove">
            <i class="fa fa-times"></i></button>
        </div>
      </div>
      <div class="box-body">
        <table class="table table-bordered table-responsive table-hover">
          <thead>
          <th width="10">ID</th>
          <th>Name</th>
          <th>Email</th>
          <th>Action</th>
          </thead>
          <tbody>
          @foreach($sponsorUsers as $user)
            <tr>
              <td>{{ $user->id }}</td>
              <td>{{ $user->name }}</td>
              <td>{{ $user->email }}</td>
              <td class="text-center">
                <a href="javascript:void(0);" onclick="openNotification('{{ $user->id }}','{{ \Illuminate\Support\Facades\Auth::id() }}')" class="btn btn-success">Notification</a>
                <a class="btn btn-warning" href="{{ secure_url('moderator/user/sponsor/'.$user->id.'/0') }}">Not sponsor</a>
                <a class="btn btn-danger" href="{{ secure_url('moderator/user/ban/'.$user->id.'/1') }}">Ban</a>
                <a class="btn btn-primary" href="{{ secure_url('moderator/user/admin/'.$user->id.'/0') }}">Remove</a>
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
    
    <!-- Default box -->
    <div class="box collapsed-box box-success">
      <div class="box-header with-border">
        <h3 class="box-title">Moderator Users</h3>
        <div class="box-tools pull-right">
          <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse">
            <i class="fa fa-plus"></i></button>
          <button type="button" class="btn btn-box-tool" data-widget="remove" data-toggle="tooltip" title="Remove">
            <i class="fa fa-times"></i></button>
        </div>
      </div>
      <div class="box-body">
        <table class="table table-bordered table-responsive table-hover">
          <thead>
          <th width="10">ID</th>
          <th>Name</th>
          <th>Email</th>
          <th>Action</th>
          </thead>
          <tbody>
          @foreach($moderatorUsers as $user)
            <tr>
              <td>{{ $user->id }}</td>
              <td>{{ $user->name }}</td>
              <td>{{ $user->email }}</td>
              <td class="text-center">
                <a href="javascript:void(0);" onclick="openNotification('{{ $user->id }}','{{ \Illuminate\Support\Facades\Auth::id() }}')" class="btn btn-success">Notification</a>
                @if($user->verified!=1)
                  <a class="btn btn-warning" href="{{ secure_url('moderator/user/verify/'.$user->id.'/1') }}">Verify</a>
                @endif
                <a class="btn btn-danger" href="{{ secure_url('moderator/user/ban/'.$user->id.'/1') }}">Ban</a>
                
                <a class="btn btn-primary" href="{{ secure_url('moderator/user/moderator/'.$user->id.'/0') }}">Remove</a>
              
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
    
    <!-- Default box -->
    <div class="box collapsed-box box-success">
      <div class="box-header with-border">
        <h3 class="box-title">Admin Users</h3>
        <div class="box-tools pull-right">
          <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse">
            <i class="fa fa-plus"></i></button>
          <button type="button" class="btn btn-box-tool" data-widget="remove" data-toggle="tooltip" title="Remove">
            <i class="fa fa-times"></i></button>
        </div>
      </div>
      <div class="box-body">
        <table class="table table-bordered table-responsive table-hover">
          <thead>
          <th width="10">ID</th>
          <th>Name</th>
          <th>Email</th>
          <th>Action</th>
          </thead>
          <tbody>
          @foreach($adminUsers as $user)
            <tr>
              <td>{{ $user->id }}</td>
              <td>{{ $user->name }}</td>
              <td>{{ $user->email }}</td>
              <td class="text-center">
                <a href="javascript:void(0);" onclick="openNotification('{{ $user->id }}','{{ \Illuminate\Support\Facades\Auth::id() }}')" class="btn btn-success">Notification</a>
                @if($user->verified!=1)
                  <a class="btn btn-warning" href="{{ secure_url('moderator/user/verify/'.$user->id.'/1') }}">Verify</a>
                @endif
                <a class="btn btn-danger" href="{{ secure_url('moderator/user/ban/'.$user->id.'/1') }}">Ban</a>
                
                <a class="btn btn-primary" href="{{ secure_url('moderator/user/admin/'.$user->id.'/0') }}">Remove</a>
              
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
  @endif
  
  <!-- Default box -->
  <div class="box collapsed-box box-danger">
    <div class="box-header with-border">
      <h3 class="box-title">Ban Users</h3>
      <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse">
          <i class="fa fa-plus"></i></button>
        <button type="button" class="btn btn-box-tool" data-widget="remove" data-toggle="tooltip" title="Remove">
          <i class="fa fa-times"></i></button>
      </div>
    </div>
    <div class="box-body">
      <table class="table table-bordered table-responsive table-hover">
        <thead>
        <th width="10">ID</th>
        <th>Name</th>
        <th>Email</th>
        <th>Action</th>
        </thead>
        <tbody>
        @foreach($banUsers as $user)
          <tr>
            <td>{{ $user->id }}</td>
            <td>{{ $user->name }}</td>
            <td>{{ $user->email }}</td>
            <td class="text-center">
              <a href="javascript:void(0);" onclick="openNotification('{{ $user->id }}','{{ \Illuminate\Support\Facades\Auth::id() }}')" class="btn btn-success">Notification</a>
              <a class="btn btn-danger" href="{{ secure_url('moderator/user/ban/'.$user->id.'/0') }}">Remove Ban</a>
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
@section('footerScripts')
  <link rel="stylesheet" type="text/css" href="//cdn.datatables.net/v/bs-3.3.7/dt-1.10.13/fc-3.2.2/fh-3.1.2/r-2.1.0/datatables.min.css"/>
  <script type="text/javascript" src="//cdn.datatables.net/v/bs-3.3.7/dt-1.10.13/fc-3.2.2/fh-3.1.2/r-2.1.0/datatables.min.js"></script>
  <script src="//cdn.jsdelivr.net/alertifyjs/1.9.0/alertify.min.js"></script>
  
  <!-- CSS -->
  <link rel="stylesheet" href="//cdn.jsdelivr.net/alertifyjs/1.9.0/css/alertify.min.css"/>
  <link rel="stylesheet" href="//cdn.jsdelivr.net/alertifyjs/1.9.0/css/themes/default.min.css"/>
  <link rel="stylesheet" href="//cdn.jsdelivr.net/alertifyjs/1.9.0/css/themes/bootstrap.min.css"/>
  
  <script src="{{ secure_asset('js/jquery.popconfirm.js') }}" type="text/javascript"></script>
  <script>
    $(function ($) {
      $('.table').DataTable();
      
      $('[data-toggle=confirmation]').popConfirm({
        placement: "left"
      });
    });
    
    
    function openNotification(to, user) {
      alertify.prompt('Send a notification', 'You can send a notification to this user', ''
        , function (evt, value) {
          $.post(
            '{{ secure_url('moderator/notify') }}',
            {"_token": "{{ csrf_token() }}", "from": user, "to": to, "data": value},
            function (data, status) {
              console.log(data)
            });
          alertify.success('Notification has been sent to user')
        }
        , function () {
          alertify.error('Cancel')
        });
    }
  
  </script>
@endsection