<?php namespace Gecche\Cupparis\App;

use Illuminate\Support\ServiceProvider;
use Spatie\Activitylog\Models\Activity;

class AppServiceProvider extends ServiceProvider {


    /**
     * Booting
     */
    public function boot()
    {

        Activity::saving(function (Activity $activity) {
            $activity->properties->put('ip', request()->getClientIp());
        });
    }

	/**
	 * Register the commands
	 *
	 * @return void
	 */
	public function register()
	{

	}


}
