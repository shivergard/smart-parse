<?php namespace Shivergard\SmartParse;

use Illuminate\Support\ServiceProvider;

class SmartParseServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot()
	{

		require __DIR__ .'/routes.php';
		View::addNamespace('smart-parse', __DIR__.'/../../views')
		$this->commands('Shivergard\SmartParse\Console\SmartParseConsole');
		$this->commands('Shivergard\SmartParse\Console\InitConsole');
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		//
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return [];
	}

}
