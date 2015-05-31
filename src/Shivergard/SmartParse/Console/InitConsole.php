<?php namespace Shivergard\SmartParse\Console;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use \Schema;
use \Config;
use \DB;

class InitConsole extends Command {

	use \Shivergard\SmartParse\CommonMeth;

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'smart-parse:init';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'SmartParse Init [basic user create].';

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
		if (DB::table('users')->where('name' , 'smart_parse')->select('id')->count() == 0){
			\App\User::create(
				array(
				'email'    => 'root@smartparse.dev',
	            "password" => \Hash::make("toor"),
	            "name"  => "smart_parse")
	            );
			$this->info(' Mail: root@smartparse.dev');
			$this->info(' Passowrd: toor');
		}else{
			$this->info(' User exists');
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