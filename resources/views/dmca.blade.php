@extends('master')
@section('title',$page->title.' -')
@section('content')
  <style>
    .pageContent{
      background-color: #f1f1f1;
      padding: 15px;
    }
  </style>
  <div class="row">
    <div class="col-md-12 col-md-offset-0">
      <div class="pageContent">
        <h3>{{ $page->title }}</h3>
        {!! $page->body !!}
      </div>
    </div>
  </div>
@endsection