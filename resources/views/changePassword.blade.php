@extends('master')
@section('headScript')
  <script src="{{ cdn('js/parsley.min.js') }}"></script>
@endsection
@section('content')
  <style>
    .form-group.required .control-label:after {
      content: " *";
      color: red;
    }
  </style>
  <div class="row">
    @include('userMenu')
    
    <div class="col-md-8">
      <div class="row">
        <div class="col-md-12">
          <div class="panel panel-rss">
            <div class="panel-heading">Details</div>
            <div class="panel-body">
              
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
  <script>
    $(function () {
      $('#streamForm').parsley();
      $('.selectpicker').selectpicker({
        style: 'btn-default',
        size: 6
      });
    });
  </script>
@endsection