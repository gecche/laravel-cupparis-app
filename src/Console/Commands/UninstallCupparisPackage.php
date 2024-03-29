<?php namespace Gecche\Cupparis\App\Console\Commands;

use Gecche\Cupparis\App\CupparisJsonTrait;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\InputStream;
use Symfony\Component\Process\Process;
use Illuminate\Support\Str;
use Symfony\Polyfill\Intl\Idn\Resources\unidata\DisallowedRanges;

class UninstallCupparisPackage extends Command
{

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


        $currentJson = json_decode(File::get($mainJsonFile), true);

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


            /*
             * We try to guess if the current model file is indeed a Breeze model file
             */
            if (($packageContents = $this->checkAndGuessPackageFile($packageFilename)) === false) {
                continue;
            }

            $this->updateJson($currentJson, $packageContents, $currentJsonDotted);
            $this->updateMix(false);

            $this->installUninstall($packageContents, true);


        }

        File::put($mainJsonFile, $this->jsonEncode($currentJson));

        $this->info('Cupparis app json updated successfully.');
        foreach ($this->packageErrors as $packageFileName => $packageError) {
            $this->info($packageFileName . ' ::: ' . $packageError);
        }

    }


    protected function updateJson(&$currentJson, $packageContents, $currentJsonDotted)
    {
        $this->removeFoorms($currentJson, $packageContents, $currentJsonDotted);
        $this->removeModelconfs($currentJson, $packageContents, $currentJsonDotted);
        $this->removePermissions($currentJson, $packageContents, $currentJsonDotted);
        $this->removePolicies($currentJson, $packageContents, $currentJsonDotted);
        $this->removeComponents($currentJson, $packageContents, $currentJsonDotted);
    }


    protected function removeFoorms(&$main, $package, $mainDotted)
    {

        $values = $this->removePackageArrayValue('foorm.entities', $main, $package, $mainDotted, false);

        $main['foorm']['entities'] = $values;

    }

    protected function removeModelconfs(&$main, $package, $mainDotted)
    {

        $values = $this->removePackageArrayValue('modelconfs.files', $main, $package, $mainDotted, false);

        $main['modelconfs']['files'] = $values;

    }

    protected function removePermissions(&$main, $package, $mainDotted)
    {

        $values = $this->removePackageArrayValue('permissions.models', $main, $package, $mainDotted, false);

        $main['permissions']['models'] = $values;

    }

    protected function removePolicies(&$main, $package, $mainDotted)
    {

        $values = $this->removePackageArrayValue('policies.models', $main, $package, $mainDotted);

        $main['policies']['models'] = $values;

    }

    protected function removeComponents(&$main, $package, $mainDotted)
    {

        $values = $this->removePackageArrayValue('components', $main, $package, $mainDotted, false);

        $main['components'] = $values;

    }

    protected function uninstall($packageContents)
    {

        $foormsDir = config_path('foorms' . DIRECTORY_SEPARATOR);
        $foormsLangDir = resource_path('lang' . DIRECTORY_SEPARATOR . 'it' . DIRECTORY_SEPARATOR . 'foorms' . DIRECTORY_SEPARATOR);
        $foormsAppDir = app_path('Foorm' . DIRECTORY_SEPARATOR);
        $foorms = $this->getJsonValue('foorm.entities', $packageContents, []);

        foreach ($foorms as $foorm) {
            $foormFile = $foormsDir . $foorm . '.php';
            $this->info($foormFile);
            File::delete($foormFile);
            $foormLangFile = $foormsLangDir . $foorm . '.php';
            $this->info($foormLangFile);
            File::delete($foormLangFile);

            $foormAppName = Str::studly($foorm);
            if (File::isDirectory($foormsAppDir . $foormAppName)) {
                File::deleteDirectory($foormsAppDir . $foormAppName);
            }


        }

        $assets = $this->getJsonValue('public', $packageContents, []);
        foreach ($assets as $asset) {
            $assetAbs  = public_path($asset);
            if (File::isDirectory($assetAbs)) {
                File::deleteDirectory($assetAbs);
            } else {
                File::delete($assetAbs);
            }
        }

//        $pagesDir = public_path('admin'.DIRECTORY_SEPARATOR.'pages'.DIRECTORY_SEPARATOR);
//        $pages = $this->getJsonValue('pages',$packageContents,[]);
//        foreach ($pages as $page) {
//            File::delete($pagesDir . $page);
//        }

//        $componentsDir = public_path('admin'.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR);
//        $components = $this->getJsonValue('components',$packageContents,[]);
//        foreach ($components as $component) {
//            File::delete($componentsDir .DIRECTORY_SEPARATOR.'js'.DIRECTORY_SEPARATOR. $component.'.js');
//            File::delete($componentsDir .DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR. $component.'.html');
//        }

        $modelsDir = app_path('Models' . DIRECTORY_SEPARATOR);
        $relationModelsDir = app_path('Models' . DIRECTORY_SEPARATOR . 'Relations' . DIRECTORY_SEPARATOR);
        $policiesDir = app_path('Policies' . DIRECTORY_SEPARATOR);
        $models = $this->getJsonValue('models', $packageContents, []);
        foreach ($models as $model) {
            File::delete($modelsDir . $model . '.php');

            $relationFile = $relationModelsDir . $model . 'Relations.php';
            if (File::exists($relationFile)) {
                File::delete($relationFile);
            }

            $policyFile = $policiesDir . $model . 'Policy.php';
            if (File::exists($policyFile)) {
                File::delete($policyFile);
            }
        }

        $modelsDir = app_path('DatafileModels' . DIRECTORY_SEPARATOR);
        $relationModelsDir = app_path('DatafileModels' . DIRECTORY_SEPARATOR . 'Relations' . DIRECTORY_SEPARATOR);
        $policiesDir = app_path('Policies' . DIRECTORY_SEPARATOR);
        $models = $this->getJsonValue('datafile-models', $packageContents, []);
        foreach ($models as $model) {
            File::delete($modelsDir . $model . '.php');

            $relationFile = $relationModelsDir . $model . 'Relations.php';
            if (File::exists($relationFile)) {
                File::delete($relationFile);
            }

            $policyFile = $policiesDir . $model . 'Policy.php';
            if (File::exists($policyFile)) {
                File::delete($policyFile);
            }
        }

        $datafileProvidersDir = app_path('DatafileProviders' . DIRECTORY_SEPARATOR);
        $datafileProviders = $this->getJsonValue('datafile-providers', $packageContents, []);
        foreach ($datafileProviders as $datafileProvider) {
            File::delete($datafileProvidersDir . $datafileProvider . '.php');
        }

        $seedsDir = database_path('seeds' . DIRECTORY_SEPARATOR);
        $seeds = $this->getJsonValue('database.seeds', $packageContents, []);
        foreach ($seeds as $seed) {
            File::delete($seedsDir . $seed . '.php');
        }
        $factoriesDir = database_path('factories' . DIRECTORY_SEPARATOR);
        $factories = $this->getJsonValue('database.factories', $packageContents, []);
        foreach ($factories as $factory) {
            File::delete($factoriesDir . $factory . '.php');
        }
        $dumpsDir = database_path('dump' . DIRECTORY_SEPARATOR);
        $dumps = $this->getJsonValue('database.dump', $packageContents, []);
        foreach ($dumps as $dump) {
            Log::info("DUMP::: " . $dumpsDir . $dump . '.sql');
            File::delete($dumpsDir . $dump . '.sql');
        }


        $configs = $this->getJsonValue('config', $packageContents, []);
        foreach ($configs as $config) {
            Log::info("CONFIG::: " . config_path($config . '.php'));
            File::delete(config_path($config . '.php'));
        }
        $this->info("Package uninstalled successfully");
        return;
    }

}
