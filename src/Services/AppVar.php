<?php namespace Gecche\Cupparis\App\Services;

use App\Models\AppVar as AppVarModel;
use Illuminate\Support\Facades\Config;

class AppVar
{

    /**
     * Returns the *Singleton* instance of this class.
     *
     * @staticvar Singleton $instance The *Singleton* instances of this class.
     *
     */
    public static function getInstance()
    {
        static $instance = null;
        if (null === $instance) {
            $instance = new static();
        }

        return $instance;
    }

    /**
     * Protected constructor to prevent creating a new instance of the
     * *Singleton* via the `new` operator from outside of this class.
     */
    protected function __construct()
    {

    }

    /**
     * Private clone method to prevent cloning of the instance of the
     * *Singleton* instance.
     *
     * @return void
     */
    private function __clone()
    {
    }

    /**
     * Private unserialize method to prevent unserializing of the *Singleton*
     * instance.
     *
     * @return void
     */
    private function __wakeup()
    {
    }


    public static function setValue($id,$value) {
        $appVarModel = static::getVar($id);
        $appVarModel->value = $value;
        $appVarModel->save();
    }

    public static function getValue($id) {
        $appVarModel = static::getVar($id);
        return $appVarModel->value;
    }

    protected static function getVar($id) {
        $appVarModel = AppVarModel::find($id);
        if (!$appVarModel) {
            $defaultValue = Config::get('cupparis-app.vars.'.$id);
            return AppVar::create(['id' => $id,'value' => $defaultValue]);
        }
        return $appVarModel;
    }
}
