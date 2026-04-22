<?php
/**
 * Created by PhpStorm.
 * User: pier
 * Date: 29/01/2019
 * Time: 16:04
 */

namespace App\Http\Controllers;

use App\Queue\FoormActionQueue;
use Gecche\Cupparis\Queue\Facades\CupparisQueue;
use Gecche\Foorm\Facades\Foorm;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;

class FoormActionQueueController extends Controller
{

    use JsonControllerTrait;


    public function foormAction($foormAction, $foorm, $foormType, $foormPk = null) {
        $inputData = [
            'input' => request()->all(),
            'foorm_action' => $foormAction,
            'foorm' => $foorm,
            'foorm_type' => $foormType,
            'foorm_pk' => $foormPk,
        ];

        try {
            $result = CupparisQueue::add('foorm-action', 'run',$inputData);
        } catch (\Throwable $e) {
            return $this->_error($e->getMessage());
        }


        $this->json['result'] = $result;
        return $this->_json();
    }

    public function foormCAction($foormcaction, $foorm, $foormType, $constraintField, $constraintValue, $foormPk = null) {
        $inputData = [
            'input' => request()->all(),
            'foorm_action' => $foormcaction,
            'foorm' => $foorm,
            'foorm_type' => $foormType,
            'foorm_pk' => $foormPk,
            'constraint_field' => $constraintField,
            'constraint_value' => $constraintValue,
        ];

        try {
            $result = CupparisQueue::add('foorm-action', 'run',$inputData);
        } catch (\Throwable $e) {
            return $this->_error($e->getMessage());
        }


        $this->json['result'] = $result;
        return $this->_json();
    }



}
