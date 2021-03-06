<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Http\Requests;
use App\Batch;
use App\User;
use App\Article;
use Auth;

class DashboardController extends Controller
{
    public function showDashboard($flag = 'false')
    {
        if (Auth()->user()->is('super-admin') || Auth()->user()->is('admin'))
        {
            $batches = Batch::where('status_final', '<>', 'Archived')->orderBy('id', 'desc')->get();
            
            if ($flag == 'archived')
            {
                $batches = Batch::where('status_final', '=', 'Archived')->orderBy('id', 'desc')->get();
            }
        }
        else
        {
            //$batches = Batch::all();
            
            /**
            $batchesByLoggedInUser = DB::table('batches')
            ->join('articles', 'batches.id', '=', 'articles.batch_id')
            ->join('article_user', 'articles.id', '=', 'article_user.article_id')
            ->select('batches.*', 'articles.*')
            ->where('article_user.user_id', '=', Auth::user()->id)
            ->get();
            **/
            
            $batchess = [];
            $articlesForThisUser = User::find(Auth::user()->id)->articles()->get();    
            
            foreach($articlesForThisUser as $article)
            {
                $batchess[] = $article->batch_id;
            }
            
            $batchess = array_unique($batchess);
            
            //$batches = Batch::find($batchess);
            
            foreach($batchess  as $b)
            {
                if ($flag == 'archived')
                {
                    $checkForBatch = Batch::where('id', $b)->where('status_final', '=', 'Archived')->orderBy('id', 'desc')->get();
                }
                else
                {
                    $checkForBatch = Batch::where('id', $b)->where('status_final', '<>', 'Archived')->orderBy('id', 'desc')->get();
                }
            
                if (isset($checkForBatch[0]))
                {
                    $batches[] = $checkForBatch[0];    
                }
            }
            
            
            
            //dd($batches);
            
            
        }
        /**
        else
        {
            $batches = DB::table('batches')
            ->join('articles', 'batches.id', '=', 'articles.batch_id')
            ->join('article_user', 'articles.id', '=', 'article_user.article_id')
            ->select('batches.*', 'articles.*')
            ->where('article_user.user_id', '=', Auth::user()->id)
            ->get();
            
            dd($batches);
            
            $batches = [];
            $articlesForThisUser = User::find(Auth::user()->id)->articles()->get();    
            
            foreach($articlesForThisUser as $article)
            {
                $batches[$article->batch_id] = Batch::find($article->batch_id);
            }
            
            
            //dd($batches);
        }
        **/
        return view('dashboard', compact('batches', 'flag'));
    }
}
