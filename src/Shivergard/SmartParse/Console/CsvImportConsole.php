<?php namespace Shivergard\SmartParse\Console;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;


use \File;
use SoapBox\Formatter\Formatter;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

use \Schema;
use \DB;

class CsvImportConsole extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'smart-parse:csv_import';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Import CSV files.';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
		$this->info('¯\(°_o)/¯');

		$path = storage_path().'/csv/';		

		if(File::exists($path)) {

			$files = File::allFiles($path);

			if (count($files) == 0)
				$this->info('No CSV avaliable [inDir]');

			foreach ($files as $file)
			{
				$this->info('importing '.$file);

				$tempTable = 'tmp_'.md5($file);
				$formated = Formatter::make(File::get((string)$file), Formatter::CSV);
				$formatedArray = $formated->toArray();

				if (count($formatedArray) > 0){
					$firstRow = $formatedArray[0];
					Schema::create($tempTable, function(Blueprint $table) use ($firstRow)
					{
						$table->increments('id');

						foreach($firstRow as $field => $value){
							$table->string(md5($field));
						}
					});
				}

				foreach ($formatedArray as $key => $value) {

					$importData = array();

					foreach ($value as $rowKey => $rowVal) {
						$importData[md5($rowKey)] = (is_array($rowVal) ? json_encode($rowVal) : (string)$rowVal);
					}

					DB::table($tempTable)->insert($importData);
				}
				
				File::delete((string)$file);
			}

			$this->info('Finished');

		}else
			$this->error('No CSV avaliable');


	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return [];
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return [];
	}

}