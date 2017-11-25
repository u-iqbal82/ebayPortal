<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;

use App\Http\Requests;
use Response;
use Illuminate\Support\Facades\Storage;
use App\Batch;
use App\Article;
use App\User;
use App\Role;
use App\Events\BatchUpdated;
use App\Events\BatchAvailableNotification;
use Illuminate\Support\Facades\Event;

class BatchController extends Controller
{
    public function notifyBatchAvailability($id)
    {
        $batch = Batch::find($id);
        
        if (!empty($batch))
        {
            Event::fire(new BatchAvailableNotification($batch));
            return redirect()->route('dashboard')->with('success', 'Batch Notification emails have been sent to the users!');
        }
        
        return redirect()->route('dashboard')->with('fail', 'Batch Notification not sent!');
    }
    public function download($id)
    {
        $import = new FileImport();
        $import->export($id);
    }
    
    public function downloadOld($id)
    {
        $batch = Batch::find($id);
        $articles = Article::where('batch_id', $id)->with('detail')->get();
        
        //dd($articles);
        
        $output = implode(",", ['article_url', 'article_subject', 'article_category', 'description']) . "\r\n";
        foreach($articles as $article)
        {
            $output .= implode(",", [$article->article_url, $article->article_subject, $article->article_category, str_replace('\r\n', '', $article->detail->description)]) . "\r\n";
        }
        
        $headers = array(
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="'.str_replace(' ', '', $batch->name).'.csv"',
        );
        
        return Response::make(rtrim($output, "\n"), 200, $headers);
    }
    
    public function submitBatch($id)
    {
        $batch = Batch::find($id);
        $batch->status = 'Submitted';
        $batch->save();
        
        Event::fire(new BatchUpdated($batch));
        
        return redirect()->route('dashboard')->with('success', 'Batch Submitted!');
    }
    
    public function assignArticles($id)
    {
         //$batch = Batch::find($id)->articles()->where('status', 'UnAssigned')->get();
         $batch = Batch::find($id);
         $users = User::all();

         $roles = Role::with('users')->where('name', 'freelancer')->get();
         
         //dd($roles);
         return view('batch.assign', compact('batch', 'users', 'roles'));
    }
    
    public function delete($id)
    {
        $batch = Batch::find($id);
        $batch->delete();
        
        return redirect()->route('dashboard')->with('success', 'Batch Deleted!');
    }
    
    public function edit($id)
    {
        $batch = Batch::find($id);
        return view('batch.edit', compact('batch'));
    }
    
    public function postEdit(Request $request)
    {
        $validationRules = [
                    'batch_name' => 'required',
                    'batch_id' => 'required|integer'
                ];
            
        $this->validate($request, $validationRules);
        
        $batch = Batch::find($request->batch_id);
        $batch->name = $request->batch_name;
        $batch->save();
        
        return redirect()->route('dashboard')->with('success', 'Batch Updated!');
        
    }
    
    public function batchAssign(Request $request){
        
         $validationRules = [
                    'batch_name' => 'required',
                    'batch_id' => 'required|integer',
                    'articles' => 'required'
                ];
            
            $this->validate($request, $validationRules);
            
            $batchId = $request->batch_id;
            $batchName = $request->batch_name;
            $articlesInBatch = $request->articles;
            
            $numberOfArticlesInBatch = Article::where('batch_id', $batchId)->count();
            
            if ($numberOfArticlesInBatch == count($articlesInBatch))
            {
                $batch = Batch::find($batchId);
                
                if (!empty($batch))
                {
                    $batch->name = $batchName;
                    $batch->save();
                }
                
                return redirect()->route('dashboard')->with('success', 'Batch Updated!');
            }
            else
            {
                $batch = Batch::find($batchId);
                
                $newBatch = new Batch();
                $newBatch->name = $batchName;
                $newBatch->file_name = $batch->file_name;
                $newBatch->status = 'Created';
                $newBatch->upload_user_id = Auth::user()->id;
                $newBatch->save();
                
                $newBatchId = $newBatch->id;
                
                if (count($articlesInBatch) > 0)
                {
                    foreach($articlesInBatch as $article)
                    {
                        $art = Article::find($article);
                        $art->batch_id = $newBatchId;
                        $art->save();
                    }
                    
                    $oldBatch = Batch::find($batchId);
                    if (count($oldBatch->articles) == 0)
                    {
                        $oldBatch->delete();
                        return redirect()->route('dashboard')->with('success', 'Batch Updated!');
                    }
                    else
                    {
                        return back()->with('success', 'New batch created!.');
                    }
                }
                
            }
    }
    
    public function showBatchAssign($id, $category = false)
    {
        
        if ($category !== false)
        {
            $categorySelected = explode('/', $category);
            $category = $categorySelected[count($categorySelected)-1];
        }
        else
        {
            $category = 'all';
        }
        
        $batch = Batch::find($id);    
        return view('batch.batch', compact('batch', 'category'));
    }
    
    protected function getUsersAsArray()
    {
        $usersCache = array();
        
        $users = User::all();
        if (count($users) > 0)
        {
            foreach($users as $user)
            {
                $usersCache[$user->id] = $user->name;
            }
        }
        unset($users);
        
        return $usersCache;
    }
    
    public function viewBatch($id, $category = false)
    {
        
        if ($category !== false)
        {
            $categorySelected = explode('/', $category);
            $category = $categorySelected[count($categorySelected)-1];
        }
        else
        {
            $category = 'all';
        }
        
        $batch = Batch::find($id);
        $users = $this->getUsersAsArray();
        
        $usersInPlace = array();
        
        $articles = Batch::find($id)->articles()->get();
        
        //dd($articles);
        
        $usersAlreadySelected = [];
        
        foreach($articles as $article)
        {
            $userId = 0;
            
            //$user = $article->user()->first();
            
            //dd($user);
            
            foreach($article->user as $user)
            {
                if (!isset($usersInPlace[$user->id]['number_of_articles']))
                {
                    $usersInPlace[$user->id]['number_of_articles'] = 0;
                    $userId = $user->id;
                    $usersInPlace[$user->id]['Saved'] = 0;
                    $usersInPlace[$user->id]['Assigned'] = 0;
                    $usersInPlace[$user->id]['UnAssigned'] = 0;
                    $usersInPlace[$user->id]['Completed'] = 0;
                    $usersInPlace[$user->id]['Review'] = 0;
                    $usersInPlace[$user->id]['QualityChecked'] = 0;
                    $usersInPlace[$user->id]['EditsCompleted'] = 0;
                    $usersInPlace[$user->id]['EditsSaved'] = 0;
                }
                
                $usersInPlace[$user->id]['name'] = $user->name;
                $usersInPlace[$user->id]['number_of_articles'] = $usersInPlace[$user->id]['number_of_articles'] + 1;
                
                $status = $article->status;
                
                if ($status == 'EditsCompleted' || $status == 'EditsSaved')
                {
                    $status = 'Review';
                }
                
                //$usersInPlace[$user->id][$article->status] = $usersInPlace[$user->id][$article->status] + 1;
                $usersInPlace[$user->id][$status] = $usersInPlace[$user->id][$status] + 1;
            }
        }

        
        return view('batch.view', compact('batch', 'category', 'usersInPlace', 'users'));
    }
    
    public function upload()
    {
        return view('batch.upload');
    }
    
    public function uploadHandler(Request $request)
    {
        //if ($request->hasFile('file'))
        //{
            $file = $request->file('file');
            
            $allowedFileTypes = Config('app.allowedFileTypes');
            $maxFileSize = Config('app.maxFileSize');
            
            $validationRules = [
                    'file' => 'required|mimes:'.$allowedFileTypes.'|max:'.$maxFileSize
                ];
            
            $this->validate($request, $validationRules);
            
            $fileName = $file->getClientOriginalName();
            $destinationPath = Config('app.fileDestinationPath') . '/' . $fileName;
            
            $uploaded = Storage::put($destinationPath, file_get_contents($file->getRealPath()));
            
            if ($uploaded)
            {
                $batch = new Batch();
                $batch->name = $request->name;
                $batch->file_name = $fileName;
                $batch->upload_user_id = Auth::user()->id;
                $batch->status = 'Created';
                $batch->save();
                
                $batchId = $batch->id;
                
                if (trim($request->name) == '')
                {
                    $batch->name = 'Temp' . $batchId;
                    $batch->save();
                }
                
                //dd(Storage::disk('local'));
                
                $import = new FileImport();
                $import->import(Storage::disk('local')->getDriver()->getAdapter()->getPathPrefix() . $destinationPath, $batchId);
            
                
                return redirect()->route('batch.upload')->with('success', 'Batch file uploaded! Click on Dashboard to view the batch.');
            }
            
            return redirect()->route('batch.upload')->with('fail', 'Unable to upload file!');
        //}
    }
}
