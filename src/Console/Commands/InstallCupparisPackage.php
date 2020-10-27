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
    use CupparisPackageTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'install-cupparis-package
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


    protected function setFoorms(&$main,$package,$mainDotted) {

        $values = $this->buildPackageArrayValue('foorm.entities',$main,$package,$mainDotted,false);

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

        $values = $this->buildPackageArrayValue('policies.models',$main,$package,$mainDotted);

        $main['policies']['models'] = $values;

    }

}
