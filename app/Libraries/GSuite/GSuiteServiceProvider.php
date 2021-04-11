<?php

namespace App\Libraries\GSuite;

use Illuminate\Support\Facades\App;
use Illuminate\Support\ServiceProvider;

class GSuiteServiceProvider extends ServiceProvider
{

	public function boot()
	{
		$this->publishes( [ __DIR__ . '/config/gsuite.php' => App::make( 'path.config' ) . '/gsuite.php', ] );
	}

	public function register()
	{
		$this->mergeConfigFrom( __DIR__ . '/config/gsuite.php', 'gsuite' );

		// Main Service
		$this->app->bind( 'GSuite', function ( $app )
		{
			return new GSuiteClass( $app[ 'config' ] );
		} );
	}
}