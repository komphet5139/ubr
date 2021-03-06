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

Route::get('/',['as' => 'home','uses'=>'Home@index']);
Route::get('/home','Home@index');



Route::get('register',['middleware'=>'guest','as' => 'register','uses'=>'Member@register']);



Route::post('register',['middleware'=>'guest','as' => 'register','uses'=>'Member@register']);

Route::get('login',['middleware'=>'guest','as'=>'login','uses'=>'Member@loginForm']);
Route::post('login','Member@login');
Route::get('logout',function(){
	Auth::logout();
	return Redirect::to('/');
});

Route::get('forgetpass',['as' => 'forgetpass','uses'=>'Member@forgetpass']);
Route::post('forgetpass',['as' => 'resetpass','uses'=>'Member@resetpass']);
Route::get('checkstu/{class}/{room}',['as' => 'checkstu','uses'=>'Home@checkstu']);


//Admin

Route::get('admin',['middleware'=>'admin','as' => 'admin','uses'=>'admin@index']);
Route::post('admin/setup/update',['middleware'=>'admin','as' => 'adminSetupUpdate','uses'=>'admin@setupUpdate']);
Route::post('admin/setup/insert',['middleware'=>'admin','as' => 'adminSetupInsert','uses'=>'admin@setupInsert']);
Route::post('admin/setup/del/',['middleware'=>'admin','as' => 'adminSetupDel','uses'=>'admin@setupdel']);


Route::get('admin/studen',['middleware'=>'admin','as'=>'studen','uses'=>'Member@studenList']);
Route::post('admin/studen',['middleware'=>'admin','as'=>'studen','uses'=>'Member@studenUpdate']);
Route::get('admin/studen/view',['middleware'=>'admin','as'=>'studenView','uses'=>'Member@studenListView']);
Route::get('admin/studen/del',['middleware'=>'admin','as'=>'studenDel','uses'=>'Member@studenDel']);



//Member
Route::get('member/edit',function(){
	return view('users.editmember');
});
Route::get('member',['middleware'=>'auth','as' => 'member','uses'=>'Member@index']);

Route::get('member/yearbook',['middleware'=>'auth','as'=>'yearbook','uses'=>'Member@yearbook']);
Route::any('member/upload-pic', ['middleware'=>'auth','as'=>'uploadPic','uses'=>'Member@uploadPic']);
Route::post('member/update', ['middleware'=>'auth','as'=>'memberUpdate','uses'=>'Member@update']);
Route::any('member/yearbook/generate', ['middleware'=>'auth','as'=>'yeaBooGen','uses'=>'Member@yeaBooGen']);
Route::post('question', ['middleware'=>'auth','as'=>'questionHome','uses'=>'Home@question']);
Route::get('question/del', ['middleware'=>'auth','as'=>'questionDel','uses'=>'Home@questionDel']);
