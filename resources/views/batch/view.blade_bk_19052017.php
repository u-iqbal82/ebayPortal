@extends('layouts.app')

@section('content')
<div class="container well">
   
    @if(count($errors))
        @foreach($errors->all() as $error)
            <div class="alert alert-danger">{{ $error }}</div>
        @endforeach
    @endif
    @if(count($batch) > 0)
        <div class="row">
            <div class="col-md-3">
                <h4>Batch Details</h4>
                <hr />
                <table class="table">
                    <tr>
                        <td>Batch #</td><td>{{ $batch->id }}</td>
                    </tr>
                    <tr>
                        <td>Batch Name</td><td>{{ $batch->name }}</td>
                    </tr>
                    <tr>
                        <td>Uploaded By</td><td>{{ $batch->user->name }}</td>
                    </tr>
                    <tr>
                        <td>Total Articles</td><td>{{ count($batch->articles) }}</td>
                    </tr>
                </table>
            </div>
            <div class="col-md-5 col-md-offset-1">
                <h4>Assigned to Users</h4>
                <hr />
                <table class="table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>No. of Articles Assigned</th>
                            <th>Saved</th>
                            <th>Completed</th>
                            <th>Quality Checked</th>
                        </tr>
                    </thead>
                    @foreach($usersInPlace as $userKey => $userValue)
                    <tr>
                        <td>{{ $userValue['name'] }}</td><td>{{ $userValue['number_of_articles'] }}</td>
                        <td>{{ $userValue['Saved'] }}</td><td>{{ $userValue['Completed'] + $userValue['QualityChecked'] }}</td>
                        <td>{{ $userValue['QualityChecked'] }}</td>
                    </tr>
                    @endforeach
                </table>
            </div>
        </div>
        <h4>Articles</h4>
        <hr />
        <div class="row">
        <div class="form-group pull-right">
            <label for="inputPassword3" class="col-md-4 control-label">Category Filter: </label>
            <div class="col-md-8">
                <select name="categories" class="form-control" id="categories">
                    <option value="/batch/view/{{ $batch->id }}/batch/all" selected="selected">Show All</option>
                    @foreach ($batch->articles()->groupBy('article_category')->get() as $cat)
                        <option @if($category == $cat->article_category) selected="selected" @endif value="/batch/view/{{ $batch->id }}/batch/{{ $cat->article_category }}">{{ $cat->article_category }}</option>
                    @endforeach    
                </select>
            </div>
          </div>
        </div>
        
        <div class="row">
            @if ($batch->status == 'PartiallyAssigned')
                <div class="alert alert-danger">Batch is not Fully Assigned. Assign it fully to start working on it.</div>
            @endif
            
            @if(count($batch->articles) > 0)
                    
                    <table class="table table-condensed table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Subject</th>
                                <th>URL</th>
                                <th>Category</th>
                                <th>Assigned To</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        @if($category !== 'all')
                            @foreach($batch->articles->where('article_category', $category) as $index => $article)
                                @if ($article->status == 'UnAssigned')
                                    @continue;
                                @endif
                                
                                @if ($article->user[0]->id != Auth::user()->id && !Auth::user()->hasRole(['admin', 'super-admin']))
                                    @continue;
                                @endif

                                <tr>
                                    <td>{{ $index + 1}}</td>
                                    <td>{{ $article->article_subject }}</td>
                                    <td>{{ $article->article_url }}</td>
                                    <td>{{ $article->article_category }}</td>
                                    <td>{{ $article->user[0]->name }}</td>
                                    <td>{{ $article->status }}</td>
                                    <td>
                                        @if ($batch->status == 'Submitted' || $batch->status == 'QCInProcess' || $batch->status == 'Final')
                                            @if (Auth::user()->hasRole(['admin', 'super-admin']))
                                                <a class="btn btn-sm btn-success" href="/article/view/{{ $article->id }}" role="button">Open Article</a>
                                                <a class="btn btn-sm btn-info" href="/article/{{ $article->id }}/saved" role="button">Re-Do</a>
                                                @permission('do-quality-check')
                                                    @if ($batch->status == 'Submitted' || $batch->status == 'QCInProcess')
                                                        @if ($article->status == 'Completed')
                                                        <a class="btn btn-sm btn-success" href="/article/view/{{ $article->id }}/qc" role="button">QC Completed</a>
                                                        @endif
                                                    @endif
                                                @endpermission
                                            @else
                                                @if ($article->status == 'Saved')
                                                    <a class="btn btn-sm btn-success" href="/article/view/{{ $article->id }}" role="button">Open Article</a>
                                                    <a class="btn btn-sm btn-info" href="/article/view/{{ $batch->id }}/completed/{{ $article->id }}" role="button">Mark as Completed</a>
                                                @endif
                                            @endif
                                        @else
                                            @if ((($batch->status == 'FullyAssigned' || $batch->status == 'InProcess') && ($article->status == 'Saved' || $article->status == 'Assigned')) || ($batch->status == 'Submitted' && $article->status == 'Completed'))
                                                <a class="btn btn-sm btn-success" href="/article/view/{{ $article->id }}" role="button">Open Article</a>
                                            @else
                                                <a class="btn btn-sm btn-success disabled" href="/article/view/{{ $article->id }}" disabled="disabled" role="button">Open Article</a>
                                            @endif
                                            
                                            @if ($article->status == 'Saved')
                                                <a class="btn btn-sm btn-info" href="/article/view/{{ $batch->id }}/completed/{{ $article->id }}" role="button">Mark as Completed</a>
                                            @endif
                                            
                                            @permission('move-to-saved')
                                                @if ($article->status == 'Completed')
                                                    <a class="btn btn-sm btn-info" href="/article/{{ $article->id }}/saved" role="button">Re-Do</a>
                                                @endif
                                            @endpermission
                                        @endif
                                    </td>    
                                </tr>
                            @endforeach
                        @else
                            @foreach($batch->articles as $index => $article)
                                @if ($article->status == 'UnAssigned')
                                    @continue;
                                @endif
                                @if ($article->user[0]->id != Auth::user()->id && !Auth::user()->hasRole(['admin', 'super-admin']))
                                    @continue;
                                @endif
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $article->article_subject }}</td>
                                    <td>{{ $article->article_url }}</td>
                                    <td>{{ $article->article_category }}</td>
                                    <td>{{ $article->user[0]->name }}</td>
                                    <td>{{ $article->status }}</td>
                                    <td>
                                        @if (Auth::user()->hasRole(['admin', 'super-admin']))
                                            <a class="btn btn-sm btn-success" href="/article/view/{{ $article->id }}" role="button">Open Article</a>
                                            @permission('do-quality-check')
                                                @if ($batch->status == 'Submitted' || $batch->status == 'QCInProcess')
                                                    @if ($article->status == 'Completed')
                                                    <a class="btn btn-sm btn-success" href="/article/view/{{ $article->id }}/qc" role="button">QC Completed</a>
                                                    @endif
                                                @endif
                                            @endpermission
                                        @else
                                        
                                        @endif
                                        
                                        @if ($batch->status == 'Submitted' || $batch->status == 'QCInProcess' || $batch->status == 'Final')
                                            @if (Auth::user()->hasRole(['admin', 'super-admin']))
                                                <a class="btn btn-sm btn-success" href="/article/view/{{ $article->id }}" role="button">Open Article</a>
                                                @if ($article->status != 'Saved')
                                                    <a class="btn btn-sm btn-info" href="/article/{{ $article->id }}/saved" role="button">Re-Do</a>
                                                @endif
                                                @permission('do-quality-check')
                                                    @if ($batch->status == 'Submitted' || $batch->status == 'QCInProcess')
                                                        @if ($article->status == 'Completed')
                                                        <a class="btn btn-sm btn-success" href="/article/view/{{ $article->id }}/qc" role="button">QC Completed</a>
                                                        @endif
                                                    @endif
                                                @endpermission
                                            @else
                                                @if ($article->status == 'Saved')
                                                    <a class="btn btn-sm btn-success" href="/article/view/{{ $article->id }}" role="button">Open Article</a>
                                                @endif
                                            @endif
                                        @else
                                            @if ((($batch->status == 'FullyAssigned' || $batch->status == 'InProcess') && ($article->status == 'Saved' || $article->status == 'Assigned')) || ($batch->status == 'Submitted' && $article->status == 'Completed'))
                                                <a class="btn btn-sm btn-success" href="/article/view/{{ $article->id }}" role="button">Open Article</a>
                                            @else
                                                <a class="btn btn-sm btn-success disabled" href="/article/view/{{ $article->id }}" disabled="disabled" role="button">Open Article</a>
                                            @endif
                                            
                                            @if ($article->status == 'Saved')
                                                <a class="btn btn-sm btn-info" href="/article/view/{{ $batch->id }}/completed/{{ $article->id }}" role="button">Mark as Completed</a>
                                            @endif
                                            
                                            @permission('move-to-saved')
                                                @if ($article->status == 'Completed')
                                                    <a class="btn btn-sm btn-info" href="/article/{{ $article->id }}/saved" role="button">Re-Do</a>
                                                @endif
                                            @endpermission
                                        
                                        @endif
                                        
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                        </tbody>
                </table>
            @endif
        </div>
    @else
        Nothing to show.
    @endif
</div>
@endsection