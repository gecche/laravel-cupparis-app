<?php namespace App\Models;

use Gecche\Cupparis\App\Breeze\Breeze;

class AppVar extends Breeze {

    //public $timestamps = false;

    protected $table = 'vars';
    
    protected $fillable = array('id', 'value');
    
    public static $relationsData = array(
        //'address' => array(self::HAS_ONE, 'Address'),
        //'orders'  => array(self::HAS_MANY, 'Order'),
        //'user' => array(self::BELONGS_TO, 'User'),
    );

    public static function initializeVar($id,$value = 0) {
        $appvar = static::findOrNew(['id'=>$id]);
        if ($appvar->value) {
            return $appvar;
        }
        $appvar->value = 0;
        $appvar->save();
        return $appvar;
    }



}
