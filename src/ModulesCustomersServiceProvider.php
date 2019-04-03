<?php

namespace Dorcas\ModulesCustomers;
use Illuminate\Support\ServiceProvider;

class ModulesCustomersServiceProvider extends ServiceProvider {

	public function boot()
	{
		$this->loadRoutesFrom(__DIR__.'/routes/web.php');
		$this->loadViewsFrom(__DIR__.'/resources/views', 'modules-customers');
		$this->publishes([
			__DIR__.'/config/modules-customers.php' => config_path('modules-customers.php'),
		], 'config');
		/*$this->publishes([
			__DIR__.'/assets' => public_path('vendor/modules-customers')
		], 'public');*/
	}

	public function register()
	{
		//add menu config
		$this->mergeConfigFrom(
	        __DIR__.'/config/navigation-menu.php', 'navigation-menu.modules-customers.sub-menu'
	     );
	}

}


?>