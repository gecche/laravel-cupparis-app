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
        $this->packages = $packageName ? [$this->packagesFolder . DIRECTORY_SEPARATOR . $packageName . '.json']
            : glob($this->packagesFolder . DIRECTORY_SEPARATOR . '*.json');


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


}