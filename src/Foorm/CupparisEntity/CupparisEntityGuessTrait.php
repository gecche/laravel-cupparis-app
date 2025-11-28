<?php

namespace Gecche\Cupparis\App\Foorm\CupparisEntity;


use Gecche\Cupparis\App\Enums\CupparisTipiCampi;
use App\Models\CupparisEntityField;
use Gecche\Cupparis\App\Foorm\Base\FoormInsert as BaseFoormInsert;
use Gecche\DBHelper\Facades\DBHelper;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

trait CupparisEntityGuessTrait
{



    protected function guessWidgetSearch($fieldName, $tipoCampo, $relazioneModello)
    {

        if ($relazioneModello) {
            return 'w-select';
        }
        switch ($tipoCampo) {
            case CupparisTipiCampi::STRING->value:
                return 'w-input';
            case CupparisTipiCampi::DATE->value:
                return 'w-input-date';
            case CupparisTipiCampi::BOOLEAN:
                return 'w-select';
            default:
                return null;
        }

    }

    protected function guessWidgetList($fieldName, $tipoCampo, $relazioneModello)
    {

        if ($relazioneModello) {
            return 'w-belongsto';
        }
        switch ($tipoCampo) {
            case CupparisTipiCampi::BOOLEAN->value:
                return 'w-swap';
            case CupparisTipiCampi::DATE->value:
            case CupparisTipiCampi::DATETIME->value:
                return 'w-date-text';
            default:
                return 'w-text';
        }

    }

    protected function guessWidgetEdit($fieldName, $tipoCampo, $relazioneModello)
    {

        if ($relazioneModello) {
            return 'w-select';
        }
        switch ($tipoCampo) {
            case CupparisTipiCampi::BOOLEAN->value:
                return 'w-select';
            case CupparisTipiCampi::TEXT->value:
                return 'w-editor';
            case CupparisTipiCampi::JSON->value:
                return 'w-textarea';
            case CupparisTipiCampi::DATE->value:
                return 'w-input-date';
            case CupparisTipiCampi::DECIMAL->value:
            case CupparisTipiCampi::FLOAT->value:
                return 'w-input-number';
            case CupparisTipiCampi::DATETIME->value:
                return null;
            default:
                return 'w-input';
        }

    }

    protected function guessRelationModel($fieldName)
    {

        $fieldName = Str::beforeLast($fieldName, '_id');
        $modelName = Str::studly($fieldName);
        return in_array($modelName, $this->modelsList) ? $modelName : null;

    }

    protected function guessCampo($fieldName, $isId)
    {
        if ($isId) {
            return [CupparisTipiCampi::ID_INTEGER->value, null];
        }

        if (Str::endsWith($fieldName, ['_it', '_en', '_fr', '_es', '_cn', '_ru', '_us', '_de'])) {
            $fieldName = substr($fieldName, 0, -3);
        }

        $booleanFieldNames = [
            'attivo',
            'attiva',
        ];
        if (in_array($fieldName, $booleanFieldNames) || Str::endsWith($fieldName, 'ed')) {
            return [CupparisTipiCampi::BOOLEAN->value, null];
        }

        $integerFieldNames = [
            'ordine',
            'priorita',
            'order',
            'priority',
            'numero',
            'quantita',
        ];
        if (in_array($fieldName, $integerFieldNames)) {
            return [CupparisTipiCampi::INTEGER->value, null];
        }

        $jsonFieldNames = [
            'info',
            'informazioni',
        ];
        if (in_array($fieldName, $jsonFieldNames) || Str::endsWith($fieldName, '_info')) {
            return [CupparisTipiCampi::JSON->value, null];
        }

        $textFieldNames = [
            'descrizione',
            'description',
            'note',
            'notes',
            'text',

        ];
        if (in_array($fieldName, $textFieldNames)) {
            return [CupparisTipiCampi::TEXT->value, null];
        }


        $decimalFieldName = [
            'prezzo',
            'importo',
            'imponibile',
            'iva',
            'imposta',
            'price',
            'total',
            'grand_total',
            'vat',
            'tax',
            'taxes',
        ];
        if (in_array($fieldName, $decimalFieldName)) {
            return [CupparisTipiCampi::DECIMAL->value, "10,2"];
        }

        $dateFieldName = [

        ];
        if (in_array($fieldName, $dateFieldName) || Str::endsWith($fieldName, ['data', 'date'])
            || Str::startsWith($fieldName, ['data', 'date'])
        ) {
            return [CupparisTipiCampi::DATE->value, null];
        }

        $datetimeFieldName = [

        ];
        if (in_array($fieldName, $datetimeFieldName) || Str::endsWith($fieldName, ['_at', '_il'])
        ) {
            return [CupparisTipiCampi::DATETIME->value, null];
        }

        return [CupparisTipiCampi::STRING->value, null];
    }


}
