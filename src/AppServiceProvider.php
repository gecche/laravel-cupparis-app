<?php namespace Gecche\Cupparis\App;

use Gecche\Cupparis\App\Foorm\FoormManager;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;
use Spatie\Activitylog\Models\Activity;

class AppServiceProvider extends ServiceProvider {


    /**
     * Booting
     */
    public function boot()
    {

        $this->publishes([
            __DIR__ . '/../config/cupparis-app.php' => config_path('cupparis-app.php'),
        ], 'public');

        if (!is_dir(config_path('foorms'))) {
            mkdir(config_path('foorms'));
        }

        $this->publishes([
            __DIR__ . '/../config-packages/auth.php' => config_path('auth.php'),
            __DIR__ . '/../config-packages/breeze.php' => config_path('breeze.php'),
            __DIR__ . '/../config-packages/filesystems.php' => config_path('filesystems.php'),
            __DIR__ . '/../config-packages/foorm.php' => config_path('foorm.php'),
            __DIR__ . '/../config-packages/image.php' => config_path('image.php'),
            __DIR__ . '/../config-packages/imagecache.php' => config_path('imagecache.php'),
            __DIR__ . '/../config-packages/permission.php' => config_path('permission.php'),
            __DIR__ . '/../config-packages/themes.php' => config_path('themes.php'),
            __DIR__ . '/../config-packages/foorms/user.php' => config_path('foorms/user.php'),
        ], 'public');

        $this->loadRoutesFrom(__DIR__.'/routes/web.php');

        $this->bootBlade();

        $this->bootActivityLog();

        $this->bootValidationRules();
    }

	/**
	 * Register the commands
	 *
	 * @return void
	 */
	public function register()
	{

        $this->app->bind('foorm', function($app)
        {
            return new FoormManager($app['config']->get('foorm'));
        });

	}


	protected function bootBlade() {
        Blade::directive('datetime', function($expression) {
            return "<?php echo with{$expression}->format('m/d/Y H:i'); ?>";
        });

        Blade::extend(function($value) {
            return preg_replace('/\@define(.+)/', '<?php ${1}; ?>', $value);
        });
    }

    protected function bootActivityLog() {
        Activity::saving(function (Activity $activity) {
            $activity->properties = $activity->properties->put('ip', request()->getClientIp());
            $activity->properties = $activity->properties->put('user_agent', request()->userAgent());
        });
    }

    protected function bootValidationRules() {

        Validator::extend('captcha', 'Gecche\Cupparis\App\Validation\Rules@captcha');
        Validator::extend('exists_or', 'Gecche\Cupparis\App\Validation\Rules@existsOr');
        Validator::extend('partita_iva', 'Gecche\Cupparis\App\Validation\Rules@partitaIva');
        Validator::extend('codice_fiscale', 'Gecche\Cupparis\App\Validation\Rules@codiceFiscale');
        Validator::extend('codice_fiscale_professional', 'Gecche\Cupparis\App\Validation\Rules@codiceFiscaleProfessional');
    }
}
