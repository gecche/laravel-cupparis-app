<?php

namespace Gecche\Cupparis\App\Foorm;

use Gecche\Foorm\FoormManager as BaseFoormManager;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class FoormManager extends BaseFoormManager
{

    protected $foormEntity;

    protected function setInputForForm($input)
    {


        $inputManipulationFunction = $this->inputManipulationFunction;

        if ($inputManipulationFunction instanceof \Closure) {
            return $inputManipulationFunction($input);
        }


        switch ($this->config['form_type']) {

            case 'list':
                $input = $this->setInputForFormList($input);

                return $input;
            default:
                return $input;

        }


    }


    protected function setInputForFormList($input)
    {
        $input['pagination'] = [
            'page' => Arr::get($input, 'page'),
            'per_page' => Arr::get($input, 'per_page'),
        ];

        $input['search_filters'] = [];
        $searchInputs = preg_grep_keys('/^s_/', $input);

        foreach ($searchInputs as $searchInputKey => $searchInputValue) {
            unset($input[$searchInputKey]);
            if (Str::endsWith($searchInputKey, '_operator')) {
                continue;
            }

            $searchFieldName = substr($searchInputKey, 2);
            $input['search_filters'][$searchFieldName] = [
                'field' => $searchFieldName,
                'op' => Arr::get($searchInputs, $searchInputKey . '_operator', '='),
                'value' => $searchInputValue,
            ];
        }

        if (array_key_exists('order_field', $input)) {
            $input['order_params'] = [
                'field' => $input['order_field'],
                'direction' => strtoupper(Arr::get($input, 'order_direction', 'ASC')),
            ];
            unset($input['order_field']);
            unset($input['order_direction']);
        }


        unset($input['page']);
        unset($input['per_page']);

        return $input;
    }

    /**
     * @return mixed
     */
    public function getFoorm($formName, Request $request, $params = [])
    {
        $formNameParts = explode('.', $formName);
        if (count($formNameParts) != 2) {
            throw new \InvalidArgumentException('A foorm name should be of type "<FORMNAME>.<FORMTYPE>".');
        }


        $this->foormName = $formName;
        $this->foormModel = $formNameParts[0];
        $this->foormEntity = $formNameParts[0];
        $this->foormType = $formNameParts[1];

        $this->request = $request;
        $this->buildParams($params);
        $this->getConfig();
        $this->setModel();

        return $this->setFoorm();
    }


    public function getConfig()
    {

        $defaultConfig = $this->baseConfig;

        $typeDefaults = Arr::get(Arr::get($defaultConfig, 'types_defaults', []), $this->foormType, []);

        unset($defaultConfig['types_defaults']);

        $defaultConfig = array_merge($defaultConfig, $typeDefaults);

        $formConfig = $this->getFormTypeConfig($this->foormName);
        $formConfig['entity'] = $this->foormEntity;

        $finalConfig = array_replace_recursive($defaultConfig, $formConfig);

        $snakeModelName = Arr::get($formConfig, 'model', $this->foormModel);
        $relativeModelName = Str::studly($snakeModelName);
        $fullModelName = $finalConfig['models_namespace'] . $relativeModelName;

        if (!class_exists($fullModelName))
            throw new \InvalidArgumentException("Model class $fullModelName does not exists");

        $finalConfig = array_merge($finalConfig, $this->getRealFoormClass($formConfig, $relativeModelName, $this->foormType));
        $this->normalizedFoormType = Arr::get($finalConfig, 'form_type', $this->foormType);

        $finalConfig['model'] = $snakeModelName;
        $finalConfig['relative_model_name'] = $relativeModelName;
        $finalConfig['full_model_name'] = $fullModelName;

        foreach (Arr::get($formConfig, 'dependencies', []) as $dependencyKey => $dependencyFormType) {
            $dependencyConfig = $this->getFormTypeConfig($this->foormModel . '.' . $dependencyFormType);


            $dependencyConfig = array_replace_recursive($defaultConfig, $dependencyConfig);
            $dependencyConfig = array_merge($dependencyConfig, $this->getRealFoormClass($formConfig, $relativeModelName, $dependencyFormType));

            $dependencyConfig['model'] = $snakeModelName;
            $dependencyConfig['relative_model_name'] = $relativeModelName;
            $dependencyConfig['full_model_name'] = $fullModelName;


            $finalConfig['dependencies'][$dependencyKey] = $dependencyConfig;
        }

        $this->config = $finalConfig;

        return $finalConfig;

    }

    protected function getRealFoormClass($formConfig, $relativeModelName, $formNameToCheck)
    {
        $snakeFormName = Arr::get($formConfig, 'form_type', $formNameToCheck);
        $relativeFormName = Str::studly($snakeFormName);

        $snakeEntityName = Arr::get($formConfig, 'entity', $this->foormEntity);
        $relativeEntityName = Str::studly($snakeEntityName);

        $fullFormName = $this->baseConfig['foorms_namespace'] . $relativeEntityName . "\\Foorm" . $relativeFormName;


        if (!class_exists($fullFormName)) {//Example: exists App\Foorm\User\List class?
            $fullFormName = $this->baseConfig['foorms_namespace'] . $relativeModelName . "\\Foorm" . $relativeFormName;
            if (!class_exists($fullFormName)) {//Example: exists App\Foorm\User\List class?

                $fullFormName = $this->baseConfig['foorms_namespace'] . $relativeFormName;
                if (!class_exists($fullFormName)) {//Example: exists App\Foorm\List class?
                    $fullFormName = $this->baseConfig['foorms_defaults_namespace'] . 'Foorm' . $relativeFormName;

                    if (!class_exists($fullFormName)) {//Example: exists Gecche\Foorm\List class?
                        throw new \InvalidArgumentException("Foorm class not found");
                    }

                }
            }

        }

        return [
            'form_type' => $snakeFormName,
            'relative_form_name' => $relativeFormName,
            'full_form_name' => $fullFormName,
        ];

    }

    protected function getRealFoormActionClass($action)
    {


        $relativeFormName = Arr::get($this->config, 'relative_form_name');
        $relativeModelName = Arr::get($this->config, 'relative_model_name');

        $snakeEntityName = Arr::get($this->config, 'entity', $this->foormEntity);
        $relativeEntityName = Str::studly($snakeEntityName);

        $fullFormActionName = $this->baseConfig['foorms_namespace'] . $relativeEntityName
            . "\\Actions\\" . Str::studly($action);


        if (!class_exists($fullFormActionName)) {//Example: exists App\Foorm\User\List class?

            $fullFormActionName = $this->baseConfig['foorms_namespace'] . $relativeModelName
                . "\\Actions\\" . Str::studly($action);
            if (!class_exists($fullFormActionName)) {

                $fullFormActionName = $this->baseConfig['foorms_namespace'] . "Actions\\" . Str::studly($action);
                if (!class_exists($fullFormActionName)) {//Example: exists App\Foorm\List class?
                    $fullFormActionName = $this->baseConfig['foorms_defaults_namespace']
                        . "Actions\\" . Str::studly($action);

                    if (!class_exists($fullFormActionName)) {//Example: exists Gecche\Foorm\List class?
                        throw new \InvalidArgumentException("Foorm Action class not found");
                    }

                }
            }

        }

        return [
            'full_form_action_name' => $fullFormActionName,
        ];

    }

}
