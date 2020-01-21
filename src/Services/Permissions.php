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

class Permissions
{

    protected $config = [];

    protected $allPermissions = [];

    /**
     * Permissions constructor.
     * @param string $configFilePath
     * @param array $aclModels
     * @param array $$this->configValues
     */
    public function __construct($configValues = [], $files = null)
    {
        $this->config = $configValues;
    }


    public function savePermissions()
    {


        Artisan::call('cache:clear');

        //CANCELLO TUTTE LE ASSOCIAZIONI TRA PERMESSI E RUOLI E PERMESSI E UTENTI
        $tableNames = Config::get('permission.table_names', []);
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        DB::table(Arr::get($tableNames, 'model_has_permissions', 'model_has_permissions'))->truncate();
        DB::table(Arr::get($tableNames, 'role_has_permissions', 'role_has_permissions'))->truncate();
        DB::table(Arr::get($tableNames, 'permissions', 'permissions'))->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');

        //INSERISCO IN AGGIORNAMENTO SELETTIVO RUOLI E PERMESSI IN MODO DA NON CANCELLARE EVENTUALI
        //ASSOCIAZIONI TRA UTENTI E RUOLI
        $this->seedRoles();
        $this->seedPermissions();
//
        $this->seedRolesPermissions();

        //PER IL GUEST NON SO SE E' POSSIBILE FARLO: IL METODO SAREBBE COMUNQUE PRATICAMENTE PRONTO
//        $this->seedGuestPermissions();


    }

    protected function seedRoles()
    {

        $roles = Arr::get($this->config, 'roles', []);
        $guards = Arr::get($this->config, 'guards', []);

        foreach ($roles as $guardName => $guardRoles) {

            if (!in_array($guardName,$guards)) {
                Role::where('guard_name', $guardName)
                    ->delete();
                continue;
            }

            Role::where('guard_name', $guardName)
                ->whereNotIn('name', $guardRoles)
                ->delete();

            $rolesInDb = Role::where('guard_name', $guardName)
                ->whereIn('name', $guardRoles)
                ->pluck('name')->all();


            foreach (array_diff($guardRoles, $rolesInDb) as $guardRole) {

                Role::create(['name' => $guardRole, 'guard_name' => $guardName]);

            }

        }

    }


    protected function getModelPermissionName($modelName, $prefix)
    {

        return $prefix . ' ' . $modelName;

    }

    public function seedPermissions()
    {

        $modelsPrefixes = Arr::get($this->config, 'models_permissions_prefixes', []);

        $models = Arr::get($this->config, 'models', []);

        $guards = Arr::get($this->config, 'guards', []);

        foreach ($modelsPrefixes as $guardName => $guardModelsPrefixes) {


            if (!in_array($guardName,$guards)) {
                continue;
            }

            foreach ($models as $modelName) {


                foreach ($guardModelsPrefixes as $guardModelsPrefix) {


                    Permission::create([
                        'name' => $this->getModelPermissionName($modelName, $guardModelsPrefix),
                        'guard_name' => $guardName,
                    ]);

                }

            }


        }


        $otherPermissions = Arr::get($this->config, 'other_permissions', []);

        foreach ($otherPermissions as $guardName => $guardPermissions) {

            if (!in_array($guardName,$guards)) {
                continue;
            }

            foreach ($guardPermissions as $guardPermission) {
                Permission::create([
                    'name' => $guardPermission,
                    'guard_name' => $guardName,
                ]);
            }
        }

        foreach ($guards as $guardName) {

            $this->allPermissions[$guardName] = Permission::where('guard_name',$guardName)->pluck('name')->all();

        }

    }


    protected function seedRolesPermissions()
    {

        $modelsPrefixes = Arr::get($this->config, 'models_permissions_prefixes', []);


        $roles_models_permissions = Arr::get($this->config, 'roles_models_permissions', []);
        $roles_other_permissions = Arr::get($this->config, 'roles_other_permissions', []);


        $roles = Arr::get($this->config, 'roles', []);
        $models = Arr::get($this->config, 'models', []);
        $guards = Arr::get($this->config, 'guards', []);

        $rolesGrantedPermissions = [];

        foreach ($roles_models_permissions as $guardName => $guardRoles) {

            if (!in_array($guardName,$guards)) {
                continue;
            }

            $configGuardRoles = Arr::get($roles, $guardName, []);

            foreach ($guardRoles as $roleName => $roleModels) {

                if (!in_array($roleName, $configGuardRoles)) {
                    continue;
                }

                $guardModelsPrefixes = Arr::get($modelsPrefixes, $guardName, []);

                foreach ($roleModels as $modelName => $grantedPermissions) {

                    if (!in_array($modelName, $models)) {
                        continue;
                    }


                    $grantedPermissions = is_null($grantedPermissions) ?
                        $guardModelsPrefixes : array_intersect($guardModelsPrefixes, $grantedPermissions);


                    foreach ($grantedPermissions as $grantedPrefixPermission) {
                        $grantedPermission = $this->getModelPermissionName($modelName, $grantedPrefixPermission);

                        if (in_array($grantedPermission,$this->allPermissions[$guardName])) {

                            $rolesGrantedPermissions[$guardName][$roleName][$grantedPermission] = $grantedPermission;
                        }
                    }


                }
            }

        }

        foreach ($roles_other_permissions as $guardName => $guardRoles) {

            if (!in_array($guardName,$guards)) {
                continue;
            }

            $configGuardRoles = Arr::get($roles, $guardName, []);

            foreach ($guardRoles as $roleName => $grantedPermissions) {

                if (!in_array($roleName, $configGuardRoles)) {
                    continue;
                }

                foreach ($grantedPermissions as $grantedPermission) {
                    if (in_array($grantedPermission,$this->allPermissions[$guardName])) {
                        $rolesGrantedPermissions[$guardName][$roleName][$grantedPermission] = $grantedPermission;
                    }
                }
            }
        }

        foreach ($rolesGrantedPermissions as $guardName => $guardRoles) {

            foreach ($guardRoles as $roleName => $grantedPermissions) {

                Role::findByName($roleName, $guardName)->syncPermissions($grantedPermissions);

            }

        }

    }

    protected function seedGuestPermissions()
    {
        $modelsPrefixes = Arr::get($this->config, 'models_permissions_prefixes', []);

        $guest_models_permissions = Arr::get($this->config, 'guest_models_permissions', []);
        $guest_other_permissions = Arr::get($this->config, 'guest_other_permissions', []);

        $models = Arr::get($this->config, 'models', []);
        $guards = Arr::get($this->config, 'guards', []);

        $guestGrantedPermissions = [];

        foreach ($guest_models_permissions as $guardName => $guardModels) {

            if (!in_array($guardName,$guards)) {
                continue;
            }
            $guardModelsPrefixes = Arr::get($modelsPrefixes, $guardName, []);

            foreach ($models as $modelName => $grantedPermissions) {

                if (!in_array($modelName, $models)) {
                    continue;
                }

                $grantedPermissions = is_null($grantedPermissions) ?
                    $guardModelsPrefixes : array_intersect($guardModelsPrefixes, $grantedPermissions);

                foreach ($grantedPermissions as $grantedPrefixPermission) {
                    $grantedPermission = $this->getModelPermissionName($modelName, $grantedPrefixPermission);
                    if (in_array($grantedPermissions,$this->allPermissions[$guardName])) {
                        $guestGrantedPermissions[$guardName][$grantedPermission] = $grantedPermission;
                    }
                }


            }

        }

        foreach ($guest_other_permissions as $guardName => $grantedPermissions) {

            foreach ($grantedPermissions as $grantedPermission) {
                if (in_array($grantedPermissions,$this->allPermissions[$guardName])) {
                    $guestGrantedPermissions[$guardName][$grantedPermission] = $grantedPermission;
                }
            }
        }

        foreach ($guestGrantedPermissions as $guardName => $grantedPermissions) {
            //Assign to guest
            //User::findById($guardName)->syncPermissions($grantedPermissions);
        }
    }

}
