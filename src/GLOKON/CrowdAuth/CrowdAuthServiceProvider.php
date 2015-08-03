<?php

/*
 * This file is part of Laravel CrowdAuth
 *
 * (c) Daniel McAssey <hello@glokon.me>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GLOKON\CrowdAuth;

use Illuminate\Support\ServiceProvider;

class CrowdAuthServiceProvider extends ServiceProvider {

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
		$this->package('glokon/crowdauth','crowdauth');
		\Auth::extend('crowdauth', function() {
			return new \Illuminate\Auth\Guard(new \GLOKON\CrowdAuth\CrowdAuthUserProvider, \App::make('session.store'));
		});
	}


	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		// Bind the crowdauth name to a singleton instance of the Crowd API Service
		$this->app->singleton("crowdauth", function() {
			return new CrowdAPI();
		});

		// When Laravel logs out, logout the Crowd token using Crowd API
		\Event::listen('auth.logout', function($user) {
			\App::make("crowdauth")->ssoInvalidateToken($user->getRememberToken());
		});
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