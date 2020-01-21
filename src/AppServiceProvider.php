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
            $activity->properties = $activity->properties->put('ip', request()->getClientIp());
            $activity->properties = $activity->properties->put('user_agent', request()->userAgent());
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
