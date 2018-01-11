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
                        <td>Article Status</td>
                        <td>{{ $article->status }}</td>
                    </tr>    
                
                </table>
            </div>
        </div>
        @if(count($article->comments) > 0)
        <div class="row well well-sm">
            <h4>Comments</h4>
            <hr />        
            <!--<ul class="list-group">-->
            @foreach($article->comments->sortByDesc('created_at') as $comment)
            <div class="list-group">
                <a href="#" class="list-group-item">
                    <div class="row">
                        <div class="col-md-6"><h5 class="list-group-item-heading">By User : {{ $comment->user->name }}</h5></div>
                        <div class="col-md-6"><h5 class="list-group-item-heading">Timestamp : {{ $comment->created_at }}</h5></div>
                    </div>
                    <hr />
                    <!--<p><u>Comment:</u></p>-->
                    <p class="list-group-item-text">{!! $comment->comment !!}</p>
                    @if (count($comment->answers) > 0 )
                        <ul>
                        @foreach($comment->answers as $answer)
                            <li><strong>{{ $answer->user->name }}</strong> - {{ $answer->created_at }} {!! $answer->comment !!}</li>
                        @endforeach
                        </ul>
                    @endif
                    <button class="btn btn-sm btn-primary open-form" id="open-form-{{ $comment->id }}" type="submit">Add a Reply</button>
                </a>
            </div>
            <div class="add-answers-form" id="form-open-form-{{ $comment->id }}">
                <h4>Add a Reply:</h4>
                <form class="form" method="post" action="{{ route('article.comment.add.answer') }}" name="form-add-comment-answer" id="form-add-comment-answer">
                  {{ csrf_field() }}
                
                @if (Auth::user()->hasRole(['admin', 'super-admin']))  
                <div class="checkbox">
                    <label>
                      <input type="checkbox" name="flag_review" id="flag_review" value="1"> Flag to review
                    </label>
                </div>
                @endif
                
                <input type="hidden" name="article_id" id="article_id" value="{{ $article->id }}" />
                <input type="hidden" name="c_article_comment_id" id="c_article_comment_id" value="{{ $comment->id }}" />
                  
                <div class="form-group">
                    <textarea name='article_answer' id="article_answer" class="form-control" rows="3"></textarea>
                </div>
                <input class="btn btn-default" type="submit" value="Submit">
                </form>
            </div>
            @endforeach
            <!--</ul>-->
            
        </div>
        @endif
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
                        <textarea name='article_content' id="article_content" class="form-control">@if (!empty($article->detail->description)) {{ $article->detail->description }} @endif</textarea>
                    </div>

                </div>
                <div class="col-md-3">
                    
                    <div class="row">
                        <div class="col-md-6">Last Updated at:</div>
                        <div class="col-md-6">{{ $article->updated_at }}</div>
                    </div>
                    
                    <hr />
                    <div class="col-md-12">
                    @if ($article->status == 'QualityChecked' || $article->status == 'Final')
                        <div class="alert alert-danger" role="alert">Quality check has been completed but you can make changes to article and save or can Re-Do.!</div>
                        <hr />
                    @endif
                    </div>
                    
                    @if (Auth::user()->hasRole(['admin', 'super-admin']))
                        <div class="col-md-12">
                            <button class="btn btn-primary pull-left btn-block" type="submit">Save Article</button>
                        </div>
                        @if ($article->status == 'Saved' || $article->status == 'EditsSaved')
                            <div class="col-md-12">    
                                <a class="btn btn-completed btn-sm btn-info btn-block" href="/article/view/{{ $article->batch_id }}/completed/{{ $article->id }}" role="button">Mark as Completed</a>
                            </div>
                        @endif
                        
                        @if ($article->status == 'Completed' || $article->status == 'QualityChecked' || $article->status == 'EditsCompleted' || $article->status == 'Final')
                            @if ($article->status != 'QualityChecked' && $article->status != 'Final')
                                <div class="col-md-12 margin-bottom-10px">    
                                    <button type="button" class="btn btn-sm btn-success btn-block btn-qc-complete" role="button">Mark QC Completed</button>
                                    <!--<a class="btn btn-sm btn-success btn-block" href="/article/view/{{ $article->id }}/qc" role="button">Mark QC Completed</a>-->
                                </div>
                            @endif
                            @if ($article->status == 'QualityChecked')
                                <hr />    
                                <div class="col-md-12 margin-bottom-10px">    
                                    <!--<a class="btn btn-sm btn-success btn-block" href="/article/status/{{ $article->id }}/fs" role="button">Final Checks Completed</a>-->
                                    <button class="btn btn-sm btn-success btn-block btn-fc-complete" role="button">Final Checks Completed</button>
                                </div>
                                <hr />
                            @endif
                            <div class="col-md-12">    
                                <!--<a class="btn btn-sm btn-warning btn-block" href="/article/{{ $article->id }}/saved" role="button">Re-Do without Comment</a>-->
                            </div>
                            <div class="col-md-12">    
                                <button type="button" class="btn btn-primary btn-block" data-toggle="modal" data-target=".bs-example-modal-lg">Add Comment & Re-Do</button>
                            </div>
                        @endif
                        
                    @else
                        @if ($article->status == 'Assigned' || $article->status == 'Saved' || $article->status == 'Review' || $article->status == 'EditsCompleted')
                            <div class="col-md-12">
                                <button class="btn btn-primary pull-left btn-block" type="submit">Save Article</button>
                            </div>
                        @endif
                        
                        @if ($article->status == 'Saved' || $article->status == 'EditsSaved')
                            <div class="col-md-12">    
                                <a class="btn btn-sm btn-completed btn-info btn-block" href="/article/view/{{ $article->batch_id }}/completed/{{ $article->id }}" role="button">Mark as Completed</a>
                            </div>
                        @endif
                        
                        @if ($article->status == 'Completed')
                            <div class="alert alert-danger" role="alert">No changes can be made now, article submitted for QC.</div>
                        @endif
                        
                    @endif
                
                </div>
            </form>
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
          setup:function(ed) {
               ed.on('change', function(e) {
                   $('.btn-completed').addClass('btn-block');
                   $('.btn-completed').atr('disbaled', 'disabled');
               });
           },
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
        
        $('.btn-fc-complete').on('click', function(){
            $('#article_status').val('Final');
            $('#form_article').submit();
        });
        
        $('.btn-qc-complete').on('click', function(){
            $('#article_status').val('QualityChecked');
            $('#form_article').submit();
        });
        
        
        $('.open-form').on('click', function(){
            id = $(this).attr('id');
            //$('.add-answers-form'+id).show();
            $('#form-'+id).show();
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

