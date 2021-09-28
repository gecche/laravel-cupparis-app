<?php namespace Gecche\Cupparis\App\Http\Controllers;

use Illuminate\Support\Facades\Response;

trait JsonControllerTrait
{

    protected $json = [
        'error' => 0,
        'msg' => '',
        'result' => null,
    ];


    protected function _error($msg)
    {
        $this->json['error'] = 1;
        $this->json['msg'] = $msg;
    }

    protected function _json($msg = false)
    {
        if ($msg !== false) {
            $this->json['msg'] = $msg;
        }
        return Response::json($this->json);
    }

    protected function _result($result) {
        $this->json['result'] = $result;
    }

}
