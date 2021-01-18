<?php

namespace Gecche\Cupparis\App;

use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;

use Illuminate\Support\Str;


/*
 * CLASSE PER GESTIRE SERVIZI GENERICI DELLA CUPPARIS APP COME LE VARIE SETTINGS NEL JSON.
 */

trait CupparisJsonTrait
{


    public function getJsonValue($key, $array, $default = null, $arrayDotted = [])
    {

        if (array_key_exists($key,$arrayDotted)) {
            return $arrayDotted[$key];
        }

        $parts = explode('.',$key);

        $part = array_shift($parts);
        $result = $this->getGroup($part,$parts,$array);
        return  $result !== false ? $result : $default;

    }

    protected function getGroup($part,$parts,$array) {
        if (!array_key_exists($part,$array)) {
            return false;
        }
        $array = $array[$part];
        if (!$parts) {
            return $array;
        }
        if (!is_array($array)) {
            return false;
        }
        $part = array_shift($parts);
        return $this->getGroup($part,$parts,$array);
    }

    public function jsonEncode($json) {
        return json_encode($json,JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    }
}
