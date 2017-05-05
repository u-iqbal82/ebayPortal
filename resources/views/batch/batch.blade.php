@extends('layouts.app')

@section('content')
<div class="container well">
    <h4>Assign Batch Name</h4>
    <hr />
    @if(count($errors))
        @foreach($errors->all() as $error)
            <div class="alert alert-danger">{{ $error }}</div>
        @endforeach
    @endif
    @if(count($batch) > 0)
    <form method="post" action="{{ route('batch.assign') }}">
        <div class="row">
            <div class="col-md-3">
                <table class="table">
                    <tr>
                        <td>Batch #</td><td>{{ $batch->id }}</td>
                    </tr>
                    <tr>
                        <td>Uploaded By</td><td>{{ $batch->user->name }}</td>
                    </tr>
                    <tr>
                        <td>Total Articles</td><td>{{ count($batch->articles) }}</td>
                    </tr>
                </table>
            </div>
            <div class="col-md-4 col-md-offset-2">
                {{ csrf_field() }}
                    <input type="hidden" name="batch_id" id="batch_id" value="{{ $batch->id }}" />
                    <div class="form-group">
                        <label for="batch-name">Give it a Name</label>
                        <input type="text" class="form-control" id="batch_name" name="batch_name" value="">
                    </div>
                    <button type="submit" class="btn btn-primary">Create Batch</button>
            </div>
        </div>
        <h4>Articles</h4>
        <hr />
        <div class="row">
        <div class="form-group pull-right">
            <label for="inputPassword3" class="col-md-4 control-label">Category Filter: </label>
            <div class="col-md-8">
                <select name="categories" class="form-control" id="categories">
                    <option value="/batch/show/{{ $batch->id }}/batch/all" selected="selected">Show All</option>
                    @foreach ($batch->articles()->groupBy('article_category')->get() as $cat)
                        <option @if($category == $cat->article_category) selected="selected" @endif value="/batch/show/{{ $batch->id }}/batch/{{ $cat->article_category }}">{{ $cat->article_category }}</option>
                    @endforeach    
                </select>
            </div>
          </div>
        </div>
        <br />
            
        
        <div class="row">
            
            @if(count($batch->articles) > 0)
                    
                    <table class="table table-condensed">
                        <thead>
                            <tr>
                                <th>Subject</th>
                                <th>URL</th>
                                <th>Category</th>
                                <th><input type="checkbox" name="select_all" id="select_all" /></th>
                            </tr>
                        </thead>
                        <tbody>
                        @if($category !== 'all')
                            @foreach($batch->articles->where('article_category', $category) as $article)
                                <tr>
                                    <td>{{ $article->article_subject }}</td>
                                    <td>{{ $article->article_url }}</td>
                                    <td>{{ $article->article_category }}</td>
                                    <td><input class="cats" type="checkbox" name="articles[]" value="{{ $article->id }}" /></td>
                                </tr>
                            @endforeach
                        @else
                            @foreach($batch->articles as $article)
                                <tr>
                                    <td>{{ $article->article_subject }}</td>
                                    <td>{{ $article->article_url }}</td>
                                    <td>{{ $article->article_category }}</td>
                                    <td><input class="cats" type="checkbox" name="articles[]" value="{{ $article->id }}" /></td>
                                </tr>
                            @endforeach
                        @endif
                        <tr>
                            <td colspan="4"><button type="submit" class="btn btn-primary pull-right">Create Batch</button></td>
                        </tr>
                        </tbody>
                </table>
            @endif
        </div>
    @else
        Nothing to show.
    @endif
    </form>
</div>
@endsection