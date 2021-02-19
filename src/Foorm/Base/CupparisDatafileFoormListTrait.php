<?php

namespace Gecche\Cupparis\App\Foorm\Base;

use Illuminate\Support\Arr;

trait CupparisDatafileFoormListTrait
{

    protected $displayOnlyErrors = false;

    protected function init()
    {
        parent::init();
        $this->checkDisplayOnlyErrorsFromInput();
        return;
    }

    protected function checkDisplayOnlyErrorsFromInput() {
        if (Arr::get($this->input,'datafile_only_errors')) {
            $this->displayOnlyErrors = true;
        }
    }

    protected function applyListBuilder()
    {
        if (Arr::get($this->customFuncs,'listBuilder') instanceof \Closure) {
            $builder = $this->customFuncs['listBuilder'];
            $this->formBuilder = $builder($this->model);
            return;
        }

        $modelClass = get_class($this->model);
        $this->formBuilder = $modelClass::acl();

        if ($this->displayOnlyErrors) {
            $this->paginateSelect = array($this->model->getTable().".*");
            $this->model->setDefaultOrderColumns(["datafile_sheet" => 'ASC',"row" => "ASC"]);
            $this->formBuilder = $this->formBuilder->join('datafile_error',"datafile_error.datafile_table_id", "=",$this->model->getTable().".id")
                ->where('datafile_error.datafile_table_type',$modelClass)
                ->groupBy("datafile_error.datafile_table_id");

        }

    }


}
