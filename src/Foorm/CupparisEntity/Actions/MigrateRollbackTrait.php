<?php

namespace Gecche\Cupparis\App\Foorm\CupparisEntity\Actions;

use Gecche\Cupparis\App\Foorm\CupparisEntity\CupparisEntityCommonTrait;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

trait MigrateRollbackTrait
{

    use CupparisEntityCommonTrait;

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


    protected function getJsonFile($filename) {

        if (!$this->files->exists($filename)) {
            $this->files->put($filename,cupparis_pretty_json_encode(new \stdClass()));
        }
        return json_decode($this->files->get($filename),true);

    }

}

