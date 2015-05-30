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

		//config publish
		$this->publishes([
		    __DIR__.'/smart-parse.php' => config_path('smart-parse.php'),
		]);

		require __DIR__ .'/routes.php';
		$this->loadViewsFrom(__DIR__.'/../../views', 'smart-parse');
		$this->commands('Shivergard\SmartParse\Console\SmartParseConsole');
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
