<?php namespace Gecche\Cupparis\App\Console\Commands;

use Gecche\Cupparis\App\CupparisJsonTrait;
use Gecche\Cupparis\App\Facades\Cupparis;
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

    use CupparisPackageTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'install-cupparis-package
                            {package : package to install}
                            {--force : it forces initialization without prompting (default: no)}
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


        $currentJson = json_decode(File::get($mainJsonFile),true);

        if (is_null($currentJson)) {
            throw new \Exception("Problemi nel file cupparis-app.json");
        }

        $currentJsonDotted = Arr::dot($currentJson);

        $packageJson = $this->getPackageJson($this->argument('package'));

        $this->packageErrors = [];

        /*
         * For each model encountered we compile the relations defined in the Breeze relational array
         */
        foreach ($packageJson as $packageFilename) {

            $packageName = substr($packageFilename,0,-5);

            /*
             * We try to guess if the current model file is indeed a Breeze model file
             */
            if (($packageContents = $this->checkAndGuessPackageFile($packageFilename)) === false) {
                continue;
            }

            $this->updateJson($currentJson,$packageContents,$currentJsonDotted);
            $this->updateMix();

            $this->installUninstall($packageContents);

            //$this->updateInstallScript($packageName,$packageContents);

        }

        File::put($mainJsonFile,$this->jsonEncode($currentJson));

        $this->info('Cupparis app json updated successfully.');
        foreach ($this->packageErrors as $packageFileName => $packageError) {
            $this->info($packageFileName.' ::: '.$packageError);
        }

    }


    protected function updateJson(&$currentJson,$packageContents,$currentJsonDotted) {
        $this->setFoorms($currentJson,$packageContents,$currentJsonDotted);
        $this->setModelconfs($currentJson,$packageContents,$currentJsonDotted);
        $this->setPermissions($currentJson,$packageContents,$currentJsonDotted);
        $this->setPolicies($currentJson,$packageContents,$currentJsonDotted);
        $this->setComponents($currentJson,$packageContents,$currentJsonDotted);
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
    protected function setComponents(&$main,$package,$mainDotted) {

        $values = $this->buildPackageArrayValue('components',$main,$package,$mainDotted,false);

        $main['components'] = $values;

    }


    protected function install($packageContents) {
        $this->info("Package installed successfully");
        return;
    }


//    protected function updateInstallScript($packageName,$packageContents) {
//
//        $installFile = base_path('cupparis/install.sh');
//        if (!File::exists($installFile)) {
//            File::put($installFile,"#Cupparis Packages installation file\n#####\n\n");
//        }
//        $installCurrentContents = File::get($installFile);
//
//        $installPackageContents = $this->getJsonValue('install',$packageContents,[],Arr::dot($packageContents));
//
//        $data = "#".$packageName."-start\n";
//
//        $data .= implode("\n",$installPackageContents) . "\n";
//        $data .= "#".$packageName."-end\n";
//
//        File::append($installFile,$data);
//        return;
//    }
}
