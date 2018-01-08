<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Article;
use App\ArticleDetail;
use App\ArticleComment;
use App\CommentAnswers;
use App\User;
use App\Batch;
use App\Events\BatchUpdated;
use App\Events\ArticleUpdated;
use Illuminate\Support\Facades\Event;
use Auth;

class ArticleController extends Controller
{
    public function updateArticlesByArticleId(Request $request)
    {
        $validationRules = [
            'batch_id' => 'required|integer',
            'update_status_to' => 'required'
        ];
            
        $this->validate($request, $validationRules);
        
        $batchId = $request->batch_id;
        $statusToUpdate = $request->update_status_to;
        $articles = $request->articles;
        
        if ($statusToUpdate == 'false')
        {
            return \Redirect::route('batch.view', ['id' => $batchId])->with('fail', 'Please select "status" to update from the dropdown!');
        }
        
        if (count($articles) > 0)
        {
            foreach($articles as $articleId)
            {
                $article = Article::find($articleId);
                
                if ($article->status != $statusToUpdate)
                {
                    $article->status = $statusToUpdate;
                    $article->save();
                }
            }
            
            return \Redirect::route('batch.view', ['id' => $batchId])->with('success', 'Article status updated!');
        }
        else
        {
            return \Redirect::route('batch.view', ['id' => $batchId])->with('fail', 'Please select articles to update!');
        }
    }
    
    public function articleFinalised($id)
    {
        $article = Article::find($id);
        $article->status = 'Final';
        $article->save();
        
        Event::fire(new ArticleUpdated($article));
        
        return \Redirect::route('batch.view', ['id' => $article->batch_id])->with('success', 'Article status updated!');
    }
    
    public function qcCompleted($id)
    {
        $article = Article::find($id);
        $article->status = 'QualityChecked';
        $article->qc_at = Auth::user()->id;
        $article->save();
        
        Event::fire(new ArticleUpdated($article));
        
        return \Redirect::route('batch.view', ['id' => $article->batch_id])->with('success', 'Article status updated!');
    }
    
    public function saveAnswer(Request $request)
    {
        $validationRules = [
            'c_article_comment_id' => 'required|integer',
            'article_answer' => 'required|min:10'
        ];
        
        $this->validate($request, $validationRules);
        
        $articleComment = ArticleComment::find($request->c_article_comment_id);
        
        if (!empty($articleComment))
        {
            $answer = new CommentAnswers();
            $answer->comment = $request->article_answer;
            $answer->user_id = Auth::user()->id;
            
            $articleComment->answers()->save($answer);
            
            if (isset($request->flag_review))
            {
                $article = Article::find($request->article_id);
                if (!empty($article))
                {
                    $article->status = 'Review';
                    $article->save();
                }
            }
            
            return redirect()->back()->with('success', 'Comment added!');
        }
        
        return redirect()->back()->with('fail', 'Unable to save the comment.');
    }
    
    public function saveComment(Request $request)
    {
        $validationRules = [
            'c_batch_id' => 'required|integer',
            'c_article_id' => 'required|integer',
            'c_article_status' => 'required',
            'article_comment' => 'required|min:10'
        ];
            
        $this->validate($request, $validationRules);
        
        $article = Article::find($request->c_article_id);
        $batch = Batch::find($article->batch_id);
        
        //if ($batch->status == 'QCInProcess' || $batch->status == 'Submitted')
        //{
            $comment = new ArticleComment();
            $comment->comment = $request->article_comment;
            $comment->user_id = Auth::user()->id;
            
            $article->status = 'Review'; //Saved
            $article->save();
            
            $article->comments()->save($comment);
            
            
            return redirect()->back()->with('success', 'Comment added and Article moved to re-do stage.');
        //}

        //return redirect()->back();
    }
    
    
    public function saved($id)
    {
        $article = Article::find($id);
        $batch = Batch::find($article->batch_id);
        
        $article->status = 'Saved';
        $article->save();
        
        /**
        if ($batch->status == 'QCInProcess')
        {
            $article->status = 'Saved';
            $article->save();
            
            return redirect()->back()->with('success', 'Article moved to re-do stage.');
        }
        
        
        if ($batch->status == 'Completed' || $batch->status == 'Submitted' || $batch->status == 'QualityChecked')
        {
            $batch->status = 'InProcess';
            $batch->save();
            
            Event::fire(new BatchUpdated($batch));
        }
        **/
        
        return redirect()->back()->with('success', 'Article moved to re-do stage.');
        
        //return view('article.view', compact('article'))->with('success', 'Article moved from Completed to Saved status.');
        
    }
    public function completed($batch_id, $article_id)
    {
        $article = Article::find($article_id);
        
        if ($article->status == 'EditsSaved')
        {
            $article->status = 'EditsCompleted';
        }
        else
        {
            $article->status = 'Completed';
        }
        
        $article->save();
        
        $batch = Batch::find($batch_id);
        
        $isBatchCompleted = true;
        foreach($batch->articles as $art)
        {
            if ($art->status == 'Saved' || $art->status == 'Assigned')
            {
                $isBatchCompleted = false;
            }
        }
        
        if ($isBatchCompleted === TRUE)
        {
            if ($batch->status == 'InProcess')
            {
                $batch->status = 'Completed';
                $batch->save();
                
                Event::fire(new BatchUpdated($batch));
            }
        }
        
       return \Redirect::route('batch.view', ['id' => $batch_id])->with('success', 'Article marked as completed!');
    }
    
    public function save(Request $request)
    {
        $validationRules = [
            'batch_id' => 'required|integer',
            'article_id' => 'required|integer',
            'article_status' => 'required',
            'article_content' => 'required|min:10'
        ];
            
        $this->validate($request, $validationRules);
        
        $batchId = $request->batch_id;
        $articleId = $request->article_id;
        $articleStatus = $request->article_status;
        $articleContent = $request->article_content;
        
        $article = Article::find($articleId);
        
        if ($articleStatus == 'Assigned' || $articleStatus == 'Review')
        {
            if ($articleStatus == 'Review')
            {
                $articleStatus = 'EditsSaved';
            }
            else
            {
                $articleStatus = 'Saved';
            }
        }
        
        $article->status = $articleStatus;
        $article->save();
        
        $article->touch();
        
        $articleDetail = new ArticleDetail();
        $articleDetail->description = $request->article_content;
        $article->detail()->delete();
        $article->detail()->save($articleDetail);

        if ($articleStatus == 'Saved' || $articleStatus == 'Completed')
        {
            $batch = Batch::find($batchId);
            
            if ($batch->status == 'FullyAssigned')
            {
                $batch->status = 'InProcess';
                $batch->save();    
            } 
            else 
            {
                if ($batch->status == 'QCInProcess')
                {
                    $article->status = 'Saved';
                    $article->save();
                }
            }
            return redirect()->route('article.view', compact('article'));
        }
            
        return \Redirect::route('batch.view', ['id' => $batchId])->with('success', 'Article updated!');
    }
    
    public function view($id)
    {
        $article = Article::find($id);
        
        return view('article.view', compact('article'));
    }
    
    public function assignArtilcesToUsers(Request $request)
    {
        $validationRules = [
                    'users' => 'required',
                    'articles' => 'required',
                    'batch_id' => 'required'
                ];
            
        $this->validate($request, $validationRules);
        
        $batchId = $request->batch_id;
        
        $numberOfUsers = count($request->users);
        $numberOfArticles = count($request->articles);
        
        $articlesPerUser = $numberOfArticles / $numberOfUsers;
        
        $articlesPerUser = (int) $articlesPerUser;
        $leftoverArticles = $numberOfArticles - ($numberOfUsers * $articlesPerUser);
        
        $articles = $request->articles;
        $users = $request->users;
        
        $articleCounter = 0;
        
        foreach($request->users as $userId)
        {
            $articleCounter = 0;
            foreach($articles as $key => $articleId)
            {
                $art = Article::find($articleId);
                $art->status = 'Assigned';
                $art->assigned_at = date('Y-m-d H:i:s');
                $art->save();
                
                $user = User::find($userId);
                $user->articles()->attach($art);
                
                unset($articles[$key]);
                
                ++$articleCounter;
                
                if ($articleCounter == $articlesPerUser)
                {
                    $articleCounter = 0;
                    continue 2;
                }

            }
        }
        
        $articleCounter = 0; 
        if ($leftoverArticles > 0)
        {
            foreach($articles as $key => $articleId)
            {
                foreach($users as $keyU => $userId)
                {
                    $art = Article::find($articleId);
                    $art->status = 'Assigned';
                    $art->save();
                    
                    $user = User::find($userId);
                    $user->articles()->attach($art);
                    
                    unset($articles[$key]);
                    
                    ++$articleCounter;
                    
                    if ($articleCounter == $leftoverArticles)
                    {
                        continue 2;
                    }
                }
            }
        }
        
        if (count($articles) == 0)
        {
            $batch = Batch::find($batchId);
            $totalUnAssigned = count($batch->articles->where('status', 'UnAssigned'));
            
            if ($totalUnAssigned == 0)
            {
                $batch->status = 'FullyAssigned';
                
            }
            else
            {
                $batch->status = 'PartiallyAssigned';
            }
            $batch->save();
            
            return redirect()->route('dashboard')->with('success', 'Selected articles are now assigned to Users!');
        }
        
        return back()->with('fail', 'Task not assigned, please check!');
    }
}
