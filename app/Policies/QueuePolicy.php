<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Queue;
use Gecche\PolicyBuilder\Facades\PolicyBuilder;
use Illuminate\Auth\Access\HandlesAuthorization;

class QueuePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Queue  $model
     * @return mixed
     */
    public function view(User $user, Queue $model)
    {
        //
        if ($user && $user->can('view queue')) {
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
        if ($user && $user->can('create queue')) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Queue  $model
     * @return mixed
     */
    public function update(User $user, Queue $model)
    {
        //
        if ($user && $user->can('edit queue')) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Queue  $model
     * @return mixed
     */
    public function delete(User $user, Queue $model)
    {
        //
        if ($user && $user->can('delete queue')) {
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
        if ($user && $user->can('listing queue')) {
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

        if ($user && $user->can('view queue')) {
            return PolicyBuilder::all($builder,Queue::class);
        }

        return PolicyBuilder::none($builder,Queue::class);

    }
}
