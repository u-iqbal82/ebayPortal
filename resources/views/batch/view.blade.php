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
            <div class="col-md-8 col-md-offset-1">
                <h4>Assigned to Users</h4>
                <hr />
                <table class="table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>No. of Articles Assigned</th>
                            <th>Saved</th>
                            <th>Review</th>
                            <th>Completed</th>
                            <th>Quality Checked</th>
                            <th>Final</th>
                        </tr>
                    </thead>
                    @php
                        $totalAssigned = $totalSaved = $totalComplete = $totalQC = $totalFinal = $totalReview = $totalReviewCompleted = 0;
                    @endphp
                    
                    @foreach($usersInPlace as $userKey => $userValue)
                    @if (Auth::user()->hasRole(['admin', 'super-admin']) || $userKey == Auth::user()->id)
                    <tr>
                        <td>{{ $userValue['name'] }}</td><td>{{ $userValue['number_of_articles'] }}</td>
                        <td>{{ $userValue['Saved'] }}</td><td>{{ $userValue['Review'] }}</td>
                        <td>{{ $userValue['Completed'] }}</td>
                        <td>{{ $userValue['QualityChecked'] }}</td><td>{{ $userValue['Final'] }}</td>
                    </tr>
                    
                    @php
                        $totalAssigned = $totalAssigned + $userValue['number_of_articles'];
                        $totalSaved = $totalSaved + $userValue['Saved'];
                        $totalComplete = $totalComplete + $userValue['Completed'];
                        $totalQC = $totalQC + $userValue['QualityChecked'];
                        $totalFinal = $totalFinal + $userValue['Final'];
                        $totalReview = $totalReview + $userValue['Review'];
                    @endphp
                    
                    @endif
                    @endforeach
                    <tfooter>
                        <tr class="info bold">
                            <td> -- </td><td>{{ $totalAssigned }}</td>
                            <td>{{ $totalSaved }}</td><td>{{ $totalReview }}</td>
                            <td>{{ $totalComplete }}</td>
                            <td>{{ $totalQC }}</td><td>{{ $totalFinal }}</td>
                        </tr>
                    </tfooter>
                </table>
            </div>
        </div>
        <h4>Articles</h4>
        <hr />
        <!--
        <div class="row">
        <div class="form-group pull-left">
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
        -->
        
        <div class="row">
            @if(count($batch->articles) > 0)
                    <form class="form form-inline" action="{{ route('articles.update') }}" method="post">
                        {{ csrf_field() }}
                        <input type="hidden" name="batch_id" id="batch_id" value="{{ $batch->id }}" />
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group pull-left">
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
                        <div class="col-md-6">
                            <div class="form-group pull-right" style="margin-right:40px;">
                                <label for="update_status_to" class="col-md-3 control-label">Update Status : </label>
                                <div class="col-md-5">
                                    <select name="update_status_to" id="update_status_to" class="form-control">
                                        <option value="false"> -- Please select -- </option>
                                        <option value="Final">Mark as Final</option>
                                        
                                        @if (Auth::user()->hasRole(['admin', 'super-admin']))  
                                            <option value="UnAssigned">Move to Un-Assigned</option>
                                        @endif
                                        
                                    </select>
                                </div>
                                <div class="col-md-3"><input type="submit" class="btn btn-primary" value="Update Status" /></div>
                            </div>
                        </div>
                    </div>
                    <br /><br />
                    <table class="table table-condensed table-striped">
                        <thead>
                            <tr>
                                <th><input type="checkbox" name="select_all_art" id="select_all_art" /></th>
                                <th>#</th>
                                <th>Subject</th>
                                <th>URL</th>
                                <th>Category</th>
                                <th>Assigned To</th>
                                <th>Date Assigned</th>
                                <th>Status</th>
                                <th>QC By</th>
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
                                
                                @php
                                $focus = '';
                                
                                if (isset($articleToFocus) && $articleToFocus !== FALSE)
                                {
                                    if ($articleToFocus ==  $article->id)
                                    {
                                        $focus = 'class="focus-row"';
                                    }
                                }
                                @endphp
                                
                                <tr @php echo $focus; @endphp>
                                    <td>
                                        <input class="cats_art" type="checkbox" name="articles[]" value="{{ $article->id }}" />
                                    </td>
                                    <td>{{ $index + 1}}</td>
                                    <td>{{ $article->article_subject }}</td>
                                    <td>{{ $article->article_url }}</td>
                                    <td>{{ $article->article_category }}</td>
                                    <td>{{ $article->user[0]->name }}</td>
                                    <td>
                                        @if ($article->assigned_at != '0000-00-00 00:00:00')
                                            {{ $article->assigned_at }}
                                        @else
                                            --
                                        @endif
                                    </td>
                                    <td>{{ $article->status }}</td>
                                    <td>
                                        @if ($article->qc_at == '0')
                                            --
                                        @else
                                            {{ $users[$article->qc_at] }}
                                        @endif                                                                        
                                    </td>
                                    <td>    
                                        @if (Auth::user()->hasRole(['admin', 'super-admin']))
                                            <a class="btn btn-sm btn-success" href="/article/view/{{ $article->id }}" role="button">Open Article</a>
                                            @permission('do-quality-check')
                                                @if ($article->status == 'Completed')
                                                    <a class="btn btn-sm btn-success" href="/article/view/{{ $article->id }}/qc" role="button">QC Completed</a>
                                                @endif
                                            @endpermission
                                        @else
                                            @if($article->status == 'Assigned' || $article->status == 'Saved')
                                                <a class="btn btn-sm btn-success" href="/article/view/{{ $article->id }}" role="button">Open Article</a>
                                            @else
                                                <a class="btn btn-sm btn-success disabled" href="/article/view/{{ $article->id }}" disabled="disabled" role="button">Open Article</a>
                                            @endif
                                            
                                            @if ($article->status == 'Saved')
                                                <a class="btn btn-sm btn-info" href="/article/view/{{ $batch->id }}/completed/{{ $article->id }}" role="button">Mark as Completed</a>
                                            @endif
                                        @endif
                                        
                                        @permission('move-to-saved')
                                            @if ($article->status == 'Completed')
                                                <a class="btn btn-sm btn-info" href="/article/{{ $article->id }}/saved" role="button">Re-Do</a>
                                            @endif
                                        @endpermission  
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
                                
                                @php
                                
                                $focus = '';
                                if (isset($articleToFocus) && $articleToFocus !== FALSE)
                                {
                                    if ($articleToFocus ==  $article->id)
                                    {
                                        $focus = 'class="focus-row"';
                                    }
                                }
                                @endphp
                                <tr @php echo $focus; @endphp>
                                    <td>
                                        <input class="cats_art" type="checkbox" name="articles[]" value="{{ $article->id }}" />
                                    </td>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $article->article_subject }}</td>
                                    <td>{{ $article->article_url }}</td>
                                    <td>{{ $article->article_category }}</td>
                                    <td>{{ $article->user[0]->name }}</td>
                                    <td>
                                        @if ($article->assigned_at != '0000-00-00 00:00:00')
                                            {{ $article->assigned_at }}
                                        @else
                                            --
                                        @endif
                                    </td>
                                    <td>
                                        @if($article->status == 'QualityChecked')
                                            QC/Approved
                                        @else
                                            {{ $article->status }}
                                        @endif
                                    </td>
                                    <td>
                                        @if ($article->qc_at == '0')
                                            --
                                        @else
                                            {{ $users[$article->qc_at] }}
                                        @endif                                                                         
                                    </td>
                                    <td>
                                        @if (Auth::user()->hasRole(['admin', 'super-admin']))
                                            <a class="btn btn-sm btn-success" href="/article/view/{{ $article->id }}" role="button">Open Article</a>
                                            @permission('do-quality-check')
                                                @if ($article->status == 'Completed')
                                                    <!--<a class="btn btn-sm btn-success" href="/article/view/{{ $article->id }}/qc" role="button">QC Completed</a>-->
                                                @endif
                                            @endpermission
                                        @else
                                            @if($article->status == 'Assigned' || $article->status == 'Saved' || $article->status == 'Review')
                                                <a class="btn btn-sm btn-success" href="/article/view/{{ $article->id }}" role="button">Open Article</a>
                                            @else
                                                <a class="btn btn-sm btn-success disabled" href="/article/view/{{ $article->id }}" disabled="disabled" role="button">Open Article</a>
                                            @endif
                                            
                                            @if ($article->status == 'Saved')
                                                <!--<a class="btn btn-sm btn-info" href="/article/view/{{ $batch->id }}/completed/{{ $article->id }}" role="button">Mark as Completed</a>-->
                                            @endif
                                        @endif
                                        
                                        @permission('move-to-saved')
                                            @if ($article->status == 'Completed')
                                                <!--<a class="btn btn-sm btn-info" href="/article/{{ $article->id }}/saved" role="button">Re-Do</a>-->
                                            @endif
                                        @endpermission
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                        </tbody>
                </table>
                </form>
            @endif
        </div>
    @else
        Nothing to show.
    @endif
</div>
@endsection

@section('footerjs')
<script type="text/javascript">
//$(window).scrollTop($('.focus-row').position().top);
$('html, body').animate({ scrollTop: $(".focus-row").offset().top-200 }, 500);
$('.focus-row').addClass('info');
</script>
@endsection