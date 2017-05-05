@extends('layouts.app')

@section('content')
<div class="container well">
    <h4>Edit Batch Name</h4>
    <hr />
    @if(count($errors))
        @foreach($errors->all() as $error)
            <div class="alert alert-danger">{{ $error }}</div>
        @endforeach
    @endif
    <form method="post" action="{{ route('batch.edit') }}">
        {{ csrf_field() }}
        <input type="hidden" name="batch_id" id="batch_id" value="{{ $batch->id }}" />
        <div class="form-group">
            <label for="name">Batch Name</label>
            <input type="text" class="form-control" id="batch_name" placeholder="New Batch Name" name="batch_name">
        </div>
        <button type="submit" class="btn btn-primary">Upload Batch</button>
    </form>
</div>
@endsection