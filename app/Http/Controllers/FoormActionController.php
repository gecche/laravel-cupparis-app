<?php
/**
 * Created by PhpStorm.
 * User: pier
 * Date: 29/01/2019
 * Time: 16:04
 */

namespace App\Http\Controllers;

use Gecche\Foorm\Facades\Foorm;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;

class FoormActionController extends Controller
{
    protected $foorm;

    protected $foormAction;

    protected $foormManager;

    protected $json = [
        'error' => 0,
        'msg' => '',
    ];


    public function foormAction($foormAction, $foorm, $foormType, $foormPk = null) {
        $params = $foormPk ? ['id' => $foormPk] : [];
        $this->buildAndGetFoormActionResult($foormAction, $foorm, $foormType, $params);
        return $this->_json();
    }

    public function delete($foormName, $foormType, $pk = null)
    {
        $params = $pk ? ['id' => $pk] : [];
        $this->buildAndGetFoormActionResult('delete', $foormName, $foormType, $params);
        return $this->_json();
    }

    public function migrate($pk = null)
    {

        $params = $pk ? ['id' => $pk] : [];
        $this->buildAndGetFoormActionResult('migrate', 'cupparis_entity', 'list', $params);
        return $this->_json();
    }

    public function rollback($pk = null)
    {

        $params = $pk ? ['id' => $pk] : [];
        $this->buildAndGetFoormActionResult('rollback', 'cupparis_entity', 'list', $params);
        return $this->_json();
    }

    public function import($pk = null)
    {

        $params = $pk ? ['id' => $pk] : [];
        $this->buildAndGetFoormActionResult('import', 'cupparis_entity', 'list', $params);
        return $this->_json();
    }

    public function foormCAction($foormcaction, $foorm, $foormType, $constraintField, $constraintValue, $foormPk = null)
    {
        $params = $this->prepareFixedConstraints($constraintField, $constraintValue, $foorm, $foormType);
        if ($pk) {
            $params['id'] = $pk;
        }
        $this->buildAndGetFoormActionResult($foormcaction, $foorm, $foormType, $params);
        return $this->_json();
    }

    protected function prepareFixedConstraints($constraintField, $constraintValue, $foorm = null, $foormType = null)
    {
        switch ($foorm) {
            default:
                return [
                    'fixed_constraints' => [
                        [
                            'field' => $constraintField,
                            'value' => $constraintValue,
                        ],
                    ]
                ];

        }

    }


    protected function buildAndGetFoormActionResult($action, $foormName, $foormType, $params)
    {

        try {
            $this->buildFoormAction($action, $foormName, $foormType, $params);
            $this->getFoormActionResult();
        } catch (\Exception $e) {
            Log::info("FOORM ACTION CONTROLLER EXCEPTION");
            Log::info($e->getMessage());
            Log::info($e->getTraceAsString());
            $this->_error($e->getMessage());
        }
    }


    protected function buildFoormAction($action, $foormName, $foormType, $params)
    {
        $this->foormAction = Foorm::getFoormAction($action, "$foormName.$foormType", request(), $params);
    }

    protected function getFoormActionResult()
    {

        $this->foormAction->validateAction();
        $this->json['result'] = $this->foormAction->performAction();

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
}
