<?php

namespace Gecche\Cupparis\App\Models;

use Gecche\Cupparis\App\Breeze\Breeze;
use Gecche\Bannable\Bannable;
use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\MustVerifyEmail;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Contracts\Auth\MustVerifyEmail as MustVerifyEmailContract;
use Gecche\Bannable\Contracts\Bannable as BannableContract;
use App\Models\Foto;
use App\Models\Attachment;

class User extends Breeze implements
    AuthenticatableContract,
    AuthorizableContract,
    CanResetPasswordContract,
    MustVerifyEmailContract,
    BannableContract
{
	use Relations\UserRelations;


    use Authenticatable, Authorizable, CanResetPassword, MustVerifyEmail;
    use Bannable;
    use Notifiable;

    use HasRoles;
    use HasFactory;


    public $ownerships = true;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];


    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public static $relationsData = [
        'fotos' => [self::MORPH_MANY, 'related' => Foto::class, 'name' => 'mediable'],
        'attachments' => [self::MORPH_MANY, 'related' => Attachment::class, 'name' => 'mediable'],
        'roles' => [
            self::MORPH_TO_MANY,
            'related' => \App\Models\Role::class,
            'name' => 'model',
            'table' => 'model_has_roles',
            'foreignPivotKey' => 'model_id',
            'relatedPivotKey' => 'role_id'
        ],
        'permissions' => [
            self::MORPH_TO_MANY,
            'related' => \App\Models\Permission::class,
            'name' => 'model',
            'table' => 'model_has_permissions',
            'foreignPivotKey' => 'model_id',
            'permission_id'
        ]
//        'cliente' => [self::BELONGS_TO, 'related' => 'App\Models\Cliente'],
//        'tickets' => [self::HAS_MANY, 'related' => 'App\Models\Ticket'],
    ];

    public $appends = [
        'mainrole'
    ];

    public static $rules = array(
        'name' => 'required|between:4,255|unique:users,name|username_email',
        'email' => 'required|email|unique:users,email',
        'password' => ['required', 'string', 'min:8', 'confirmed'],
//        'password' => 'required|alpha_num|between:4,16|confirmed',
//        'password_confirmation' => 'required|alpha_num|between:4,16',
//        'nome' => 'between:1,255',
//        'cognome' => 'between:1,255',
    );


    public function getMainroleAttribute()
    {
        return $this->roles()->first();
    }

    public function getRolename() {
        return $this->mainrole ? $this->mainrole->name : null;
    }


    public $columnsForSelectList = ['name','email'];
    //['id','nome_it'];

    public $defaultOrderColumns = ['email' => 'ASC'];
    //['cognome' => 'ASC','nome' => 'ASC'];

    public $columnsSearchAutoComplete = ['name', 'email'];
    //['cognome','denominazione','codicefiscale','partitaiva'];

    public $nItemsAutoComplete = 20;
    public $nItemsForSelectList = 100;
    public $itemNoneForSelectList = false;
    public $fieldsSeparator = ' - ';
}
