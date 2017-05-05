@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="col-md-2">
            @include('includes.side-menu')    
        </div>
        <div class="col-md-10">
            <div class="panel panel-default">
                <!-- Default panel contents -->
                <div class="panel-heading">Create a Role <a class="btn btn-xs btn-primary pull-right" href="{{ route('role.index') }}" role="button"><span class="glyphicon glyphicon-th-list" aria-hidden="true"></span> View Roles</a></div>
                <div class="panel-body">
                    <form class="form" method="post" action="{{ route('role.store') }}">
                        {{ csrf_field() }}
                        <div class="form-group">
                            <label for="name">Role Name</label>
                            <input type="text" class="form-control" id="name" placeholder="e.g. Admin" name="name">
                        </div>
                        <div class="form-group">
                            <label for="display_name">Role Display Name</label>
                            <input type="test" class="form-control" id="display_name" placeholder="" name="display_name">
                        </div>
                        <div class="form-group">
                            <label for="description">Role Description</label>
                            <input type="text" class="form-control" id="description" placeholder="" name="description">
                        </div>
                        <br />
                        <div class="form-group">
                            <h4>Permissions</h4>
                            <hr />
                            @php
                                $currentGroup = false;
                            @endphp
                            @foreach($permissions as $permission)
                                @php
                                    if ($currentGroup == false)
                                    {
                                        $currentGroup = $permission->grouped_id;
                                    }
                                    
                                    if ($currentGroup !== $permission->grouped_id)
                                    {
                                        echo '<br />';
                                        $currentGroup = $permission->grouped_id;
                                    }
                                @endphp
                                <div class="checkbox">
                                    <label>
                                      <input type="checkbox" name="permission[]" value="{{ $permission->id }}" /> {{ $permission->display_name }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                        <button type="submit" class="btn btn-primary">Create a Role</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection