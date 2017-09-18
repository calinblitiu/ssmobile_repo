@extends('admin.master')
@section('title','Create a new event |')
@section('contentHeader')
  <h1>Create Event</h1>
@endsection

@section('content')
  <!-- Default box -->
  <div class="box">
    <div class="box-header with-border">
      <h3 class="box-title">Create Event</h3>
      <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse">
          <i class="fa fa-minus"></i></button>
        <button type="button" class="btn btn-box-tool" data-widget="remove" data-toggle="tooltip" title="Remove">
          <i class="fa fa-times"></i></button>
      </div>
    </div>
    <form class="form-horizontal" action="{{ secure_url('moderator/event/storeMatch') }}" method="post">
      {{ csrf_field() }}
      <div class="box-body">
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
          <label class="col-sm-2 control-label">Select Sport</label>
          <div class="col-sm-10">
            <select class="form-control" name="sport" required>
              @foreach($sports as $sport)
                <option value="{{ $sport->sport_id }}">{{ $sport->sport_name }}</option>
              @endforeach
            </select>
          </div>
        </div>
        <div class="form-group">
          <label class="col-sm-2 control-label">Select Competition</label>
          <div class="col-sm-10">
            <select class="form-control" name="competition" id="competition" required>
              <option>Select Nation first</option>
            </select>
          </div>
        </div>
        <div class="form-group">
          <label class="col-sm-2 control-label">Home Team</label>
          <div class="col-sm-10">
            <select class="form-control team" name="home_team" required>
              <option> Select Nation first </option>
            </select>
          </div>
        </div>
        <div class="form-group">
          <label class="col-sm-2 control-label">Away Team</label>
          <div class="col-sm-10">
            <select class="form-control team" name="away_team" required>
              <option> Select Nation first</option>
            </select>
          </div>
        </div>
        <div class="form-group">
          <label for="" class="col-sm-2 control-label">Game week</label>
          <div class="col-md-4">
            <input type='text' class="form-control" name="game_week"/>
          </div>
          <label for="" class="col-sm-2 control-label">Round name</label>
          <div class="col-md-4">
            <input type='text' class="form-control" name="round_name"/>
          </div>
        </div>
        <div class="form-group">
          <label for="" class="col-sm-2 control-label">Kickoff</label>
          <div class="col-md-10">
            <div class='input-group date datetimepicker'>
              <input type='text' class="form-control" name="kickoff" required/>
              <span class="input-group-addon">
              <span class="glyphicon glyphicon-calendar"></span>
            </span>
            </div>
          </div>
        </div>
        <div class="form-group">
          <label for="" class="col-sm-2 control-label">End time</label>
          <div class="col-md-10">
            <div class='input-group date datetimepicker'>
              <input type='text' class="form-control" name="end_date" required/>
              <span class="input-group-addon">
              <span class="glyphicon glyphicon-calendar"></span>
            </span>
            </div>
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
  <script src="{{ secure_asset('js/moment.min.js') }}"></script>
  <script src="{{ secure_asset('plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js') }}"></script>
  <link rel="stylesheet" href="{{ secure_asset('plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css') }}">
  <script>
    $(function ($) {
      $('.datetimepicker').datetimepicker({
        'format': 'YYYY-MM-DD HH:mm'
      });
      $('#nation').change(function () {
        $.get("{{ secure_url('moderator/getNationCompetitions')}}",
          {option: $(this).val()},
          function (data) {
            var competition = $('#competition');
            competition.empty();
            
            $.each(data, function (index, element) {
              competition.append("<option value='" + element.competition_id + "'>" + element.competition_name + "</option>");
            });
          });
        $.get("{{ secure_url('moderator/getNationTeams')}}",
          {option: $(this).val()},
          function (data) {
            var teams = $('.team');
            teams.empty();
      
            $.each(data, function (index, element) {
              teams.append("<option value='" + element.team_id + "'>" + element.team_name + "</option>");
            });
          });
      });
    });
  </script>
@endsection