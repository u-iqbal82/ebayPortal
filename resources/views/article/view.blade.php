@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <h4>Article Details</h4>
            <hr />
            @if(count($errors))
                @foreach($errors->all() as $error)
                    <div class="alert alert-danger">{{ $error }}</div>
                @endforeach
            @endif
            <div class="col-md-12">
                <table class="table table-borderless table-striped">
                    <tr>
                        <td><b>Subject</b></td>
                        <td>{{ $article->article_subject }}</td>
                        
                        <td><b>Assigned To</b></td>
                        <td>{{ $article->user->first()->name }}</td>
                    </tr>
                    <tr>
                        <td><b>Category</b></td>
                        <td>{{ $article->article_category }}</td>
                        <td>Batch</td>
                        <td><a href="/batch/view/{{ $article->batch_id }}/batch">{{ $article->batch->name }}</a></td>
                    </tr>
                    <tr>
                        <td><b>URL</b></td>
                        <td><a href="{{ $article->article_url }}">{{ $article->article_url }}</a></td>
                    </tr>    
                
                </table>
            </div>
        </div>
        <div class="row">
            <h4>Article Content</h4>
            <hr />
            <form method="post" action="{{ route('article.save') }}" id="form_article">
                <div class="col-md-9">
                
                    {{ csrf_field() }}
                    
                    <input type="hidden" name="batch_id" id="batch_id" value="{{ $article->batch_id }}" />
                    <input type="hidden" name="article_id" id="article_id" value="{{ $article->id }}" />
                    <input type="hidden" name="article_status" id="article_status" value="{{ $article->status }}" />
                    
                    <div class="form-group">
                        <textarea name='article_content' class="form-control">@if (!empty($article->detail->description)) {{ $article->detail->description }} @endif</textarea>
                    </div>

                </div>
                <div class="col-md-3">
                    
                    <div class="row">
                        <div class="col-md-6">Last Updated at:</div>
                        <div class="col-md-6">{{ $article->updated_at }}</div>
                    </div>
                    
                    <hr />
                    @if ($article->status == 'QualityChecked')
                        <div class="alert alert-danger" role="alert">Quality check has been completed!</div>
                    @endif
                    
                    @if ($article->status != 'Completed' || ($article->status == 'Completed' && ($article->batch->status == 'Submitted' || $article->batch->status == 'QCInProcess')))
                    <div class="col-md-12">
                        <button class="btn btn-primary pull-left btn-block" type="submit">Save Article</button>
                    </div>
                    @endif
                    @if ($article->status == 'Saved')
                    <div class="col-md-12">    
                        <a class="btn btn-sm btn-info btn-block" href="/article/view/{{ $article->batch_id }}/completed/{{ $article->id }}" role="button">Mark as Completed</a>
                    </div>
                    @endif
                    
                    @if ($article->status == 'Completed' )
                    <div class="col-md-12 margin-bottom-10px">    
                        <a class="btn btn-sm btn-success btn-block" href="/article/view/{{ $article->id }}/qc" role="button">QC Completed</a>
                    </div>
                    <div class="col-md-12">    
                        <a class="btn btn-sm btn-warning btn-block" href="/article/{{ $article->id }}/saved" role="button">Re-Do without Comment</a>
                    </div>
                    <div class="col-md-12">    
                        <button type="button" class="btn btn-primary btn-block" data-toggle="modal" data-target=".bs-example-modal-lg">Add Comment & Re-Do</button>
                    </div>
                    @endif
                    
                    
                </div>
            </form>
        </div>
        
        <div class="well well-sm">
            <h4>Comments</h4>
            <hr />        
            <ul class="list-group">
            @foreach($article->comments->sortByDesc('created_at') as $comment)
            <div class="list-group">
                <a href="#" class="list-group-item">
                    <div class="row">
                        <div class="col-md-6"><h5 class="list-group-item-heading">By User : {{ $comment->user_id }}</h5></div>
                        <div class="col-md-6"><h5 class="list-group-item-heading">Timestamp : {{ $comment->created_at }}</h5></div>
                    </div>
                    <hr />
                    <p><u>Comment:</u></p>
                    <p class="list-group-item-text">{!! $comment->comment !!}</p>
                </a>
            @endforeach
            </ul>
        </div>
        </div>
    </div>
    <div class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="gridSystemModalLabel">Add a comment</h4>
        <div class="form-group pull-right">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            <button type="button" class="btn btn-primary save-comment">Save changes</button>
        </div>
      </div>
      <div class="modal-body">
          <form class="form" method="post" action="{{ route('article.comment.add') }}" name="form-add-comment" id="form-add-comment">
              {{ csrf_field() }}
                    
            <input type="hidden" name="c_batch_id" id="c_batch_id" value="{{ $article->batch_id }}" />
            <input type="hidden" name="c_article_id" id="c_article_id" value="{{ $article->id }}" />
            <input type="hidden" name="c_article_status" id="c_article_status" value="{{ $article->status }}" />
              
            <div class="form-group">
                <textarea name='article_comment' id="article_comment" class="form-control" rows="10"></textarea>
            </div>
            </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary save-comment">Save changes</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
@endsection
@section('footerjs')
    <script type="text/javascript" src="{{ URL::asset('js/tinymce/js/tinymce/tinymce.min.js') }}"></script>
    <script type="text/javascript">
        tinymce.init({
          selector: 'textarea',
          browser_spellcheck: true,
          paste_as_text: true,
          height: 500,
          menubar: "tools",
          plugins: [
            'link preview anchor',
            'searchreplace visualblocks code fullscreen',
            'paste code visualchars spellchecker wordcount'
          ],
          toolbar: 'undo redo | styleselect | link | spellchecker | pastetext | searchreplace | removeformat',
        });
        
        $('#mark_as_completed').on('click', function(){
            $('#article_status').val('Completed');
            $('#form_article').submit();
        });
        
        $('.save-comment').click(function(){
            //var commentLength = $('#article_comment').val().length;
            
            //if (commentLength > 10)
            //{
                $('#form-add-comment').submit();    
            //}
        });
        
    </script>
@endsection

