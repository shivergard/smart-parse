<?php namespace Shivergard\SmartParse;

use App\Requests;

use Illuminate\Http\Request;

use \Carbon;

use \Config;


class SmartParseController extends \App\Http\Controllers\Controller {

	public function test(){
		return false;
	}


	public function init(){
		return view('smart-parse::smart-parse');
	}

}