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

class SmartParseController extends \App\Http\Controllers\Controller {

	private function getAllColumnsNames($table){

		$this->table = $table;

        switch (\DB::connection()->getConfig('driver')) {
            case 'pgsql':
                $query = "SELECT column_name FROM information_schema.columns WHERE table_name = '".$this->table."'";
                $column_name = 'column_name';
                $reverse = true;
                break;

            case 'mysql':
                $query = 'SHOW COLUMNS FROM '.$this->table;
                $column_name = 'Field';
                $reverse = false;
                break;

            case 'sqlite':
                $query = "PRAGMA table_info(".$this->table.")";
                $column_name = 'name';
                $reverse = false;
                break;

            case 'sqlsrv':
                $parts = explode('.', $this->table);
                $num = (count($parts) - 1);
                $table = $parts[$num];
                $query = "SELECT column_name FROM ".DB::connection()->getConfig('database').".INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = N'".$table."'";
                $column_name = 'column_name';
                $reverse = false;
                break;

            default: 
                $error = 'Database driver not supported: '.DB::connection()->getConfig('driver');
                throw new Exception($error);
                break;
        }

        $columns = array();

        foreach(\DB::select($query) as $column){
            $columns[] = $column->$column_name;
        }

        if($reverse){
            $columns = array_reverse($columns);
        }

        return $columns;
    }


    private function getTables(){
    	switch (Config::get('database.default')) {
			case 'sqlite':
				$tableListQuery = 'SELECT * FROM sqlite_master WHERE type="table"';
				break;
			
			default:
				$tableListQuery = 'SHOW TABLES';
				break;
		}
		return DB::select($tableListQuery);
    }

    private function getRandomData($name){
    	switch (Config::get('database.default')) {
			case 'sqlite':
				$orderBy = 'Random()';
				break;
			
			default:
				$orderBy = 'RAND()';
				break;
		}

		return DB::table($name)->orderByRaw($orderBy);
    }

	public function test(){
		return false;
	}


	public function init(){
		$tables = $this->getTables();
		$finalTables = array();
		foreach ($tables as $item) {
			if (strpos( $item->name , 'tmp_' ) > -1)
				$finalTables[] = $item;
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