<?php namespace Gecche\Cupparis\App\Facades;

use Illuminate\Support\Facades\Facade;
/**
 * @see \Illuminate\Filesystem\Filesystem
 */
class Cupparis extends Facade {

	/**
	 * Get the registered name of the component.
	 *
	 * @return string
	 */
            protected static function getFacadeAccessor() { return 'cupparis'; }

}
