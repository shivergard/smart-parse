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

trait CommonMeth{

	public function getAllColumnsNames($table){

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


    public function getTables(){
    	switch (Config::get('database.default')) {
			case 'sqlite':
				$tableListQuery = 'SELECT * FROM sqlite_master WHERE type="table"';
				break;
			
			default:
				$tableListQuery = 'SHOW TABLES as name';
				break;
		}
		return DB::select($tableListQuery);
    }

    public function getRandomData($name){
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

}