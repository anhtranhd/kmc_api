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
