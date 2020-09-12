<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['middleware' => 'auth:api'], function () {
    Route::resource('classroom', 'ClassroomController');
    Route::get('classroom2/{classroom_id}', 'ClassroomController@show2');
    Route::resource('group', 'GroupController');
    Route::post('classroom/join', 'ClassroomController@join');
    Route::resource('post', 'PostController');
    Route::post('classroom/{classroom_id}/cover', 'ClassroomController@cover');
    Route::get('classroom/{classroom_id}/get', 'ClassroomController@getClassroom');
    Route::resource('classroom/{classroom_id}/assignment', 'AssignmentController');
    Route::post('classroom/{classroom_id}/assignment/{id}/upload', 'AssignmentController@upload');
    Route::post('classroom/{classroom_id}/assignment/{id}/remove', 'AssignmentController@removeFile');
    Route::get('classroom/{classroom_id}/assignment/{id}/confirm', 'AssignmentController@confirm');
    Route::get('classroom/{classroom_id}/assignment/{id}/done', 'AssignmentController@alldone');
    Route::get('classroom/{classroom_id}/assignment/{id}/post', 'AssignmentController@getPost');
    Route::patch('classroom/{classroom_id}/assignment/{id}/post', 'AssignmentController@editPost');
    Route::resource('comments', 'CommentController');
    // Route::get('student/{classroom_id}', 'MemberController@student');
    // Route::get('studentmember/{group_id}', 'MemberController@studentmember');
    // Route::get('teacher/{classroom_id}', 'MemberController@teacher');
    Route::get('members/{classroom_id}', 'MemberController@show');
    Route::get('membersstu/{classroom_id}', 'MemberController@showstu');
    Route::get('memberstu/{classroom_id}', 'MemberController@showtu');
    // Route::resource('members', 'MemberController', ['except' => ['show']])->middleware('role:is_teacher');
    Route::resource('members', 'MemberController');
    Route::post('members/add', 'MemberController@add')->middleware('role:is_teacher');
    Route::post('members/addmember', 'MemberController@addmember')->middleware('role:is_student');
    Route::post('members/remove', 'MemberController@remove')->middleware('role:is_teacher');
    Route::get('me', 'UserController@index');
    Route::get('meandi', 'UserController@indexme');
    Route::patch('me', 'UserController@update');
    Route::patch('mestatus', 'UserController@updatestatus');
    Route::patch('accept', 'UserController@accept');
    Route::patch('me/password', 'UserController@updatePassword');
    Route::post('me/avatar/upload', 'UserController@uploadProfile');
    Route::post('avatar/upload', 'UserController@uploadTempProfile');
    Route::get('assignment/download/{file_id}', 'FileController@assignment');
    Route::get('attachment/download/{file_id}', 'FileController@attachment');
    Route::post('attachment/upload', 'FilesAttachmentController@upload');
    Route::post('user', 'UserController@store')->middleware('role:is_teacher');
    Route::get('user', 'UserController@show')->middleware('role:is_teacher');
    Route::get('user2', 'UserController@show2');
    Route::get('user3', 'UserController@show3');
    Route::get('user/{user_id}', 'UserController@getUser')->middleware('role:is_teacher');
    Route::patch('user/{user_id}', 'UserController@edit')->middleware('role:is_teacher');
    Route::delete('user/{user_id}', 'UserController@destroy')->middleware('role:is_teacher');
    Route::get('role', 'RoleController@index')->middleware('role:is_teacher');
    Route::post('classroom/{classroom_id}/assignment/{id}/storereply', 'AssignmentController@storereply');

    Route::post('send-mail', 'MailsendController@mailsend');
});
