<?php

namespace App\Policies;

use App\Models\User;
use App\Models\DatafileJsonRow;
use Gecche\PolicyBuilder\Facades\PolicyBuilder;
use Illuminate\Auth\Access\HandlesAuthorization;

class DatafileJsonRowPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\DatafileJsonRow  $model
     * @return mixed
     */
    public function view(User $user, DatafileJsonRow $model)
    {
        //
        if ($user && $user->can('view datafile_json_row')) {
            return true;
        }

        return false;

    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        //
        if ($user && $user->can('create datafile_json_row')) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\DatafileJsonRow  $model
     * @return mixed
     */
    public function update(User $user, DatafileJsonRow $model)
    {
        //
        if ($user && $user->can('edit datafile_json_row')) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\DatafileJsonRow  $model
     * @return mixed
     */
    public function delete(User $user, DatafileJsonRow $model)
    {
        //
        if ($user && $user->can('delete datafile_json_row')) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can access to the listing of the models.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function listing(User $user)
    {
        //
        if ($user && $user->can('listing datafile_json_row')) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can access to the listing of the models.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function acl(User $user, $builder)
    {

        if ($user && $user->can('view datafile_json_row')) {
            return PolicyBuilder::all($builder,DatafileJsonRow::class);
        }

        return PolicyBuilder::none($builder,DatafileJsonRow::class);

    }
}
