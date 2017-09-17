@extends('admin.master')
@section('title','Dashboard |')
@section('contentHeader')
  <h1>
    Update {{ $page->title }} page
  </h1>
@endsection

@section('content')
  <!-- Default box -->
  <div class="box">
    <div class="box-header with-border">
      <h3 class="box-title">Page details</h3>
      <div class="box-tools pull-right">
        <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse">
          <i class="fa fa-minus"></i></button>
        <button type="button" class="btn btn-box-tool" data-widget="remove" data-toggle="tooltip" title="Remove">
          <i class="fa fa-times"></i></button>
      </div>
    </div>
    <div class="box-body">
      <form action="{{ secure_url('moderator/page/save') }}" method="post">
        {{ csrf_field() }}
        <input type="hidden" name="pageId" value="{{ $page->id }}">
        <div class="form-group">
          <label>Page title</label>
          <input type="text" class="form-control" name="title" placeholder="Page title" value="{{ $page->title }}" required>
        </div>
        <div class="form-group">
          <label>Textarea</label>
          <textarea id="editor" class="form-control editor" name="body" rows="3" placeholder="add body text">
            {{ $page->body }}
          </textarea>
        </div>
        <div class="form-group">
          <button type="submit" class="btn btn-primary">Save</button>
        </div>
      </form>
    </div>
    <!-- /.box-body -->
    <div class="box-footer"></div>
    <!-- /.box-footer-->
  </div>
  <!-- /.box -->
@endsection

@section('footerScripts')
  <script src="//cdn.ckeditor.com/4.6.2/full/ckeditor.js"></script>
  
  <script>
    $(function($){
      CKEDITOR.replace( 'editor' );
    });
    
  </script>
@endsection