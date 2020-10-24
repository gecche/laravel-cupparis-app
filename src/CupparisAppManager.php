<?php

namespace Gecche\Cupparis\App;

use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;

use Illuminate\Support\Str;


/*
 * CLASSE PER GESTIRE SERVIZI GENERICI DELLA CUPPARIS APP COME LE VARIE SETTINGS NEL JSON.
 */

class CupparisAppManager
{

    use CupparisJsonTrait;

    protected $config = [];

    protected $appSettings = [];
    protected $appSettingsDotted = [];

    /**
     * Protected constructor to prevent creating a new instance of the
     * *Singleton* via the `new` operator from outside of this class.
     */
    public function __construct($config)
    {
        $this->config = $config;
        $jsonSettingsFile = Arr::get($config, 'json_file', base_path('cupparis-app.json'));

        if (file_exists($jsonSettingsFile)) {
            $fileContents = file_get_contents($jsonSettingsFile);
            $json = json_decode($fileContents, true);
            $this->appSettings = $json;
            $this->appSettingsDotted = Arr::dot($json);
        }
    }

    public function get($key, $default = null)
    {

        return $this->getJsonValue($key,$this->appSettings,$default,$this->appSettingsDotted);

    }
}
