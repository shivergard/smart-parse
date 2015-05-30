<?php
use Illuminate\Support\Facades\Route;
use \Config;
use \Redirect;
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

Route::get('/smart-parse/table/{name}' , 'Shivergard\SmartParse\SmartParseController@singleTable');

Route::post('/smart-parse/prepare' , 'Shivergard\SmartParse\SmartParseController@prepareJob');
Route::post('/smart-parse/publish' , 'Shivergard\SmartParse\SmartParseController@publishJob');
Route::get('/smart-parse/job-list' , 'Shivergard\SmartParse\SmartParseController@jobList');
Route::get('/smart-parse/upload' , 'Shivergard\SmartParse\SmartParseController@upload');


Route::get('/smart-parse/{method}', function($method)
{
    $controller = new Shivergard\SmartParse\SmartParseController;
    if (method_exists ( $controller , $method ))
    	return $controller->{$method}();
    else
    	return Redirect::to('/');
});

Route::get('/smart-parse/{method}/{param}', function($method , $param)
{
    $controller = new Shivergard\SmartParse\SmartParseController;
    if (method_exists ( $controller , $method ))
    	return $controller->{$method}($param);
    else
    	return Redirect::to('/');
});
