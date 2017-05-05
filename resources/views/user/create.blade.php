@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="col-md-2">
            @include('includes.side-menu')    
        </div>
        <div class="col-md-10">
            <div class="panel panel-default">
                <!-- Default panel contents -->
                <div class="panel-heading">Create a User <a class="btn btn-xs btn-primary pull-right" href="{{ route('user.index') }}" role="button"><span class="glyphicon glyphicon-th-list" aria-hidden="true"></span> View Users</a></div>
                <div class="panel-body">
                    <form class="form" action="{{ route('user.store') }}" method="post">
                        {{ csrf_field() }}
                        <div class="form-group">
                            <label for="name">Name</label>
                            <input type="text" class="form-control" id="name" name="name">
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="text" class="form-control" id="email" placeholder="example@example.com" name="email">
                        </div>
                        <br />
                        <div class="form-group">
                            <h4>Assign Role</h4>
                            <hr />
                            @foreach($allRoles as $role)
                                <div class="checkbox">
                                    <label>
                                      <input type="checkbox" name="roles[]" value="{{ $role->id }}" /> {{ $role->name }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                        <br />
                        <button type="submit" class="btn btn-primary">Create a User</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection