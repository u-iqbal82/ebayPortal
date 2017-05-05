@extends('layouts.app')

@section('content')
<div class="container">
    <h4>Assign Articles to Users</h4>
    <hr />
    @if(count($errors))
        @foreach($errors->all() as $error)
            <div class="alert alert-danger">{{ $error }}</div>
        @endforeach
    @endif
    <div class="row">
        <form method="post" action="{{ route('articles.assign') }}">
            {{ csrf_field() }}
            <input type="hidden" name="batch_id" id="batch_id" value="{{ $batch->id }}" />
            <div class="col-md-7">
                <table class="table table-condensed">
                    <thead>
                        <tr>
                            <th>Subject</th>
                            <th>URL</th>
                            <th>Category</th>
                            <th><input type="checkbox" name="select_all_art" id="select_all_art" /></th>
                        </tr>
                    </thead>
                    <tbody>
                    @if(!empty($batch))
                        @foreach($batch->articles as $article)
                            @if($article->status == 'UnAssigned')
                            <tr>
                                <td>{{ $article->article_subject }}</td>
                                <td>{{ $article->article_url }}</td>
                                <td>{{ $article->article_category }}</td>
                                <td><input class="cats_art" type="checkbox" name="articles[]" value="{{ $article->id }}" /></td>
                            </tr>
                            @endif
                        @endforeach
                    @endif
                    </tbody>
                </table>
            </div>
            <div class="col-md-4 col-md-offset-1 well">
                <h4>Select Users To Assign Task</h4> 
                <div class="checkbox pull-right">
                    <label>
                      <input type="checkbox" name="select_all" id="select_all" /> Select All
                    </label>
                </div>
                <div class="clearfix"></div>  
                <hr />
                @if(!empty($roles))
                    @foreach($roles as $role)
                        @foreach($role->users as $user)
                            <div class="checkbox">
                            <label>
                              <input type="checkbox" class="cats" value="{{ $user->id }}" name="users[]"> {{ $user->name }}
                            </label>
                          </div>
                        @endforeach
                    @endforeach
                @endif
                <br />
                <hr />
                <button type="submit" class="btn btn-primary">Assign To Users</button>
            </div>
        </form>
    </div>
    
@endsection    