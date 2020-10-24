<?php namespace Gecche\Cupparis\App\Console\Commands;

use Gecche\Cupparis\App\CupparisJsonTrait;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\InputStream;
use Symfony\Component\Process\Process;
use Illuminate\Support\Str;

class InstallCupparisPackage extends Command
{

    use CupparisJsonTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'init
                            {--force : it forces initialization without prompting (default: no)}
                            {package? : Only compile relations for the specified model and not for all the models in the folder}
                            {--dir= : Directory of the models}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Installazione package cupparis app';


    /**
     * @var
     */
    protected $packagesFolder;

    /**
     * @var
     */
    protected $modelsNamespace;
    /**
     * @var array
     */
    protected $packages = [];
    /**
     * @var array
     */
    protected $packagesErrors = [];


    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {

        $mainJsonFile = base_path('cupparis-app.json');
        /*
         * We set the folder, the namespace of the models and the models for compiling relations
         */
        $this->prepareData();


        $currentJson = json_decode(File::get($mainJsonFile),true);

        if (is_null($currentJson)) {
            throw new \Exception("Problemi nel file cupparis-app.json");
        }

        $currentJsonDotted = Arr::dot($currentJson);

        $this->packagesErrors = [];

        /*
         * For each model encountered we compile the relations defined in the Breeze relational array
         */
        foreach ($this->packages as $packageFilename) {


            /*
             * We try to guess if the current model file is indeed a Breeze model file
             */
            if (($packageContents = $this->checkAndGuessPackageFile($packageFilename)) === false) {
                continue;
            }

            $this->setFoorms($currentJson,$packageContents,$currentJsonDotted);
            $this->setModelconfs($currentJson,$packageContents,$currentJsonDotted);
            $this->setPermissions($currentJson,$packageContents,$currentJsonDotted);
            $this->setPolicies($currentJson,$packageContents,$currentJsonDotted);

        }

        File::put($mainJsonFile,cupparis_json_encode($currentJson));

        $this->info('Cupparis app json updated successfully.');
        foreach ($this->packagesErrors as $packageFileName => $packageError) {
            $this->info($packageFileName.' ::: '.$packageError);
        }

    }

    /**
     *
     */
    protected function prepareData()
    {


        $this->packagesFolder = $this->option('dir') ?:
            (Config::get('cupparis-app.json-dir') ?:
                base_path('cupparis'));

        $this->modelsNamespace = Config::get('breeze.namespace') ?: $this->getAppNamespace();


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


    protected function setFoorms(&$main,$package,$mainDotted) {

        $values = $this->buildPackageArrayValue('foorm.entities',$main,$package,$mainDotted);

        $main['foorm']['entities'] = $values;

    }

    protected function setModelconfs(&$main,$package,$mainDotted) {

        $values = $this->buildPackageArrayValue('modelconfs.files',$main,$package,$mainDotted,false);

        $main['modelconfs']['files'] = $values;

    }

    protected function setPermissions(&$main,$package,$mainDotted) {

        $values = $this->buildPackageArrayValue('permissions.models',$main,$package,$mainDotted,false);

        $main['permissions']['models'] = $values;

    }

    protected function setPolicies(&$main,$package,$mainDotted) {

        $values = $this->buildPackageArrayValue('policies.models',$main,$package,$mainDotted= $value);

        $main['policies']['models'] = $values;

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
