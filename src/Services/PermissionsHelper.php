<?php namespace Gecche\Cupparis\App\Services;

use App\Models\User;
use Cupparis\Filesystem\Filesystem;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Validator;
use Illuminate\Contracts\Auth\Registrar as RegistrarContract;

class PermissionsHelper
{


    public static function getUsersIdsbyRoles($roles = [],$asArray = true) {

        $roles = Arr::wrap($roles);

        if (count($roles) < 1) {
            return [];
        }

        $idsCollection = DB::table('model_has_roles')
            ->select('model_id')
            ->join('roles','model_has_roles.role_id','=','roles.id')
            ->whereIn('roles.name',$roles)
            ->get()
            ->pluck('model_id','model_id');


        return $asArray ? $idsCollection->all() : $idsCollection;

    }

}
