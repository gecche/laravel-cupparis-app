<?php

namespace Gecche\Cupparis\App\Foorm\Base;

use App\Models\Datafile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

trait CupparisDatafileFoormListTrait
{

    protected $displayOnlyErrors = false;

    protected $datafileId;
    protected $datafileModel;

    protected function init()
    {
        parent::init();
        $this->checkDisplayOnlyErrorsFromInput();
        $this->setDatafileId();
        return;
    }

    protected function checkDisplayOnlyErrorsFromInput()
    {
        if (Arr::get($this->input, 'datafile_only_errors')) {
            $this->displayOnlyErrors = true;
        }
    }

    protected function applyListBuilder()
    {
        if (Arr::get($this->customFuncs, 'listBuilder') instanceof \Closure) {
            $builder = $this->customFuncs['listBuilder'];
            $this->formBuilder = $builder($this->model);
            return;
        }

        $modelClass = get_class($this->model);
        $this->formBuilder = $modelClass::acl();

        if ($this->displayOnlyErrors) {
//            $errorIds = DB::table('datafile_error')
//                ->where('datafile_error.datafile_table_type', $modelClass)
//                ->where('datafile_id',$this->getDatafileId())
//                ->select(['id'])
//                ->get()
//                ->pluck('id', 'id')->all();

//            $this->paginateSelect = array($this->model->getTable() . ".*");
            $this->model->setDefaultOrderColumns(["datafile_sheet" => 'ASC', "row" => "ASC"]);
            $this->formBuilder->has('errors');
//            $this->formBuilder = $this->formBuilder->join('datafile_error', "datafile_error.datafile_table_id", "=",
//                $this->model->getTable() . ".id")
//                ->whereIn('datafile_error.id', $errorIds)
//                ->groupBy($this->model->getTable() . ".id");

        }

    }

    public function setFormMetadata()
    {
        parent::setFormMetadata();

        $this->setFormMetadataHasErrors();
        $this->setFormMetadataSheets();
    }

    protected function setDatafileId()
    {
        $fixedConstraints = Arr::get($this->params, 'fixed_constraints', []);
        $this->datafileId = null;
        foreach ($fixedConstraints as $fixedConstraint) {
            if (Arr::get($fixedConstraint, 'field') == 'datafile_id') {
                $this->datafileId = Arr::get($fixedConstraint, 'value');
                $this->datafileModel = Datafile::where('datafile_id',$this->datafileId)->first();
                return;
            }
        }
    }

    public function getDatafileId() {
        return $this->datafileId;
    }

    public function getDatafileModel() {
        return $this->datafileModel;
    }

    protected function setFormMetadataHasErrors()
    {
        $hasErrors = 0;
        if ($this->datafileId) {
            $hasErrors = DB::table('datafile_error')
                ->select(['datafile_table_id'])
                ->where("datafile_id", $this->datafileId)
                ->where('datafile_table_type', get_class($this->model))
                ->groupBy('datafile_table_id')
                ->get();

            $hasErrors = $hasErrors->count();

        }

        $this->formMetadata['has_datafile_errors'] = $hasErrors;
    }

    protected function setFormMetadataSheets()
    {
        $anyOption = [
            $this->config['null-value'] => $this->config['any-label'],
        ];


        $sheets = [

        ];
        if ($this->datafileModel) {
            $sheets = $this->datafileModel->datafile_sheet;
        }

        $sheets = $anyOption + $sheets;
        $this->formMetadata['sheets'] = $sheets;
        $this->formMetadata['sheets_order'] = array_keys($sheets);
    }

}
