<?php

namespace Gecche\Cupparis\App\Foorm\Base\Actions;


use App\Models\Datafile;
use Gecche\Foorm\FoormAction;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Barryvdh\Snappy\Facades\SnappyPdf as PDF;

class FlushDatafile extends FoormAction
{


    protected function init()
    {
        parent::init();

    }


    public function performAction()
    {

        $datafileId = $this->getFoorm()->getDatafileId();


        if (!$datafileId) {
            throw new \Exception("Datafile non definito");
        }

        $datafileTable = $this->getModel()->getTable();

        DB::beginTransaction();
        try {

            DB::table($datafileTable)
                ->where('datafile_id', $datafileId)
                ->delete();

            DB::table('datafile_error')
                ->where('datafile_id', $datafileId)
                ->delete();

            DB::table('datafile_check')
                ->where('datafile_id', $datafileId)
                ->delete();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
        }

        $this->actionResult = true;

    }


    public function validateAction()
    {

        return true;

    }

}
