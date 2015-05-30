<?php namespace Shivergard\SmartParse\Console;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use \Schema;
use \Config;
use \DB;

class SmartParseConsole extends Command {

	use \Shivergard\SmartParse\CommonMeth;

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'smart-parse:job_process';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'SmartParse Job processing.';

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
		if (DB::table('spt_jobs')->select('id')->count() > 0){
			$job = DB::table('spt_jobs')->first();

			$jobDescription = json_decode($job->details , true);


			//cleanup for job
			if (!isset($jobDescription['started'])){

				$validJob = false;

				$jobDescription['started'] = 1;
				DB::table('spt_jobs')
		            ->where('id', $job->id)
		            ->update(array('details' => json_encode($jobDescription)));

		        if (!in_array('spt_jobs' , $this->getAllColumnsNames($jobDescription['from']))){
        			Schema::table($jobDescription['from'], function($table){
						$table->string('spt_jobs')->nullable();
					});
		        }else{
		        	DB::table($jobDescription['from'])->update(array('spt_jobs' => ''));
		        }

		        //check is any job field is filled
		        $targetFields = $this->getAllColumnsNames($jobDescription['target_tables']);

		        foreach ($targetFields as $field) {
		        	if (trim($jobDescription[$field]) != ''){
		        		$validJob = true;
		        		break;
		        	}		
		        }

		        if (!$validJob){
		        	DB::table('spt_jobs')->where('id', $job->id)->delete();
		        	$this->error('Invalid job');
		        }
		        	
			}

			//start processing
			$this->info('start processing');

			//detect valid fields
			if (!isset($jobDescription['target_fields'])){
				$this->info('Define valid fields for first time');
				$targetFields = $this->getAllColumnsNames($jobDescription['target_tables']);
		        foreach ($targetFields as $field) {
		        	if (trim($jobDescription[$field]) == ''){
		        		unset($jobDescription[$field]);
		        		unset($targetFields[$field]);
		        	}		
		        }

		        $jobDescription['target_fields'] = $targetFields;
		        DB::table('spt_jobs')
			            ->where('id', $job->id)
			            ->update(array('details' => json_encode($jobDescription)));
			}

			unset($jobDescription['target_fields']['id']);
			//start loading data
			switch (Config::get('database.default')) {
				case 'sqlite':
					$orderBy = 'Random()';
					break;
				
				default:
					$orderBy = 'RAND()';
					break;
			}
			foreach (
				DB::table($jobDescription['from'])->where('spt_jobs' , 'NOT LIKE' , $job->table)->orderByRaw($orderBy)->paginate(20) 
				as $row
			) {
				$fillData = array();
				foreach ($jobDescription['target_fields'] as $field) {
					if (isset($jobDescription[$field]) && isset($row->$jobDescription[$field]))
					$fillData[$field] = $row->$jobDescription[$field];
				}
				DB::table($jobDescription['target_tables'])->insert($fillData);
				DB::table($jobDescription['from'])
			            ->where('id', $row->id)
			            ->update(array('spt_jobs' => $job->table));
			}

			$this->info('batch processed');

		}else{
			$this->info('¯\(°_o)/¯ No Jobs');
		}

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