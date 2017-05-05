@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="col-md-2">
            @include('includes.side-menu')    
        </div>
        <div class="col-md-10">
            <div class="panel panel-default">
                <!-- Default panel contents -->
                <div class="panel-heading">
                    Update a Role 
                    <a class="btn btn-xs btn-primary pull-right" style="margin-left:10px;" href="{{ route('role.index') }}" role="button"><span class="glyphicon glyphicon-th-list" aria-hidden="true"></span> View Roles</a>&nbsp;
                    <a class="btn btn-xs btn-primary pull-right" style="margin-left:10px;" href="{{ route('role.create') }}" role="button"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Create Role</a>
                    </div>
                <div class="panel-body">
                    <form class="form" method="post" action="{{ route('role.update', $role->id) }}">
                        {{ method_field('PATCH') }}
                        {{ csrf_field() }}
                        <div class="form-group">
                            <label for="name">Name</label>
                            <input type="text" class="form-control" id="name" value="{{ $role->name }}" placeholder="Name" name="name">
                        </div>
                        <div class="form-group">
                            <label for="display_name">Display Name</label>
                            <input type="test" class="form-control" id="display_name" value="{{ $role->display_name }}" placeholder="" name="display_name">
                        </div>
                        <div class="form-group">
                            <label for="description">Description</label>
                            <input type="text" class="form-control" id="description" value="{{ $role->description }}" placeholder="" name="description">
                        </div>
                        <br />
                        <div class="form-group">
                            <h4>Select Permissions:</h4>
                            <hr />
                            @foreach($permissions as $permission)
                                <input type="checkbox" {{ in_array($permission->id, $role_permissions)?"checked":"" }} name="permission[]" value="{{ $permission->id }}" /> {{ $permission->name }}<br />
                            @endforeach
                        </div>
                        <button type="submit" class="btn btn-primary">Update Role</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection