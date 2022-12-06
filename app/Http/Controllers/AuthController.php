<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use Validator;
use Throwable;
use App\Services\UserService;
use Illuminate\Support\Facades\Lang;

/**
 * @OA\Schema(
 *   schema="UserSchema",
 *   title="User Model",
 *   description="User model",
 *   @OA\Property(
 *     property="id", description="ID of the user",
 *     @OA\Schema(type="integer", example=1)
 *  ),
 *   @OA\Property(
 *     property="user_name", description="user_name of the user",
 *     @OA\Schema(type="string", example="admin_name")
 *  ),
 *  @OA\Property(
 *     property="full_name", description="full_name of the user",
 *     @OA\Schema(type="string", example="AR Admin")
 *  ),
 *  @OA\Property(
 *     property="email", description="email of the user",
 *     @OA\Schema(type="string", example="admin@yopmail.com")
 *  ),
 *  @OA\Property(
 *     property="phone", description="phone of the user",
 *     @OA\Schema(type="string", example="4544444444")
 *  ),
 *  @OA\Property(
 *     property="gender", description="gender of the user",
 *     @OA\Schema(type="integer", example="1")
 *  ),
 *  @OA\Property(
 *     property="birthday", description="birthday of the user",
 *     @OA\Schema(type="string", example="11/11/1990")
 *  ),
 *  @OA\Property(
 *     property="province", description="province of the user",
 *     @OA\Schema(type="string", example="Hà Nội")
 *  ),
 *  @OA\Property(
 *     property="city", description="city of the user",
 *     @OA\Schema(type="string", example="Thủ đô Hà Nội")
 *  ),
 *  @OA\Property(
 *     property="address", description="address of the user",
 *     @OA\Schema(type="string", example="Ba Dinh")
 *  ),
 *  @OA\Property(
 *     property="store_id", description="store_id of the user",
 *     @OA\Schema(type="integer", example="123")
 *  ),
 *  @OA\Property(
 *     property="store_name", description="store_name of the user",
 *     @OA\Schema(type="string", example="Cua Hang")
 *  ),
 *  @OA\Property(
 *     property="permission", description="permission of the user",
 *     @OA\Schema(type="string", example="role")
 *  ),
 *  @OA\Property(
 *     property="last_login", description="last_login of the user",
 *     @OA\Schema(type="timestamp", example="2020-01-01 00:00:01")
 *  ),
 *  @OA\Property(
 *     property="status", description="status of the user",
 *     @OA\Schema(type="integer", example="1")
 *  ),
 *  @OA\Property(
 *     property="created_by", description="created_by of the user",
 *     @OA\Schema(type="integer", example="1")
 *  ),
 *  @OA\Property(
 *     property="created_at", description="email of the user",
 *     @OA\Schema(type="timestamp", example="2020-01-01 00:00:01")
 *  ),
 *  @OA\Property(
 *     property="updated_by", description="updated_by of the user",
 *     @OA\Schema(type="integer", example="1")
 *  ),
 *  @OA\Property(
 *     property="updated_at", description="updated_at of the user",
 *     @OA\Schema(type="timestamp", example="2020-01-01 00:00:01")
 *  )
 * )
 */
class AuthController extends Controller
{
    /** @var UserService */
    private $userService;

    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct() {
        $this->userService = new UserService();
    }

}
