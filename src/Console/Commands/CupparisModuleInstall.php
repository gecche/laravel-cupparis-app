<?php

namespace Gecche\Cupparis\App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use Nwidart\Modules\Facades\Module;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class CupparisModuleInstall extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'cupparis-module-install {type=i : install (i) or uninstall (u), default i}';

    /**
     * The console command description.
     */
    protected $description = 'Cupparis Module Install';


    protected $moduleName = null;

    protected $modelConfsPath = '/main/resources/vue-application-v4/src/application/ModelConfs';

    protected $foormsPath = '/main/config/foorms';

    protected $modelsPath = '/main/app/Models';
    protected $fileService = null;

    protected $type;

    protected $dirsToManage = [];

    protected $modulePath;

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {

        $this->type = $this->argument('type');
        $this->fileService = new Filesystem();

        if (!$this->moduleName) {
            $this->comment("Questo è un comando 'astratto', è da ereditare dentro a uno specifico modulo Cupparis :) ");
        }

        $this->modulePath = module_path($this->moduleName);

        $this->dirsToManage = $this->setDirsToManage();

        if (!$this->checkCommandType()) {
            return;
        };

        foreach ($this->dirsToManage as $dirToManage => $destDir) {

            $this->manageDir($dirToManage,$destDir);
        }

        $this->manageModelConfs();

        $this->manageConfigs();

    }

    protected function manageDir($dirToManage,$destDir) {
        $this->fileService->ensureDirectoryExists($dirToManage,$destDir);

        $this->comment("\n" . $dirToManage . ' ---> ' . $destDir . "\n");
        $fileNames = $this->fileService->files($this->modulePath . $dirToManage);

        foreach ($fileNames as $fileName) {
            $relativeFilename = Str::afterLast($fileName, DIRECTORY_SEPARATOR);
            $this->comment($fileName . ' ---> ' . $destDir . DIRECTORY_SEPARATOR . $relativeFilename);

            if ($this->type == 'u') {
                $this->fileService->delete($destDir . DIRECTORY_SEPARATOR . $relativeFilename);
            } else {
                $this->fileService->copy($fileName, $destDir . DIRECTORY_SEPARATOR . $relativeFilename);
            }
        }
    }

    protected function checkCommandType() {
        switch ($this->type) {
            case 'i':
                break;
            case 'u':
                $this->comment("Questa operazione eliminerà i file del modulo Cupparis " . $this->moduleName . ' nelle cartelle: ');
                foreach ($this->dirsToManage as $dirToManage => $destDir) {
                    $this->comment($destDir);
                }
                if (!$this->confirm("Sei sicuro di disinstallare il modulo Cupparis " . $this->moduleName . '?')) {
                    $this->comment("Ok, operazione annullata");
                    return false;
                };
                break;
            default:
                $this->comment("Tipo di operazione '$this->type' non prevista, solo 'i' o 'u'");
                return false;
        }
    }

    protected function setDirsToManage() {
        return [
            //CLASSES
            $this->modelsPath => app_path('Models'),
            '/main/app/Models/Relations' => app_path('Models/Relations'),
            '/main/app/Policies' => app_path('Policies'),
            '/main/app/Enums' => app_path('Enums'),
            //DB
            '/database/migrations' => database_path('migrations'),
            '/main/database/seeders' => database_path('seeders'),
            //FOORMS
            $this->foormsPath => config_path('foorms'),
            //VUE-APPLICATION-V4
            $this->modelConfsPath => Config::get('modules.paths.modelconfs-path'),
        ];
    }

    protected function manageConfigs()
    {


        $configsData = [
            [
                'configFile' => 'foorm',
                'filesPath' => $this->foormsPath,
                'key' => 'foorms',
                'method' => 'snakeArraySubstitution'
            ],
            [
                'configFile' => 'permission',
                'filesPath' => $this->modelsPath,
                'key' => 'cupparis.models',
                'method' => 'snakeArraySubstitution'
            ],
            [
                'configFile' => 'permission',
                'filesPath' => $this->modelsPath,
                'key' => 'policies.models',
                'method' => 'policyModelsSubstitution'
            ],
        ];

        foreach ($configsData as $configData) {

            $configFile = Arr::get($configData, 'configFile');
            $filesPath = Arr::get($configData, 'filesPath');
            $configKey = Arr::get($configData, 'key');
            $method = Arr::get($configData, 'method');
            $configValue = config($configFile, []);

            $configFileString = "<?php\n\nreturn ";

            $values = Arr::get($configValue, $configKey, []);

            $fileNames = $this->fileService->files($this->modulePath . $filesPath);

            foreach ($fileNames as $fileName) {
                $values = $this->$method($values,$fileName);
            }

            $configValue[$configKey] = $values;

            $configFileString .= varexport($configValue) . ';';


            $this->fileService->put(config_path($configFile . '.php'), $configFileString);

        }

    }

    protected function snakeArraySubstitution($values,$fileName) {
        $relativeFilename = Str::afterLast($fileName, DIRECTORY_SEPARATOR);
        $objectName = Str::snake(Str::beforeLast($relativeFilename, '.'));

        if ($this->type == 'u') {
            $values = Arr::reject($values, function ($item) use ($objectName) {
                return $item == $objectName;
            });
        } else {
            $values[] = $objectName;
        }

        return $values;
    }

    protected function policyModelsSubstitution($values,$fileName) {
        $relativeFilename = Str::afterLast($fileName, DIRECTORY_SEPARATOR);

        $modelObjectName = '\\App\\Models\\' . Str::beforeLast($relativeFilename, '.');
        $policyObjectName = '\\App\\Policies\\' . Str::beforeLast($relativeFilename, '.') . 'Policy';

        if ($this->type == 'u') {
            unset($values[$modelObjectName]);
        } else {
            $values[$modelObjectName] = $policyObjectName;
        }

        return $values;
    }

    protected function manageModelConfs()
    {

        $modelConfsDirs = [
            $this->modelConfsPath => Config::get('modules.paths.modelconfs-path'),
        ];

        $modelConfsIndex = Config::get('modules.paths.modelconfs-path') . DIRECTORY_SEPARATOR . 'index.js';
        $indexJsFile = $this->fileService->get($modelConfsIndex);

        foreach ($modelConfsDirs as $dirToManage => $destDir) {

            $this->fileService->ensureDirectoryExists($destDir);

            $fileNames = $this->fileService->files($this->modulePath . $dirToManage);

            $totalImportString = "";
            $totalInstallString = "";
            $this->comment("AGGIORNO MODELCONFS index.js");
            foreach ($fileNames as $fileName) {

                /*


                 */

                $relativeFilename = Str::afterLast($fileName, DIRECTORY_SEPARATOR);
                $this->comment("File: $relativeFilename");
                //AGGIORNO MODEL CONFS INDEX.JS


                $jsModelName = Str::beforeLast($relativeFilename, '.');

                $importString = "\nimport " . $jsModelName . " from './" . $jsModelName . ".js';";
                $installString = "\n\t\tcs.CrudVars.modelConfs." . $jsModelName . " = " . $jsModelName . ";";
                if ($this->type == 'u') {
                    $indexJsFile = Str::replace([$importString, $installString], ["", ""], $indexJsFile);
                } else {
                    $totalImportString .= $importString;
                    $totalInstallString .= $installString;
                }

            }
            if ($this->type == 'i') {
                $indexJsFile = Str::replace("//IMPORT START", "//IMPORT START" . $totalImportString, $indexJsFile);
                $indexJsFile = Str::replace("//INSTALL START", "//INSTALL START" . $totalInstallString, $indexJsFile);
            }
            $this->fileService->put($modelConfsIndex, $indexJsFile);

        }
    }


    /**
     * Get the console command arguments.
     */
    protected function getArguments(): array
    {
        return [
            ['example', InputArgument::REQUIRED, 'An example argument.'],
        ];
    }

    /**
     * Get the console command options.
     */
    protected function getOptions(): array
    {
        return [
            ['example', null, InputOption::VALUE_OPTIONAL, 'An example option.', null],
        ];
    }
}
