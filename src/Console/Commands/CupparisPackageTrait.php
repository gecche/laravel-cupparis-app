<?php namespace Gecche\Cupparis\App\Console\Commands;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;

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
    protected $package = [];

    /**
     * @var array
     */
    protected $packageErrors = [];


    /**
     *
     */
    protected function getPackageJson($packageName)
    {


        $modulesDir = Config::get('cupparis-app.modules-dir') ?: base_path('Modules');
        $jsonDir = $this->option('dir') ?:
            (Config::get('cupparis-app.json-dir') ?:
                'cupparis');

        return [$packageName => $modulesDir . DIRECTORY_SEPARATOR .
            Str::studly($packageName) . DIRECTORY_SEPARATOR .
            $jsonDir . DIRECTORY_SEPARATOR . Str::studly($packageName) . '.json'];


    }

    /**
     * @param $packageFilename
     * @return array|bool
     */
    protected function checkAndGuessPackageFile($packageFilename)
    {


        if (!File::exists($packageFilename)) {
            $this->packageErrors[$packageFilename] = "File inesistente";
            return false;
        }


        $packageContents = json_decode(File::get($packageFilename),true);

        if (is_null($packageContents)) {
            $this->packageErrors[$packageFilename] = "File json incorretto";
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

    protected function removePackageArrayValue($buildKey,$main,$package,$mainDotted,$associative = true) {
        $values = $this->getJsonValue($buildKey,$main,[],$mainDotted);
        $packageValues = $this->getJsonValue($buildKey,$package,[]);

        if ($associative) {
            $values = array_diff_key($values,$packageValues);
        } else {
            $values = array_diff($values,$packageValues);
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
