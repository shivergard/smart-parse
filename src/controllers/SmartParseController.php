<?php namespace Shivergard\SmartParse;

use App\Requests;

use Illuminate\Http\Request;

use \Carbon;

use \Config;
use \DB;


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

	public function test(){
		return false;
	}


	public function init(){
		
		switch (Config::get('database.default')) {
			case 'sqlite':
				$tableListQuery = 'SELECT * FROM sqlite_master WHERE type="table"';
				break;
			
			default:
				$tableListQuery = 'SHOW TABLES';
				break;
		}
		$tables = DB::select($tableListQuery);

		$finalTables = array();
		foreach ($tables as $item) {
			if (strpos( $item->name , 'tmp_' ) > -1)
				$finalTables[] = $item;
		}

		return view('smart-parse::smart-parse' , array('tables' => $finalTables , 'fields' => array('name' , 'table_url')));
	}

	public function singleTable($name){

		switch (Config::get('database.default')) {
			case 'sqlite':
				$orderBy = 'Random()';
				break;
			
			default:
				$orderBy = 'RAND()';
				break;
		}

		$sampleFields = DB::table($name)->orderByRaw($orderBy)->paginate(3);

		return view('smart-parse::table' , 
			array(
				'fields' => $this->getAllColumnsNames($name),
				'name' => $name ,
				'list' => $sampleFields,
			)		
		);
	}

}