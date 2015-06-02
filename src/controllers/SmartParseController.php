<?php namespace Shivergard\SmartParse;

use App\Requests;

use Illuminate\Http\Request;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

use \Schema;


use \Carbon;

use \Config;
use \DB;
use \Form;

use \Input;
use \Validator;
use \Session;
use \File;
use \Redirect;

use Shivergard\SmartParse\CommonMeth;

class SmartParseController extends \Shivergard\SmartParse\PackageController {

	use CommonMeth;

	public function test(){
		return false;
	}


	public function init($full = false){
		$tables = $this->getTables();
		$finalTables = array();
		if (!$full){
			foreach ($tables as $item) {
				if (is_object($item) && isset($item->name) && strpos( $item->name , 'tmp_' ) > -1)
					$finalTables[] = $item;
			}
		}else{
			$finalTables = $tables;
		}

		return view('smart-parse::smart-parse' , array('tables' => $finalTables , 'fields' => array('name' , 'table_url')));
	}

	public function singleTable($name){

		$sampleFields = $this->getRandomData($name)->paginate(3);

		$tables = $this->getTables();
		$finalTables = array();
		foreach ($tables as $item) {
			if (!(strpos( $item->name , 'tmp_' ) > -1))
				$finalTables[$item->name] = $item->name;
		}

		return view('smart-parse::table' , 
			array(
				'fields' => $this->getAllColumnsNames($name),
				'name' => $name ,
				'list' => $sampleFields,
				'target_tables' => $finalTables
			)		
		);
	}

	public function prepareJob(Request $request){
		$validator = $this->validate($request, [
            'from' => 'required|max:255',
            'target_tables' => 'required',
        ]);

        if (is_object($validator) && $validator->fails()){
            return redirect()->back()->withErrors($validator->errors());
        }

        $rndItem = $this->getRandomData($request->input('from'))->first();

        $selectArray = array('' => '---');

        foreach ($this->getAllColumnsNames($request->input('from')) as $field) {
        	$selectArray[$field] =  $rndItem->$field;
        }


		return view('smart-parse::prepare' , array(
			'target_fields' => $this->getAllColumnsNames($request->input('target_tables')),
			'select' => $selectArray,
			'from' => $request->input('from'),
			'target_tables' => $request->input('target_tables')
		));
	}

	public function publishJob(Request $request){
		$validator = $this->validate($request, [
            'from' => 'required|max:255',
            'target_tables' => 'required',
        ]);

        if (is_object($validator) && $validator->fails()){
            return redirect()->to(action('\Shivergard\SmartParse\SmartParseController@singleTable'))->withErrors($validator->errors());
        }

        if (!Schema::hasTable('spt_jobs')){
        	Schema::create('spt_jobs', function(Blueprint $table){
				$table->increments('id');
				$table->longText('details');
				$table->string('table')->unique();
			});
        }


        DB::table('spt_jobs')->insert(
		    array('table' => $request->input('from') , 'details' => json_encode($request->all()))
		);

        return redirect()->to(action('\Shivergard\SmartParse\SmartParseController@jobList'));
	}

	public function jobList(){

		if (!Schema::hasTable('spt_jobs')){
        	Schema::create('spt_jobs', function(Blueprint $table){
				$table->increments('id');
				$table->longText('details');
				$table->string('table')->unique();
			});
        }


		return view('smart-parse::joblist' , 
			array(
				'fields' => array('id', 'table'),
				'list' => DB::table('spt_jobs')->select('id', 'table')->get(),
			)		
		);
	}

	public function upload(){
		// getting all of the post data
		  $file = array('csv' => Input::file('csv'));

		    // checking file is valid.
		    if (Input::file('csv')->isValid()) {
		      $destinationPath = storage_path().'/csv'; // upload path
		      if(!File::exists($destinationPath)) 
		      	File::makeDirectory($destinationPath);
		      $extension = Input::file('csv')->getClientOriginalExtension(); // getting image extension
		      $fileName = rand(11111,99999).'.'.$extension; // renameing image
		      Input::file('csv')->move($destinationPath, $fileName); // uploading file to given path
		      // sending back with message
		      Session::flash('success', 'Upload successfully'); 
		      return Redirect::to(action('\Shivergard\SmartParse\SmartParseController@jobList'));
		    }
		    else {
		      // sending back with error message.
		      Session::flash('error', 'uploaded file is not valid');
		      return Redirect::to(action('\Shivergard\SmartParse\SmartParseController@jobList'));
		    }
	}

}