@extends('layouts.app')

@section('content')
<div class="container well">
    <h4>Create a Batch Job</h4>
    <hr />
    @if(count($errors))
        @foreach($errors->all() as $error)
            <div class="alert alert-danger">{{ $error }}</div>
        @endforeach
    @endif
    <form method="post" action="{{ route('batch.upload') }}" enctype="multipart/form-data">
        {{ csrf_field() }}
        <div class="form-group">
            <label for="name">Batch Name</label>
            <input type="text" class="form-control" id="name" placeholder="Batch Name" name="name">
        </div>
        <div class="form-group">
            <label for="InputFile">File input</label>
            <input type="file" id="file" name="file">
            <p class="help-block">Supported Format : xlxs, csv</p>
        </div>
        <button type="submit" class="btn btn-primary">Upload Batch</button>
    </form>
</div>
@endsection