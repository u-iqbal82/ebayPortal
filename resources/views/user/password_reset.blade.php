@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        @if(count($errors))
            @foreach($errors->all() as $error)
                <div class="alert alert-danger">{{ $error }}</div>
            @endforeach
        @endif
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <!-- Default panel contents -->
                <div class="panel-heading">Update Password</div>
                <div class="panel-body">
                    <form class="form" action="{{ route('change.password') }}" method="post">
                        {{ csrf_field() }}
                        <div class="form-group">
                            <label for="name">Old Password</label>
                            <input type="password" class="form-control" id="old_password" name="old_password">
                        </div>
                        <div class="form-group">
                            <label for="name">New Password</label>
                            <input type="password" class="form-control" id="new_password" name="new_password">
                        </div>
                        <button type="submit" class="btn btn-primary">Update Password</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection