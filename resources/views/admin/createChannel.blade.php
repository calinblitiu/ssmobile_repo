@extends('admin.master')
@section('title','Create a new Channel |')
@section('contentHeader')
  <h1>Create Channel</h1>
@endsection

@section('content')
  <!-- Default box -->
  <div class="box">
    <div class="box-header with-border">
      <h3 class="box-title">Create Channel</h3>
      <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse">
          <i class="fa fa-minus"></i></button>
        <button type="button" class="btn btn-box-tool" data-widget="remove" data-toggle="tooltip" title="Remove">
          <i class="fa fa-times"></i></button>
      </div>
    </div>
    <form class="form-horizontal" action="{{ secure_url('moderator/channel/storeChannel') }}" method="post">
      {{ csrf_field() }}
      <div class="box-body">
        <div class="form-group">
          <label for="" class="col-sm-2 control-label">Channel ID</label>
          <div class="col-md-10">
              <input type='text' class="form-control" name="id" placeholder="Channel ID" required/>
          </div>
        </div>
        <div class="form-group">
          <label class="col-sm-2 control-label">Select Nation</label>
          <div class="col-sm-10">
            <select class="form-control" name="nation" id="nation">
              <option> Select nation </option>
              @foreach($nations as $nation)
                <option value="{{ $nation->nation_id }}">{{ $nation->nation_name }}</option>
              @endforeach
            </select>
          </div>
        </div>
        <div class="form-group">
          <label for="" class="col-sm-2 control-label">Channel Name</label>
          <div class="col-md-10">
              <input type='text' class="form-control" name="name" placeholder="Channel Name" required/>
          </div>
        </div>
        <div class="form-group">
          <label for="" class="col-sm-2 control-label">Channel Slug</label>
          <div class="col-md-10">
              <input type='text' class="form-control" name="slug" placeholder="Channel Slug" required/>
          </div>
        </div>
        <div class="form-group">
          <label for="" class="col-sm-2 control-label">Channel Description</label>
          <div class="col-md-10">
              <input type='text' class="form-control" name="description" placeholder="Channel Description" required/>
          </div>
        </div>
        <div class="form-group">
          <label for="" class="col-sm-2 control-label">Channel Acquire Rights</label>
          <div class="col-md-10">
              <input type='text' class="form-control" name="acqire" placeholder="Channel Acquire Rights" required/>
          </div>
        </div>
        <div class="form-group">
          <label for="" class="col-sm-2 control-label">Channel Iframe</label>
          <div class="col-md-10">
              <input type='text' class="form-control" name="iframe" placeholder="Channel Iframe" required/>
          </div>
        </div>
        <div class="form-group">
          <label for="" class="col-sm-2 control-label">Channel Logo</label>
          <div class="col-md-10">
              <input type='text' class="form-control" name="logo" placeholder="Channel Logo" required/>
          </div>
        </div>
        <div class="form-group">
          <label for="" class="col-sm-2 control-label">Channel Status</label>
          <div class="col-md-10">
              <input type='int' class="form-control" name="status" placeholder="Channel Status" required/>
          </div>
        </div>
        
      </div>
      <!-- /.box-body -->
      <div class="box-footer">
        <button type="submit" name="submit" value="back" class="btn btn-default">Save and back</button>
        <button type="submit" name="submit" value="new" class="btn btn-info pull-right">Save and Add new</button>
      </div>
      <!-- /.box-footer -->
    </form>
    <!-- /.box-body -->
    <div class="box-footer">
    </div>
    <!-- /.box-footer-->
  </div>
  <!-- /.box -->
@endsection

@section('footerScripts')
  <script src="{{ secure_asset('js/moment.js') }}"></script>
  <script src="{{ secure_asset('plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js') }}"></script>
  <link rel="stylesheet" href="{{ secure_asset('plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css') }}">
  <script>
    // $(function ($) {
    //   $('.datetimepicker').datetimepicker({
    //     'format':'YYYY-MM-DD HH:mm'
    //   });
  
    //   $('#nation').change(function(){
    //     $.get("{{ secure_url('moderator/getNationCompetitions')}}",
    //       { option: $(this).val() },
    //       function(data) {
    //         var competition = $('#competition');
    //         competition.empty();
        
    //         $.each(data, function(index, element) {
    //           competition.append("<option value='"+ element.competition_id +"'>" + element.competition_name + "</option>");
    //         });
    //       });
    //   });
    // });
  </script>
@endsection