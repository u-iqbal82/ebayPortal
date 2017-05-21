@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="col-md-2">
            @include('includes.side-menu')    
        </div>
        <div class="col-md-10">
            <div class="panel panel-default">
                <!-- Default panel contents -->
                <div class="panel-heading">Users</div>
                <div class="panel-body">
                    
                </div>
                    <!-- Table -->
                    <table class="table table-striped table-condensed">
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Roles</th>
                            <th>Creation Date</th>
                            <th>Last Login</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                        @forelse($users as $user)
                            <tr>
                                <td></td>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                
                                <td>
                                    @foreach($user->roles as $role)
                                        {{$role->name}}
                                    @endforeach
                
                                </td>
                                <td>{{ $user->created_at }}</td>
                                <td> @if ($user->last_login != '0000-00-00 00:00:00') {{ $user->last_login }} @endif</td>
                                <td> 
                                        @if ($user->deleted_at != null)
                                            InActive
                                        @else
                                            Active
                                        @endif
                                    </td>
                                <td>
                                    @role('super-admin')
                            
                                        @if ($user->deleted_at != null)
                                            <form method="GET" action="{{ route('user.restore', $user->id) }}" class="pull-left">
                                                <input class="btn btn-sm btn-info" type="submit" value="Activate" />
                                            </form>
                                        @else
                                            <form method="POST" action="{{ route('user.destroy', $user->id) }}" class="pull-left">
                                                {{ csrf_field() }}
                                                {{ method_field('DELETE') }}
                                                <input class="btn btn-sm btn-danger" type="submit" value="Delete" />
                                            </form>
                                        @endif
                                        <a class="btn btn-sm btn-warning" href="/reset/user/{{ $user->id }}" role="button">Send Reset Password Email</a>
                                        <a class="btn btn-sm btn-info" href="/reset/user/{{ $user->id }}/manual" role="button">Reset Password Manually</a>
                                    @endrole
                                    
                                    <!-- Button trigger modal -->
                                    <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#myModal-{{$user->id}}">
                                        Edit
                                    </button>
                
                                    <!-- Modal -->
                                    <div class="modal fade" id="myModal-{{$user->id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                                                aria-hidden="true">&times;</span></button>
                                                    <h4 class="modal-title" id="myModalLabel">{{$user->name}} Role edit</h4>
                                                </div>
                                                <div class="modal-body">
                                                    <form action="{{route('user.update',$user->id)}}" method="post" role="form" id="role-form-{{$user->id}}">
                                                        {{csrf_field()}}
                                                        {{method_field('PATCH')}}
                                                        <div class="form-group">
                
                                                            <select name="roles[]" multiple>
                                                                @foreach($allRoles as $role)
                                                                    <option value="{{$role->id}}">{{$role->name}}</option>
                                                                @endforeach
                                                            </select>
                
                                                        </div>
                
                                                        {{--<button type="submit" class="btn btn-primary">Submit</button>--}}
                                                    </form>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                                    <button type="submit" class="btn btn-primary" onclick="$('#role-form-{{$user->id}}').submit()">Save changes</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td>no users</td></tr>
                        @endforelse
                </table>
            </div>
        </div>
    </div>
@endsection

