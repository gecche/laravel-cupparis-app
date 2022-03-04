<?php namespace Gecche\Cupparis\App\Providers;

use Gecche\Cupparis\App\CupparisAppManager;
use Gecche\Cupparis\App\Foorm\FoormManager;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;
use Spatie\Activitylog\Models\Activity;
use Symfony\Component\HttpFoundation\File\MimeType\MimeTypeGuesser;

class AppServiceProvider extends ServiceProvider
{


    /**
     * Booting
     */
    public function boot()
    {
        $rootDir = __DIR__ . '/../../';

        $crudVersion = env('CRUD_VERSION', '1.0');

        //Publishing configs
        $this->publishes([
            $rootDir . 'config/cupparis-app.php' => config_path('cupparis-app.php'),
        ], 'public');

        if (!is_dir(base_path('cupparis'))) {
            mkdir(base_path('cupparis'));
        }
        $this->publishes([
            $rootDir . 'cupparis-app.json' => base_path('cupparis-app.json'),
        ], 'public');

        if (!is_dir(config_path('foorms'))) {
            mkdir(config_path('foorms'));
        }
        $this->publishes([
            $rootDir . 'config-packages/auth.php' => config_path('auth.php'),
            $rootDir . 'config-packages/breeze.php' => config_path('breeze.php'),
            $rootDir . 'config-packages/filesystems.php' => config_path('filesystems.php'),
            $rootDir . 'config-packages/foorm.php' => config_path('foorm.php'),
            $rootDir . 'config-packages/image.php' => config_path('image.php'),
            $rootDir . 'config-packages/imagecache.php' => config_path('imagecache.php'),
            $rootDir . 'config-packages/permission.php' => config_path('permission.php'),
            $rootDir . 'config-packages/themes.php' => config_path('themes.php'),
            $rootDir . 'config-packages/foorms/user.php' => config_path('foorms/user.php'),
        ], 'public');


        //Publishing and overwriting app folders
        $this->publishes([
            $rootDir . 'bootstrap/app.php' => base_path('bootstrap/app.php'),
            $rootDir . 'app/Console/Kernel.php' => app_path('Console/Kernel.php'),
            $rootDir . 'app/Console/Commands' => app_path('Console/Commands'),
            $rootDir . 'app/Foorm' => app_path('Foorm'),
            $rootDir . 'app/Models' => app_path('Models'),
            $rootDir . 'app/Policies' => app_path('Policies'),
            $rootDir . 'app/Services' => app_path('Services'),
            $rootDir . 'app/Providers/AppServiceProvider.php' => app_path('Providers/AppServiceProvider.php'),
            $rootDir . 'app/Providers/AuthServiceProvider.php' => app_path('Providers/AuthServiceProvider.php'),
            $rootDir . 'app/Providers/EventServiceProvider.php' => app_path('Providers/EventServiceProvider.php'),
            $rootDir . 'app/Http/Kernel.php' => app_path('Http/Kernel.php'),
            $rootDir . 'app/Http/Controllers/Controller.php' => app_path('Http/Controllers/Controller.php'),
            $rootDir . 'app/Http/Controllers/DownloadController.php' => app_path('Http/Controllers/DownloadController.php'),
            $rootDir . 'app/Http/Controllers/FoormActionController.php' => app_path('Http/Controllers/FoormActionController.php'),
            $rootDir . 'app/Http/Controllers/FoormController.php' => app_path('Http/Controllers/FoormController.php'),
            $rootDir . 'app/Http/Controllers/HomeController.php' => app_path('Http/Controllers/HomeController.php'),
            $rootDir . 'app/Http/Controllers/MiscController.php' => app_path('Http/Controllers/MiscController.php'),
            $rootDir . 'app/Http/Controllers/ModelSkeletonController.php' => app_path('Http/Controllers/ModelSkeletonController.php'),
            $rootDir . 'app/Http/Controllers/TestController.php' => app_path('Http/Controllers/TestController.php'),
            $rootDir . 'app/Http/Kernel.php' => app_path('Http/Kernel.php'),
        ], 'public');

        //Publishing and overwriting databases folders
        $this->publishes([
            $rootDir . 'database/factories' => database_path('factories'),
            $rootDir . 'database/migrations' => database_path('migrations'),
            $rootDir . 'database/seeders' => database_path('seeders'),
        ], 'public');

        //Publishing and overwriting resources folders
        $this->publishes([
            $rootDir . 'resources/documenti' => base_path('resources/documenti'),
            $rootDir . 'resources/lang' => base_path('resources/lang'),
        ], 'public');

        //Publishing and overwriting public folders
        $this->publishes([
            $rootDir . 'public/bootstrap4' => public_path('bootstrap4'),
            $rootDir . 'public/images' => public_path('images'),
            $rootDir . 'public/bootstrap4-app' => public_path('bootstrap4-app'),
            $rootDir . 'public/smarty3-app' => public_path('smarty3-app'),
            $rootDir . 'public/admin' => public_path('admin'),
            //$rootDir. 'public/js/edit_area' => public_path('js/edit_area'),
            //$rootDir. 'public/crud-vue/components' => public_path('crud-vue/components'),
            //$rootDir. 'public/crud-vue/ModelConfs' => public_path('crud-vue/ModelConfs'),
            //$rootDir. 'public/crud-vue/plugins' => public_path('crud-vue/plugins'),
        ], 'public');


        if (env('CRUD_ENV', '') != 'develop') {
            $this->publishes([
                $rootDir . "public/crud-$crudVersion/bootstrap4" => public_path('bootstrap4'),
                $rootDir . "public/crud-$crudVersion/smarty3" => public_path('smarty3'),
                $rootDir . "resources/views/crud-$crudVersion/bootstrap4" => resource_path('views/bootstrap4'),
                $rootDir . "resources/views/crud-$crudVersion/smarty3" => resource_path('views/smarty3'),
                $rootDir . 'resources/views/bootstrap4-app' => resource_path('views/bootstrap4-app'),
                $rootDir . 'resources/views/smarty3-app' => resource_path('views/smarty3-app'),
                $rootDir . 'resources/views/admin' => resource_path('views/admin'),
            ], 'templates');
        }


        $this->loadRoutesFrom($rootDir . 'src/routes/web.php');

        $this->bootBlade();

        $this->bootActivityLog();

        $this->bootValidationRules();

        $this->bootFilesystemMacros();

    }

    /**
     * Register the commands
     *
     * @return void
     */
    public function register()
    {

        $this->app->singleton('cupparis', function ($app) {
            return new CupparisAppManager($app['config']->get('cupparis-app'));
        });
        $this->app->extend('foorm', function ($service,$app) {
            return new FoormManager($app['config']->get('foorm'));
        });

    }


    protected function bootBlade()
    {
        Blade::directive('datetime', function ($expression) {
            return "<?php echo with{$expression}->format('m/d/Y H:i'); ?>";
        });

        Blade::extend(function ($value) {
            return preg_replace('/\@define(.+)/', '<?php ${1}; ?>', $value);
        });
    }

    protected function bootActivityLog()
    {
        Activity::saving(function (Activity $activity) {
            $activity->properties = $activity->properties->put('ip', request()->getClientIp());
            $activity->properties = $activity->properties->put('user_agent', request()->userAgent());
        });
    }

    protected function bootValidationRules()
    {

        Validator::extend('captcha', 'Gecche\Cupparis\App\Validation\Rules@captcha');
        Validator::extend('exists_or', 'Gecche\Cupparis\App\Validation\Rules@existsOr');
        Validator::extend('partita_iva', 'Gecche\Cupparis\App\Validation\Rules@partitaIva');
        Validator::extend('codice_fiscale', 'Gecche\Cupparis\App\Validation\Rules@codiceFiscale');
        Validator::extend('codice_fiscale_professional', 'Gecche\Cupparis\App\Validation\Rules@codiceFiscaleProfessional');
    }

    protected function bootFilesystemMacros()
    {
        Filesystem::macro('deleteFiles', function ($pattern, $flags = 0) {
            File::delete(File::glob($pattern, $flags));
        });

        Filesystem::macro('mimeFromGuesser', function ($path) {
            $guesser = MimeTypeGuesser::getInstance();

            try {
                $mimetype = $guesser->guess($path);
            } catch (\Exception $e) {
                return false;
            }

            return $mimetype;
        });

        Filesystem::macro('getIconaMime', function ($path, $iconeMimesArray = [], $default = 'default.png') {

            $mimetype = static::mimeFromGuesser($path);
            if ($mimetype === false) {
                return $default;
            }
            if (is_array($mimetype)) {
                $mimetype = current($mimetype);
            }

            return Arr::get($iconeMimesArray, $mimetype, $default);

        });
    }
}
