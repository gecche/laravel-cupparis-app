<?php namespace Cupparis\App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;

use Illuminate\Support\Facades\File;

class UserEventSubscriber {

    /**
     * Create the event handler.
     *
     * @return void
     */
    public function __construct()
    {

    }

	/**
	 * Handle the user logged in event.
	 *
	 * @param  UserLoggedIn  $event
	 * @return void
	 */
	public function onUserLoggedIn(Login $event)
	{
//	    $user = $event->user;

	    // $remember = $event->remember;
        //$user->logins = $user->logins+1;
        //$user->last_login = new Carbon();
        //$user->forceSave();

        activity()->log('Login');
        File::makeDirectory(storage_temp_path(),0755,false,true);
	}

    /**
     * Handle the user logged in event.
     *
     * @param  UserLoggedIn  $event
     * @return void
     */
    public function onUserLoggedOut(Logout $event)
    {
        activity()->log('Logout');
        File::deleteDirectory(storage_temp_path());
    }


    /**
     * Register the listeners for the subscriber.
     *
     * @param  Illuminate\Events\Dispatcher  $events
     */
    public function subscribe($events)
    {
        $events->listen(
            'Illuminate\Auth\Events\Login',
            'Cupparis\App\Listeners\UserEventSubscriber@onUserLogin'
        );

        $events->listen(
            'Illuminate\Auth\Events\Logout',
            'Cupparis\App\Listeners\UserEventSubscriber@onUserLogout'
        );
    }

}
