<?php namespace Gecche\Cupparis\App\Console\Commands;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Config;

/**
 * Created by PhpStorm.
 * User: gecche
 * Date: 27/10/2020
 * Time: 10:19
 */

trait CupparisPackageTrait {

    /**
     * @var
     */
    protected $packagesFolder;

    /**
     * @var array
     */
    protected $packages = [];

    /**
     * @var array
     */
    protected $packagesErrors = [];


    /**
     *
     */
    protected function prepareData()
    {


        $this->packagesFolder = $this->option('dir') ?:
            (Config::get('cupparis-app.json-dir') ?:
                base_path('cupparis'));

        /*
         * Here we get the models files: if the 'model' option is set, we get only that model, otherwise we
         * get all the models in modelsFolder
         */
        $packageName = $this->argument('package');
        $this->packages = ($packageName == 'all')
            ? glob($this->packagesFolder . DIRECTORY_SEPARATOR . '*.json')
            : [$this->packagesFolder . DIRECTORY_SEPARATOR . $packageName . '.json'];


    }

    /**
     * @param $packageFilename
     * @return array|bool
     */
    protected function checkAndGuessPackageFile($packageFilename)
    {


        if (!File::exists($packageFilename)) {
            $this->packagesErrors[$packageFilename] = "File inesistente";
            return false;
        }


        $packageContents = json_decode(File::get($packageFilename),true);

        if (is_null($packageContents)) {
            $this->packagesErrors[$packageFilename] = "File json incorretto";
            return false;
        }


        return $packageContents;


    }


    protected function buildPackageArrayValue($buildKey,$main,$package,$mainDotted,$associative = true) {
        $values = $this->getJsonValue($buildKey,$main,[],$mainDotted);
        $packageValues = $this->getJsonValue($buildKey,$package,[]);

        if ($associative) {
            foreach ($packageValues as $key => $packageValue) {
                if (!array_key_exists($key,$values)) {
                    $values[$key] = $packageValue;
                }
            }
        } else {
            foreach ($packageValues as $packageValue) {
                if (!in_array($packageValue,$values)) {
                    $values[] = $packageValue;
                }
            }
        }

        return $values;
    }

    protected function installUninstall($packageContents,$uninstall = false) {
        $key = $uninstall ? 'uninstall' : 'install';
        $installingString = $uninstall ? 'uninstalling' : 'installing';

        $installClass = $this->getJsonValue($key.'.class',$packageContents,[]);
        $installMethod = $this->getJsonValue($key.'.method',$packageContents,[]);
        if (!$installClass || !$installMethod) {
            return $this->$key($packageContents);
        }

        if (!class_exists($installClass)) {
            $this->info("Class " . $installClass . " for ".$installingString." the package not found");
            return;
        }

        if (!class_exists($installMethod)) {
            $this->info("Method " . $installMethod . " for ".$installingString." the package not found");
            return;
        }

        try {
            $installClass->$installMethod();
        } catch (\Exception $e) {
            $this->info("Problems while ".$installingString." package: ");
            $this->info($e->getMessage());
            return;
        }

        $this->info($installClass.'@'.$installMethod." executed successfully!");
        return;
    }


}