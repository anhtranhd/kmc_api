<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject {

 
	use Notifiable, HasRoles;
 
	use SoftDeletes;
	
    public static $STATUS_EMAIL_UNCONFIRMED = 2;
    public $timestamps = true;
 

    public static $USER_TYPE_ADMIN = 'administrator';
    public static $USER_TYPE_CASUAL = 'casual';


 
	 const NEW = 0;
    const ACTIVE = 1;
    const LOCK = 2;
    const DELETED = 3; 
 
 
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'email',
        'password',
        'status',
        'user_name',
        'full_name',
        'gender',
        'phone',
        'birthday',
        'province',
        'city',
        'address',
        'store_id',
        'store_name',
        'permissions',
        'last_login',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
    ];
  

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = [
		'password',
	];

	/**
	 * Get the identifier that will be stored in the subject claim of the JWT.
	 *
	 * @return mixed
	 */
	public function getJWTIdentifier() {
		return $this->getKey();
	}

	/**
	 * Return a key value array, containing any custom claims to be added to the JWT.
	 *
	 * @return array
	 */
	public function getJWTCustomClaims() {
		return [];
	}

	public function getStatusNameAttribute() {
		$statusName = '';
		switch ($this->status) {
			case self::NEW:
				$statusName = trans('users.status.new');
				break;
			case self::ACTIVE:
				$statusName = trans('users.status.active');
				break;
			case self::LOCK:
				$statusName = trans('users.status.locked');
				break;
			case self::DELETED:
				$statusName = trans('users.status.deleted');
				break;
		}
		return $statusName;
	}

	public function medialImageable()
	{
		return $this->morphOne(MedialImageable::class, 'imageable');
	}
}
