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
    protected $vueApplicationModelConfsPath;
    protected $vueApplicationPath;
    protected $vueApplicationConfigPath;

    protected $modelsNamespaces;
    protected $policiesNamespaces;

    protected $snakeModel;
    protected $studlyModel;

    protected $cosaFare;

    protected function initOps()
    {
        $this->model = $this->model->find(Arr::get($this->input, 'id'));
        $this->cosaFare = Arr::wrap(Arr::get($this->input, 'cosa',[]));
        Log::info("ACTION INPUT");
        Log::info($this->input);
        $this->msConfig = Config::get('cupparis-app.cupparis_entity', []);

        $this->langs = Arr::get($this->msConfig, 'langs', []);
        $this->stubs = Arr::get($this->msConfig, 'stubs', []);

        $this->migrationPath = 'database/migrations/';
        $this->vueApplicationPath = Arr::get($this->msConfig, 'vueApplicationPath', 'resources/vue-application-v4/src/application/');
        $this->vueApplicationModelConfsPath = $this->vueApplicationPath . Arr::get($this->msConfig, 'vueApplicationModelConfsPath', 'ModelConfs/');
        $this->vueApplicationConfigPath = $this->vueApplicationPath . Arr::get($this->msConfig, 'vueApplicationConfigPath', 'config/');
        $this->modelsNamespaces = Arr::get($this->msConfig, 'models_namespace');
        $this->policiesNamespaces = Arr::get($this->msConfig, 'policies_namespace');

        $this->snakeModel = Str::snake($this->model->model_class);
        $this->studlyModel = Str::studly($this->snakeModel);

        $this->files = new Filesystem();

    }

    protected function checkDaFare($type) {
        return in_array("tutto",$this->cosaFare) || in_array($type,$this->cosaFare);
    }

    protected function getConfigsData()
    {
        return [
            [
                'configFile' => config_path('foorm.php'),
                'keys' => [
                    'foorms' => [
                        'entry' => $this->snakeModel,
                    ],
                ]
            ],
            [
                'configFile' => config_path('permission.php'),
                'keys' => [
                    'cupparis.models' => [
                        'entry' => $this->snakeModel
                    ],
                    'policies.models' => [
                        'key' => '\\' . $this->modelsNamespaces . $this->studlyModel,
                        'entry' => '\\' . $this->policiesNamespaces . $this->studlyModel . 'Policy',
                    ],
                ],
            ],
        ];
    }

    protected function manageConfigs($configsData, $type)
    {


        foreach ($configsData as $configData) {

            $configFile = Arr::get($configData, 'configFile');
            $configKeys = Arr::get($configData, 'keys');

            if ($this->files->exists($configFile)) {
                $configValue = require $configFile;
            } else {
                $configValue = [];
            }


            $configFileString = "<?php\n\nreturn ";

//            Log::info("CONFIG FILE::: " . $configFile);
//            Log::info($configValue);
            foreach ($configKeys as $configKey => $configKeyValue) {
                $entry = Arr::get($configKeyValue, 'entry');
                $key = Arr::get($configKeyValue, 'key');

                $values = Arr::get($configValue, $configKey, []);

                $values = $this->configSubstitution($values, $entry, $type, $key);

                Arr::set($configValue, $configKey, $values);
            }

            $configFileString .= varexport($configValue) . ';';


            $this->files->put($configFile, $configFileString);

        }

    }


    protected function configSubstitution($values, $entry, $type, $key = null)
    {

        if ($type == 'u') {
            if ($key) {
                unset($values[$key]);
            } else {
                $values = Arr::reject($values, function ($item) use ($entry) {
                    return $item == $entry;
                });
            }
        } else {
            if ($key) {
                $values[$key] = $entry;
            } else {
                $values[] = $entry;
            }
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

    protected function getJsonFile($filename) {

        if (!$this->files->exists($filename)) {
            $this->files->put($filename,cupparis_pretty_json_encode(new \stdClass()));
        }
        return json_decode($this->files->get($filename),true);

    }

}

