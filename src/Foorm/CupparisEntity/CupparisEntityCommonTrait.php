<?php

namespace Gecche\Cupparis\App\Foorm\CupparisEntity;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;

trait CupparisEntityCommonTrait
{

    protected $modelsList = [];

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
//        Log::info("ACTION INPUT");
//        Log::info($this->input);
        $this->msConfig = Config::get('cupparis-app.cupparis_entity', []);

        $this->langs = Arr::get($this->msConfig, 'langs', []);
        $this->stubs = Arr::get($this->msConfig, 'stubs', []);

        $this->migrationPath = 'database/migrations/';
        $this->vueApplicationPath = Arr::get($this->msConfig, 'vueApplicationPath', 'resources/vue-application-v4/src/application/');
        $this->vueApplicationModelConfsPath = $this->vueApplicationPath . Arr::get($this->msConfig, 'vueApplicationModelConfsPath', 'ModelConfs/');
        $this->vueApplicationConfigPath = $this->vueApplicationPath . Arr::get($this->msConfig, 'vueApplicationConfigPath', 'config/');
        $this->modelsNamespaces = Arr::get($this->msConfig, 'models_namespace', 'App\\Models\\');
        $this->policiesNamespaces = Arr::get($this->msConfig, 'policies_namespace', 'App\\Policies\\');

        if (Arr::get($this->input, 'id')) {
            $this->model = $this->model->find(Arr::get($this->input, 'id'));
        }
        $this->cosaFare = Arr::wrap(Arr::get($this->input, 'cosa', []));

        $this->snakeModel = Str::snake($this->model->model_class);
        $this->studlyModel = Str::studly($this->snakeModel);

        $this->files = new Filesystem();

    }

    protected function getModelsList()
    {
        $filesModels = $this->files->files(app_path('Models'));
        $models = [];
        foreach ($filesModels as $file) {
            if (Str::endsWith($file, '.php')) {
                $name = $file->getRelativePathName();
                $model = substr($name, 0, -4);

                if (class_exists($this->modelsNamespaces . $model)) {
                    $models[] = $model;
                }
            }
        }

        return $models;
    }


}