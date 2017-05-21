<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', 'Auth\AuthController@getLogin');

//Route::auth();

// Authentication Routes...
Route::get('login', 'Auth\AuthController@getLogin');
Route::post('login', 'Auth\AuthController@login');
Route::get('logout', 'Auth\AuthController@logout');

// Password Reset Routes...
    Route::get('password/reset/{token?}', 'Auth\PasswordController@showResetForm');
    Route::post('password/email', 'Auth\PasswordController@sendResetLinkEmail');
    Route::post('password/reset', 'Auth\PasswordController@reset');

//Route::get('/home', 'HomeController@index');


Route::group(['middleware' => ['auth']], function () {
    
    $this->get('dashboard', 'DashboardController@showDashboard')->name('dashboard');
    $this->get('/', 'DashboardController@showDashboard');
    
    
    
    //User Access Level Routes
    Route::group(['middleware' => ['role:super-admin']], function() {
        Route::resource('role', 'RoleController');
        Route::resource('user', 'UserController');
        Route::get('/admin', 'UserController@index')->name('user.index');
        Route::get('/reset/user/{id}', 'UserController@sendResetEmail');
        Route::get('/reset/user/{id}/manual', 'UserController@resetUserPassword');
         Route::post('/reset/user/{id}/manual', 'UserController@postResetUserPassword')->name('reset.passwordmanually');
        Route::get('/user/restore/{id}', [
            'uses' => 'UserController@restoreUser',
            'as' => 'user.restore'
        ]);
    });
    
    Route::get('/user/password/change', function(){
        return view('user.password_reset');
    });
    Route::post('/user/password/change', 'UserController@changePassword')->name('change.password');
    
    //Only Admin is allowed to upload a batch
    Route::group(['middleware' => ['role:admin|super-admin']], function() {
        Route::get('/batch/upload', [
            'uses' => 'BatchController@upload',
            'as' => 'batch.upload'
        ]);
        Route::post('/batch/upload', [
            'uses' => 'BatchController@uploadHandler',
            'as' => 'batch.uploadHandler'
        ]);
    });
    
    //Batch Assign and Edit
    Route::group(['middleware' => ['role:admin|super-admin']], function() {
        Route::post('/batch/assign', [
            'uses' => 'BatchController@batchAssign',
            'as' => 'batch.assign'
        ]);
        Route::get('/batch/delete/{id}', [
            'uses' => 'BatchController@delete',
            'as' => 'batch.delete'
        ]);
        Route::get('/batch/edit/{id}', 'BatchController@edit');
        Route::post('/batch/edit', [
            'uses' => 'BatchController@postEdit',
            'as' => 'batch.edit'
        ]);
        Route::get('/batch/assign/{id}', 'BatchController@assignArticles');
        
        Route::post('/artilces/assign', 'ArticleController@assignArtilcesToUsers')->name('articles.assign');
        Route::get('/batch/download/{id}', 'BatchController@download');
    });
    
    Route::get('/batch/status/{id}/submit', 'BatchController@submitBatch');
    Route::get('/batch/{id}/notify', 'BatchController@notifyBatchAvailability')->name('batch.notify.available');
    
    
    $this->get('register', 'Auth\AuthController@getRegister');
    $this->post('register', 'Auth\AuthController@register');
    
    
    Route::get('/batch/show/{id}/batch/{category?}', [
        'uses' => 'BatchController@showBatchAssign',
        'as' => 'batch.show'
    ]);

    Route::get('/batch/view/{id}/batch/{category?}', [
        'uses' => 'BatchController@viewBatch',
        'as' => 'batch.view'
    ]);
    
    Route::get('/article/view/{id}', 'ArticleController@view')->name('article.view');
    Route::get('/article/view/{id}/qc', 'ArticleController@qcCompleted')->name('article.qc');
    Route::get('/article/view/{batch_id}/completed/{article_id}', 'ArticleController@completed')->name('article.completed');
    Route::get('/article/{article_id}/saved', 'ArticleController@saved')->name('article.saved');
    Route::post('/article/save', 'ArticleController@save')->name('article.save');
    Route::post('/article/comment/add', 'ArticleController@saveComment')->name('article.comment.add');
    Route::post('/article/comment/add/answer', 'ArticleController@saveAnswer')->name('article.comment.add.answer');
});    



