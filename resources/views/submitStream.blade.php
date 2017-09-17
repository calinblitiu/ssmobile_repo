@extends('master')
@section('title','Submit stream - ')
@section('content')
  <div class="row">
    @include('userMenu')
    <div class="col-md-8">
      <div class="row">
        <div class="col-md-12">
          <div class="panel panel-rss">
            <div class="panel-heading">Details</div>
            @if (session('done'))
              <div class="alert alert-success" style="margin: 10px;">
                {{ session('done') }}
              </div>
            @endif
            @if (session('info'))
              <div class="alert alert-info" style="margin: 10px;">
                {{ session('info') }}
              </div>
            @endif
            @if (count($errors) > 0)
              <div class="alert alert-danger">
                <ul>
                  @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                  @endforeach
                </ul>
              </div>
            @endif
            @if (session('error'))
              <div class="alert alert-danger" style="margin: 10px;">
                {{ session('error') }}
              </div>
            @endif
            <div class="panel-body">
              <form class="form-horizontal" id="streamForm" role="form" method="POST" action="{{ secure_url('submit') }}" data-parsley-validate>
                {{ csrf_field() }}
                <div class="form-group required">
                  <label for="" class="col-sm-3 control-label">Select Event</label>
                  <div class="col-sm-9">
                    <select name="eventId" id="eventId" class="form-control selectpicker" data-live-search="true" required title="Choose one of the following...">
                      @foreach($events as $event)
                        @if(\Carbon\Carbon::parse($event->end_date) < \Carbon\Carbon::now())
                          @continue
                        @endif
                        <option value="{{ $event->event_id }}" data-tokens="{{ $event->home_team.' '.$event->away_team }}">
                          @if(is_null($event->event_title) || $event->event_title == 'NULL' || empty($event->event_title))
                            {{ '['.\Carbon\Carbon::parse($event->start_date)->addHours(Session::get('visitorTZ'))->format('m/d h:m').'] '.$event->home_team.' vs '.$event->away_team }}
                          @else
                            {{ '['.\Carbon\Carbon::parse($event->start_date)->addHours(Session::get('visitorTZ'))->format('m/d h:m').'] '.$event->event_title }}
                          @endif
                          
                        </option>
                      @endforeach
                    </select>
                  </div>
                </div>
                <div class="form-group required">
                  <label for="" class="col-sm-3 control-label">Stream Type</label>
                  <div class="col-sm-9">
                    <select id='type_selector' onchange="selectType(this)" class="form-control selectpicker" name="streamType" data-live-search="true" required title="Choose one of the following...">
                      <option value="http">HTTP</option>
                      <option value="Acestream">Acestream</option>
                      <option value="sopcast">Sopcast</option>
                      <option value="VLC">VLC</option>
                      <option value="Other">Other</option>
                    </select>
                  </div>
                </div>
                <div class="form-group required">
                  <label class="col-sm-3 control-label">URL</label>
                  <div class="col-sm-9">
                    <input id="url" disabled type="text" name="url" class="form-control" required placeholder="Stream URL"
                           data-parsley-pattern="(?=(http:\/\/|https:\/\/|acestream:\/\/|sopcast:\/\/)).*"
                           data-parsley-error-message="Please select stream type first" 
                           data-href="{{ secure_url('checkBanDomain') }}">
                  </div>
                </div>
                <div class="form-group required">
                  <label for="" class="col-sm-3 control-label">Language</label>
                  <div class="col-sm-9">
                    <select class="form-control selectpicker" name="language" data-live-search="true" required title="Choose one of the following...">
                      @foreach($languages as $language)
                        <option value="{{ $language->language_name }}" data-tokens="{{ $language->language_name }}">
                          {{ $language->language_name }}
                        </option>
                      @endforeach
                    </select>
                  </div>
                </div>
                <div class="form-group required">
                  <label for="" class="col-sm-3 control-label">Stream quality</label>
                  <div class="col-sm-9">
                    <select class="form-control selectpicker" name="quality" required data-live-search="true" title="Choose one of the following...">
                      <option value="HD">HD</option>
                      <option value="520p">520p</option>
                      <option value="SD">SD</option>
                    </select>
                  </div>
                </div>
                <div class="form-group">
                  <div class="col-sm-offset-3 col-sm-9">
                    <div class="checkbox">
                      <label>
                        <input name="compatible" value="1" type="checkbox"> is mobile compatible ?
                      </label>
                    </div>
                  </div>
                </div>
                <div class="form-group required">
                  <label for="" class="col-sm-3 control-label">Number of Ad-overlays</label>
                  <div class="col-sm-9">
                    <select class="form-control selectpicker" name="adNumber" data-live-search="true" required title="Choose one of the following...">
                      <option value="0">0</option>
                      <option value="1">1</option>
                      <option value="2">2</option>
                      <option value="3">3</option>
                      <option value="4">4</option>
                      <option value="5">5</option>
                      <option value="6">6</option>
                      <option value="7">7</option>
                      <option value="8">8</option>
                      <option value="9">9</option>
                      <option value="10">9+</option>
                    </select>
                  </div>
                </div>
                <div class="form-group">
                  <div class="col-sm-offset-3 col-sm-9">
                    <div class="checkbox">
                      <label>
                        <input name="nsfw" value="1" type="checkbox"> NSFW ads
                      </label>
                    </div>
                  </div>
                </div>
                <div class="form-group">
                  <div class="col-sm-offset-3 col-sm-9">
                    <div class="checkbox">
                      <label>
                        <input name="geoLock" value="1" type="checkbox"> Geo Locked
                      </label>
                    </div>
                  </div>
                </div>
                <div class="form-group">
                  <label for="" class="col-sm-3 control-label">Other Info.</label>
                  <div class="col-sm-9">
                    <input type="text" name="otherInfo" class="form-control" maxlength="40" placeholder="Other Information about the stream no more than 40 characters">
                  </div>
                </div>
                <div class="form-group">
                  <div class="col-sm-offset-3 col-sm-9">
                    <button class="btn btn-rss" onclick="submitForm()">Add stream</button>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="clearfix"></div>
  </div>
@endsection
@section('scripts')
  <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.2/css/bootstrap-select.min.css">
  <script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.2/js/bootstrap-select.min.js"></script>
  <script src="{{ secure_asset('js/parsley.min.js') }}"></script>
  <script>
    $(function ($) {
      $('#streamForm').parsley();
      $('.selectpicker').selectpicker({
        style: 'btn-default',
        size: 6
      });
    });
    
    function selectType(el) {
      var streamType = el.value;
      if (streamType == 'http') {
        $('#url').prop('disabled', false);
        $('#url').attr("data-parsley-pattern", "(?=(http:\/\/|https:\/\/)).*");
        $('#url').attr('data-parsley-error-message', 'Please only use http://, https://');
      }
      else if (streamType == 'Acestream') {
        $('#url').prop('disabled', false);
        $('#url').attr("data-parsley-pattern", "(?=(acestream:\/\/)).*");
        $('#url').attr('data-parsley-error-message', 'Please only use acestream://');
      }
      else if (streamType == 'sopcast') {
        $('#url').prop('disabled', false);
        $('#url').attr("data-parsley-pattern", "(?=(sop:\/\/)).*");
        $('#url').attr('data-parsley-error-message', 'Please only use sop://');
      }
      else if (streamType == 'VLC') {
        $('#url').prop('disabled', false);
        $('#url').attr("data-parsley-pattern", "(?=(http:\/\/|https:\/\/)).*");
        $('#url').attr('data-parsley-error-message', 'Please only use http://, https://');
      }
      else if (streamType == 'Other') {
        $('#url').disabled = false;
        $('#url').removeAttr("data-parsley-pattern");
        $('#url').removeAttr('data-parsley-error-message');
      }
    }

    function checkValidation()
    {
        $.post(
          $('#url').attr('data-href'),
          {"_token": "{{ csrf_token() }}", "url": $('#url').val(), "eventId": $('#eventId').val()},
          function (data, status) {
            if( data == 0 ){
              $('#url').attr("data-parsley-pattern", "----");
              $('#url').attr('data-parsley-error-message', 'This domain already banned, please use another url.');
            }else if( data == 1 ){
              $('#url').attr("data-parsley-pattern", "----");
              $('#url').attr('data-parsley-error-message', 'This url already published. please use another url');
            }else{
              $('#url').removeAttr("data-parsley-pattern");
              $('#url').removeAttr('data-parsley-error-message');
              selectType( document.getElementById('type_selector') );
            }
          $('#streamForm').submit();
        });
    }
    function submitForm(){
      e.preventDefault();
      checkValidation();
    }
  </script>
@endsection