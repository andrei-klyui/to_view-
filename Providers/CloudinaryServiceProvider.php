<?php
namespace App\Providers;

use League\Flysystem\Filesystem;
use Storage;
use App\Filesystem\CloudinaryAdapter;
use CarlosOCarvalho\Providers\CloudinaryServiceProvider as BaseCloudinaryServiceProvider;

class CloudinaryServiceProvider extends BaseCloudinaryServiceProvider {

	/**
	 * Perform post-registration booting of services.
	 *
	 * @return void
	 */
	public function boot() {
		Storage::extend('cloudinary', function ($app, $config) {

			return new Filesystem( new CloudinaryAdapter($config) );
		});
	}

	/**
	 * Register bindings in the container.
	 *
	 * @return void
	 */
	public function register() {
		//
	}
}
