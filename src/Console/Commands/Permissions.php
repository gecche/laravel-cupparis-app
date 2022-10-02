<?php namespace Gecche\Cupparis\App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Console\GeneratorCommand;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Illuminate\Support\Str;

class Permissions extends Command
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'permissions';


    protected $config;

    protected $configKeysToBuild = [
        'roles_models_permissions',
        'roles_other_permissions',
//        'guest_models_permissions',
//        'guest_other_permissions',
    ];

    protected $modelsConfigPath = null; //Esempio: breeze.models o json_rest.models se non è specificato prende
                                        //i valori da permission.cupparis.models

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Genera permessi e ruoli iniziali di un applicazione';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

        Cache::forget(config('permission.cache.key','spatie.permission.cache'));

        $this->config = Config::get('permission.cupparis',[]);

        if ($this->modelsConfigPath) {
            $this->config['models'] = Config::get($this->modelsConfigPath,[]);
        }


//        print_r($this->config);

//        echo "*******\n\n";

        $this->setInitialValues();

//        print_r($this->config);

//        print_r($this->stubInitialValues);
        $permissionsService = new \App\Services\Permissions($this->config);

        $permissionsService->savePermissions();

        $this->comment('Permissions updated');


    }

    protected function setInitialValues()
    {

        foreach ($this->configKeysToBuild as $key) {
            $methodName = 'build'.Str::studly($key);
            $this->config[$key] = $this->$methodName();
        }

    }

    /*
     * PERMESSI SUI MODELLI ASSOCIATI AI RUOLI
     */
    protected function buildRolesModelsPermissions()
    {

//        $rolesModelsPermissions = [
//            'web' => [
//                  'Admin' => [
//                    'news' => null,
//                ],
//            ],
//        ];

        $rolesModelsPermissions = [];

        return $rolesModelsPermissions;

    }

    protected function buildRolesOtherPermissions()
    {

//        $rolesOtherPermissions = [
//            'web' => [
//                  'Admin' => [
//                    'permission_1',
//                ],
//            ],

        $rolesOtherPermissions = [];

        return $rolesOtherPermissions;

    }


    //IL concetto di guest sul 5.5 non so se è possibile usarlo
    //dal 5.7 sì.

    /*
     * PERMESSI SUI MODELLI ASSOCIATI ALL'UTENTE NON REGISTRATO
     */
//    protected function buildGuestModelsPermissions()
//    {
////        $guestModelsPermissions = [
////            'web' => [
////                'news' => [
////                    'view',
////                ],
////            ]
////        ];
//
//        $guestModelsPermissions = [];
//
//        return $guestModelsPermissions;
//
//    }
//
//    protected function buildGuestOtherPermissions()
//    {
//
////        $guestOtherPermissions = [
////                'web' => [
////                    'permission_1' => null,
////                ],
//
//        $guestOtherPermissions = [];
//
//        return $guestOtherPermissions;
//
//    }

    protected function getArguments()
    {
        return [];
    }

}
