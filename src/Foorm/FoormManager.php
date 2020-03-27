<?php

namespace Gecche\Cupparis\App\Foorm;

use Gecche\Foorm\FoormManager as BaseFoormManager;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class FoormManager extends BaseFoormManager
{

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

        if (array_key_exists('order_field',$input)) {
            $input['order_params'] = [
                'field' => $input['order_field'],
                'direction' => strtoupper(Arr::get($input,'order_direction','ASC')),
            ];
            unset($input['order_field']);
            unset($input['order_direction']);
        }



        unset($input['page']);
        unset($input['per_page']);

        return $input;
    }



}
