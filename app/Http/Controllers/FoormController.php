<?php
/**
 * Created by PhpStorm.
 * User: pier
 * Date: 29/01/2019
 * Time: 16:04
 */

namespace App\Http\Controllers;

use App\Services\UploadService;

use Gecche\Foorm\Facades\Foorm;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Str;

class FoormController extends Controller
{
    protected $foorm;

    protected $json = [
        'error' => 0,
        'msg' => '',
    ];

    public function getSearch($foormName, $type = 'search')
    {
        $this->buildAndGetFoormResult($foormName, $type, []);
        return $this->_json();
    }

    public function getListConstrained($foormName, $constraintField, $constraintValue, $type = 'list')
    {
        $params = $this->prepareFixedConstraints($constraintField, $constraintValue);
        $this->buildAndGetFoormResult($foormName, $type, $params);
        return $this->_json();
    }

    public function getList($foormName, $type = 'list')
    {
        $this->buildAndGetFoormResult($foormName, $type, []);
        return $this->_json();
    }

    public function getNew($foormName, $type = 'insert')
    {
        $this->buildAndGetFoormResult($foormName, $type, []);
        return $this->_json();
    }

    public function postCreate($foormName, $type = 'insert')
    {
        $this->buildAndGetFoormResult($foormName, $type, [], ['save']);
        return $this->_json();
    }


    public function getEdit($foormName, $pk, $type = 'edit')
    {
        $params = ['id' => $pk];
        $this->buildAndGetFoormResult($foormName, $type, $params);
        return $this->_json();

    }

    public function postUpdate($foormName, $pk, $type = 'edit')
    {
        $params = ['id' => $pk];
        $this->buildAndGetFoormResult($foormName, $type, $params, ['save']);
        return $this->_json();
    }

    public function getShow($foormName, $pk, $type = 'view')
    {
        $params = ['id' => $pk];
        $this->buildAndGetFoormResult($foormName, $type, $params);
        return $this->_json();
    }

    /*
     * QUESTA VA VISTA E SE E COME SPOSTARLA NELLE ACTIONS O ADDIRITTURA FUORI DA QUI
     */

    public function uploadfile()
    {
        try {
            $type = Request::get("resource_type", "foto");

            $uploadService = UploadService::getInstance();

            $uploadService->validate($type, Request::all());

            //PASSO LA VALIDAZIONE


            $file = Request::file('file');

            $tempFileArray = $uploadService->saveTempFile($type, $file);


            // $temp_array = array_merge($temp_array, array_intersect_key(Request::all(), $temp_array));
            $this->json['msg'] = Lang::get('app.upload_success');
            $this->json['result'] = $tempFileArray;
        } catch (\Exception $e) {
            $this->_error($e->getMessage());
            $this->json['result'] = [];
        }
        return $this->_json();
    }

    protected function _error($msg)
    {
        $this->json['error'] = 1;
        $this->json['msg'] = $msg;
    }

    protected function _json()
    {
        return Response::json($this->json);
    }

    protected function prepareFixedConstraints($constraintField, $constraintValue)
    {
        return [
            'fixed_constraints' => [
                [
                    'field' => $constraintField,
                    'value' => $constraintValue,
                ],
            ]
        ];
    }

    protected function buildAndGetFoormResult($foormName, $type, $params, $furtherActions = [])
    {

        try {
            $this->buildFoorm($foormName, $type, $params);
            $this->performFurtherActionsOnFoorm($furtherActions);
            $this->getFoormResult();
        } catch (\Exception $e) {
            $this->_error($e->getMessage());
        }
    }

    protected function buildFoorm($foormName, $type, $params)
    {
        $this->foorm = Foorm::getFoorm("$foormName.$type", request(), $params);
    }

    protected function performFurtherActionsOnFoorm($furtherActions = []) {
        foreach ($furtherActions as $action) {
            if (method_exists($this->foorm,$action)) {
                $this->foorm->$action();
            }
        }
    }

    protected function getFoormResult()
    {

        $this->json['result'] = $this->foorm->getFormData();
        $this->json['metadata'] = $this->foorm->getFormMetadata();

    }

}
