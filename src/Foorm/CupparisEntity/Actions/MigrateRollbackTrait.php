<?php

namespace Gecche\Cupparis\App\Foorm\CupparisEntity\Actions;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

trait MigrateRollbackTrait
{
    protected $msConfig;

    protected $stubs;


    protected $langs;

    protected $files;

    protected $migrationPath;
    protected $modelConfsPath;

    protected $modelsNamespaces;
    protected $policiesNamespaces;


    protected function initOps()
    {
        $this->model = $this->model->find(Arr::get($this->input, 'id'));
        $this->msConfig = Config::get('cupparis-app.cupparis_entity', []);

        $this->langs = Arr::get($this->msConfig, 'langs', []);
        $this->stubs = Arr::get($this->msConfig, 'stubs', []);

        $this->migrationPath = 'database/migrations/';
        $this->modelConfsPath = Arr::get($this->msConfig,'modelConfsPath');
        $this->modelsNamespaces =  Arr::get($this->msConfig,'models_namespace');
        $this->policiesNamespaces =  Arr::get($this->msConfig,'policies_namespace');

        $this->files = new Filesystem();

    }



    protected function manageConfigs($type)
    {


        $configsData = [
            [
                'entry' => Str::snake($this->model->model_class),
                'configFile' => 'foorm',
                'keys' => [
                    'foorms' => 'snakeArraySubstitution',
                ]
            ],
            [
                'entry' => Str::snake($this->model->model_class),
                'configFile' => 'permission',
                'keys' => [
                    'cupparis.models' => 'snakeArraySubstitution',
                    'policies.models' => 'policyModelsSubstitution',
                ],
            ],
        ];

        foreach ($configsData as $configData) {

            $configFile = Arr::get($configData, 'configFile');
            $entry = Arr::get($configData, 'entry');
            $configKeys = Arr::get($configData, 'keys');
            $configValue = config($configFile, []);

            $configFileString = "<?php\n\nreturn ";

//            Log::info("CONFIG FILE::: " . $configFile);
//            Log::info($configValue);
            foreach ($configKeys as $configKey => $method) {
                $values = Arr::get($configValue, $configKey, []);

                $values = $this->$method($values, $entry, $type);

                Arr::set($configValue,$configKey,$values);
            }

            $configFileString .= varexport($configValue) . ';';



            $this->files->put(config_path($configFile . '.php'), $configFileString);

        }

    }

    protected function snakeArraySubstitution($values, $entry, $type)
    {

        if ($type == 'u') {
            $values = Arr::reject($values, function ($item) use ($entry) {
                return $item == $entry;
            });
        } else {
            $values[] = $entry;
        }

        return $values;
    }

    protected function policyModelsSubstitution($values, $entry, $type)
    {


        $modelObjectName = '\\' . $this->modelsNamespaces . Str::studly($entry);
        $policyObjectName = '\\' . $this->policiesNamespaces . Str::studly($entry) . 'Policy';

        if ($type == 'u') {
            unset($values[$modelObjectName]);
        } else {
            $values[$modelObjectName] = $policyObjectName;
        }

        return $values;
    }



    protected function getStub($type = 'migration')
    {
        return base_path($this->stubs[$type]);
        // TODO: Implement getStub() method.
    }

    protected function getStubInPath($type = 'input', $path = 'fieldsTypesPath', $ext = 'stub')
    {
        return base_path($this->stubs[$path] . $type . '.' . $ext);
        // TODO: Implement getStub() method.
    }

    protected function getIndent($n = null)
    {
        if (is_null($n)) {
            $n = 3;
        }

        $string = "";

        for ($i = 1; $i <= $n; $i++) {
            $string .= "\t";
        }

        return $string;
    }

    protected function implodeArrayJsFieldsType($fields)
    {

        $string = '';

        foreach ($fields as $field) {

            $type = Arr::get($field, 'type');
            $stub = $this->files->get($this->getStubInPath($type));

            $stub = str_replace(
                '{{$modelName}}', Str::snake($this->model->model_class), $stub
            );

            $string .= $this->getIndent(3) . "'" . Arr::get($field, 'nome') . "'";
            $string .= ' : { ' . "\n";
            $string .= $stub;
            $string .= $this->getIndent(3) . '}, ' . "\n";
        }

        $string = trim($string, " ,\n");

        return $string;
    }

    protected function implodeArrayJsOrderFields($fields)
    {

        $string = '';

        foreach ($fields as $field) {
            $string .= $this->getIndent(3) . "'" . Arr::get($field, 'nome') . "' : '" . Arr::get($field, 'nome') . "',\n";
        }

        $string = trim($string, " ,\n");

        return $string;
    }

    protected function implodeArrayJsFields($fields)
    {

        $string = '';

        foreach ($fields as $field) {
            $string .= $this->getIndent(3) . "'" . Arr::get($field, 'nome') . "',\n";
        }

        $string = trim($string, " ,\n");

        return $string;
    }


}

