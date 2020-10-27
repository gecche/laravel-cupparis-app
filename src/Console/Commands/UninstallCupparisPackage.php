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

class UninstallCupparisPackage extends Command
{

    use CupparisJsonTrait;
    use CupparisPackageTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'uninstall-cupparis-package
                            {package : Package to uninstall}
                            {--force : it forces initialization without prompting (default: no)}
                            {--dir= : Directory of the models}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Disinstallazione package cupparis app';



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

            $this->removeFoorms($currentJson,$packageContents,$currentJsonDotted);
            $this->removeModelconfs($currentJson,$packageContents,$currentJsonDotted);
            $this->removePermissions($currentJson,$packageContents,$currentJsonDotted);
            $this->removePolicies($currentJson,$packageContents,$currentJsonDotted);

        }

        File::put($mainJsonFile,cupparis_json_encode($currentJson));

        $this->info('Cupparis app json updated successfully.');
        foreach ($this->packagesErrors as $packageFileName => $packageError) {
            $this->info($packageFileName.' ::: '.$packageError);
        }

    }




    protected function removeFoorms(&$main,$package,$mainDotted) {

        $values = $this->buildPackageArrayValue('foorm.entities',$main,$package,$mainDotted,false);

        $main['foorm']['entities'] = $values;

    }

    protected function removeModelconfs(&$main,$package,$mainDotted) {

        $values = $this->buildPackageArrayValue('modelconfs.files',$main,$package,$mainDotted,false);

        $main['modelconfs']['files'] = $values;

    }

    protected function removePermissions(&$main,$package,$mainDotted) {

        $values = $this->buildPackageArrayValue('permissions.models',$main,$package,$mainDotted,false);

        $main['permissions']['models'] = $values;

    }

    protected function removePolicies(&$main,$package,$mainDotted) {

        $values = $this->buildPackageArrayValue('policies.models',$main,$package,$mainDotted);

        $main['policies']['models'] = $values;

    }


}
