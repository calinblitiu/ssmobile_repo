@extends('master')
@section('title','User profile - ')
@section('content')
<link href="{{ secure_asset('css/semantic.min.css') }}" rel="stylesheet">
  <div class="row">
    @include('userMenu')
    <div class="col-md-8">
      <div class="row">
        <div class="col-md-12">
          <div class="panel panel-rss">
            <div class="panel-heading">Favourite team/competitions</div>
            <div class="panel-body">
	            <div class="row">
					<div class="col-md-6">Add favourite team/competitions</div>
				</div>
				<form action="{{ secure_url('/profile/favourite/store') }}" method="POST">
					<input type="hidden" name="_token" value="{{ csrf_token() }}">
					<input type="hidden" name="type" value="Team">
					<div class="row">
						<div class="col-md-8">
							<div class="ui fluid multiple search selection dropdown team-dropdown">
							{{-- <div class="ui fluid multiple search selection"> --}}
								<input name="items" id="team" type="hidden">
								<i class="dropdown icon"></i>
								<div class="default text">Search team</div>
							</div>
						</div>
						<div class="col-md-2">
							<button class="btn btn-default">Add</button>
						</div>
					</div>
				</form>

				<form action="{{ secure_url('/profile/favourite/store') }}" method="POST">
					<input type="hidden" name="_token" value="{{ csrf_token() }}">
					<input type="hidden" name="type" value="Competition">
					<div class="row clearfix" style="margin-top: 15px;">
						<div class="col-md-8">
							<div class="ui fluid multiple search selection dropdown competition-dropdown">
							{{-- <div class="ui fluid multiple search selection"> --}}
								<input name="items" id="competition" type="hidden">
								<i class="dropdown icon"></i>
								<div class="default text">Search Competition</div>
							</div>
						</div>
						<div class="col-md-2">
							<button class="btn btn-default">Add</button>
						</div>
					</div>
				</form>

				<div class="row clearfix" style="margin-top: 20px;">
	            	<table class="table table-hover table-striped">
	            		<tr>
	            			<th>ID</th>
	            			{{-- <th>Logo</th> --}}
	            			<th>Name</th>
	            			<th>Type</th>
	            			<th>Action</th>
	            		</tr>

						<?php $i = 1; ?>
	            		@foreach($favourites as $favourite)
	            			<tr>
	            				<td> {{ $i++ }} </td>
	            				{{-- <td> {{ $favourite->item_logo }} </td> --}}
	            				<td> {{ $favourite->item_name }} </td>
	            				<td> {{ $favourite->item_type }} </td>
								<td>
									<form method="POST" action="{{ secure_url('profile/favourite/delete/'.$favourite->id) }}" accept-charset="UTF-8">
										<input name="_token" type="hidden" value="{{ csrf_token() }}">
										<input class="btn btn-danger btn-xs" type="submit" value="Delete">
									</form>
								</td>
	            			</tr>
	            		@endforeach
	            	</table>
	            </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="clearfix"></div>
  </div>
@endsection
@section('scripts')
  <script src="{{ secure_asset('js/semantic.min.js') }}"></script>
  <script>
    function notificationAction(el) {
      console.log(el);
      $.post(
        '{{ secure_url('profile/notificationAction') }}',
        {"_token": "{{ csrf_token() }}", "id": el, "action": 1},
        function (data, status) {
          console.log(data)
        });
    }

    var url = window.location.href+'/search/team/{query}';
	$('.team-dropdown')
	  .dropdown({
	    apiSettings: {
	      	url: url,
			saveRemoteData: false,
			cache: false,
			mockResponse: false
	    }
	  })
	;

    var url = window.location.href+'/search/competition/{query}';
	$('.competition-dropdown')
	  .dropdown({
	    apiSettings: {
	      	url: url,
			saveRemoteData: false,
			cache: false,
			mockResponse: false
	    }
	  })
	;
  </script>
@endsection