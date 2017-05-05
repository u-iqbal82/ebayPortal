@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="col-md-2">
            @include('includes.side-menu')    
        </div>
        <div class="col-md-10">
            <div class="panel panel-default">
                <!-- Default panel contents -->
                <div class="panel-heading">View Roles <a class="btn btn-xs btn-primary pull-right" href="{{ route('role.create') }}" role="button"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Create Role</a></div>
                <div class="panel-body">
                <table class="table table-striped table-condensed">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Display Name</th>
                            <th>Description</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($roles as $role)
                            <tr>
                                <td>{{ $role->name }}</td>
                                <td>{{ $role->display_name }}</td>
                                <td>{{ $role->description }}</td>
                                <td>
                                    <a class="btn btn-info btn-sm pull-left" href="{{ route('role.edit', $role->id) }}" role="button">Edit</a> &nbsp;
                                    @role('admin')
                                    <form method="POST" action="{{ route('role.destroy', $role->id) }}" class="pull-left">
                                        {{ csrf_field() }}
                                        {{ method_field('DELETE') }}
                                        <input class="btn btn-sm btn-danger" type="submit" value="Delete" />
                                    </form>
                                    @endrole
                                </td>
                            </tr>
                            
                            @empty
                                <tr>
                                    <td>No Roles.</td>
                                </tr>
                            
                        @endforelse
                    </tbody>
                </table>    
            </div>
        </div>
    </div>
@endsection