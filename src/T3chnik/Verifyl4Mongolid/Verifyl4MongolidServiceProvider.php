<?php 
namespace T3chnik\Verifyl4Mongolid;

use Illuminate\Support\ServiceProvider;
use Illuminate\Hashing\BcryptHasher;
use Illuminate\Auth\Guard;

class Verifyl4MongolidServiceProvider extends ServiceProvider {

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
            $this->package('t3chnik/verify-l4-mongolid');
            
            \Auth::extend('verify', function()
            {
                return new Guard(
                    new VerifyUserProvider(
                        new BcryptHasher, \Config::get('auth.model')
                    ),
                    \App::make('session')
                );
            }); 
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
		return array();
	}

}