<?php namespace Gecche\Cupparis\App\Validation;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use InvalidArgumentException;
use Spatie\Activitylog\Models\Activity;

class Rules
{

    /*
     * Regola per validare l'esistenza di un valore su più tabelle/campi (ragiona in or: basta che ne trovi uno)
     * I parametri vanno di tre in tre: table, field e extra come exists, se non ci sono extra va comunque scritto
     * "null" e tenuti i parametri a tre a tre.
     * Gli extra sono a loro volta separati con il marcatore "#"
     *
     * Es: 'exists_or:users,name,null,users,email,id#2'
     * In questo caso viene ricercato il valore nella tabella users campo email (senza extra), se non esiste viene
     * cercato sempre nella tabella users campo name ma con l'aggiunta del fatto che l'id sia uguale a 1.
     *
     */
    public function existsOr($attribute, $value, $parameters, $validator)
    {

        if (count($parameters) < 3) {
            throw new InvalidArgumentException("Validation rule exists_or requires at least 3 parameters.");
        }

        $parametersCount = count($parameters);

        for ($i = 2; $i < $parametersCount; $i = $i + 3) {
            $table = $parameters[$i - 2];
            $column = ($parameters[$i - 1] === 'null') ? $attribute : $parameters[$i - 1];

            $extra = ($parameters[$i] === 'null') ? [] : explode('#', $parameters[$i]);

            $existParameters = array_merge([$table, $column], $extra);
            if ($validator->validateExists($attribute, $value, $existParameters)) {
                return true;
            }
        }

        return false;
    }

    public function captcha($attribute, $value, $parameters, $validator)
    {
        return captcha_check($value);
    }


    public function partitaIva($attribute, $value, $parameters, $validator)
    {
        return $this->checkPartitaIva($value);
    }

    public function codiceFiscale($attribute, $value, $parameters, $validator)
    {
        return $this->checkCodiceFiscale($value);
    }

    public function codiceFiscaleProfessional($attribute, $value, $parameters, $validator)
    {
        return ($this->checkPartitaIva($value) || $this->checkCodiceFiscale($value));
    }

    protected function checkPartitaIva($value)
    {

        if (strlen($value) != 11)
            return false;
        if (preg_match("/^[0-9]+\$/", $value) != 1)
            return false;
        $s = 0;
        for ($i = 0; $i <= 9; $i += 2)
            $s += ord($value[$i]) - ord('0');
        for ($i = 1; $i <= 9; $i += 2) {
            $c = 2 * (ord($value[$i]) - ord('0'));
            if ($c > 9) $c = $c - 9;
            $s += $c;
        }
        if ((10 - $s % 10) % 10 != ord($value[10]) - ord('0'))
            return false;
        return true;
    }


    protected function checkCodiceFiscale($value)
    {

        if (strlen($value) != 16)
            return false;
        $value = strtoupper($value);
        if (preg_match("/^[A-Z0-9]+\$/", $value) != 1) {
            return false;
        }
        $s = 0;
        for ($i = 1; $i <= 13; $i += 2) {
            $c = $value[$i];
            if (strcmp($c, "0") >= 0 and strcmp($c, "9") <= 0)
                $s += ord($c) - ord('0');
            else
                $s += ord($c) - ord('A');
        }
        for ($i = 0; $i <= 14; $i += 2) {
            $c = $value[$i];
            switch ($c) {
                case '0':
                    $s += 1;
                    break;
                case '1':
                    $s += 0;
                    break;
                case '2':
                    $s += 5;
                    break;
                case '3':
                    $s += 7;
                    break;
                case '4':
                    $s += 9;
                    break;
                case '5':
                    $s += 13;
                    break;
                case '6':
                    $s += 15;
                    break;
                case '7':
                    $s += 17;
                    break;
                case '8':
                    $s += 19;
                    break;
                case '9':
                    $s += 21;
                    break;
                case 'A':
                    $s += 1;
                    break;
                case 'B':
                    $s += 0;
                    break;
                case 'C':
                    $s += 5;
                    break;
                case 'D':
                    $s += 7;
                    break;
                case 'E':
                    $s += 9;
                    break;
                case 'F':
                    $s += 13;
                    break;
                case 'G':
                    $s += 15;
                    break;
                case 'H':
                    $s += 17;
                    break;
                case 'I':
                    $s += 19;
                    break;
                case 'J':
                    $s += 21;
                    break;
                case 'K':
                    $s += 2;
                    break;
                case 'L':
                    $s += 4;
                    break;
                case 'M':
                    $s += 18;
                    break;
                case 'N':
                    $s += 20;
                    break;
                case 'O':
                    $s += 11;
                    break;
                case 'P':
                    $s += 3;
                    break;
                case 'Q':
                    $s += 6;
                    break;
                case 'R':
                    $s += 8;
                    break;
                case 'S':
                    $s += 12;
                    break;
                case 'T':
                    $s += 14;
                    break;
                case 'U':
                    $s += 16;
                    break;
                case 'V':
                    $s += 10;
                    break;
                case 'W':
                    $s += 22;
                    break;
                case 'X':
                    $s += 25;
                    break;
                case 'Y':
                    $s += 24;
                    break;
                case 'Z':
                    $s += 23;
                    break;
                /*. missing_default: .*/
            }
        }
        if (chr($s % 26 + ord('A')) != $value[15])
            return false;
        return true;
    }


}
