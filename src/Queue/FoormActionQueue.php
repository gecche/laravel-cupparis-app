<?php namespace Gecche\Cupparis\App\Queue;

use Gecche\Cupparis\Queue\Queues\MainQueue;

use Gecche\Foorm\Facades\FoormQueue;
use Illuminate\Support\Arr;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class FoormActionQueue extends MainQueue
{


    public function run($job, $data)
    {

        $this->jobStart($job, $data, 'foorm-action');
        try {

            $this->validateData("foorm-action");
            $this->ensureUser(Arr::get($data, 'userId'));


            $input = Arr::get($data, 'input');
            $foormAction = Arr::get($data, 'foorm_action');
            $foorm = Arr::get($data, 'foorm');
            $foormType = Arr::get($data, 'foorm_type');
            $foormPk = Arr::get($data, 'foorm_pk');

            $constraintField = Arr::get($data, 'constraint_field');
            $constraintValue = Arr::get($data, 'constraint_value');
            if ($constraintField && $constraintValue) {
                $params = $this->prepareFixedConstraints($constraintField, $constraintValue, $foorm, $foormType);
            } else {
                $params = [];
            }

            if ($foormPk) {
                $params['id'] = $foormPk;
            }

            $this->buildAndGetFoormActionResult($input, $foormAction, $foorm, $foormType, $params);

//            Log::info('hereENDENF2');
            $this->jobEnd();
//            Log::info('hereENDENF3');

        } catch (\Throwable $e) {
            Log::info("FOORM ACTION QUEUE EXCEPTION");
            Log::info($e->getMessage());
            Log::info($e->getTraceAsString());

            $this->jobEnd(1, $e->getMessage() . " in " . $e->getFile() . " " . $e->getLine());
            throw $e;
        }

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
    protected function validateData($job_type)
    {

        if (!Arr::get($this->data, "userId", false)) {
            throw new Exception("Utente non definito!");
        }
        foreach (['input','foorm_action','foorm', 'foorm_type', 'foorm_pk'] as $key) {
            if (!array_key_exists($key,$this->data)) {
                throw new Exception("Parametro obbligatorio $key non definito!");
            }
        }
    }



    protected function buildAndGetFoormActionResult($input, $action, $foormName, $foormType, $params)
    {

            $this->buildFoormAction($input, $action, $foormName, $foormType, $params);
            $this->getFoormActionResult();
    }


    protected function buildFoormAction($input, $action, $foormName, $foormType, $params)
    {
        $this->foormAction = FoormQueue::getFoormAction($action, "$foormName.$foormType", $input, $params);
    }

    protected function getFoormActionResult()
    {

        $this->foormAction->validateAction();
        $this->output_data = $this->foormAction->performAction();

    }


    protected function ensureUser($userId) {
        $user = Auth::user();
        if (!$user) {
            Auth::loginUsingId($userId);
        }
    }
}
