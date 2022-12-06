<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserToken;
use Carbon\Carbon;
use Illuminate\Contracts\Validation\Validator as ReturnedValidator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Lang;
use League\Fractal\Resource\Collection;
use App\Http\Transformers\UserTransformer;
use League\Fractal\Manager;
use App\Services\EmailService;


/**
 * Class UserService
 *
 * @package App\Services
 */
class UserService
{
    /**
     * Validate request on login
     *
     * @param  Request  $request
     *
     * @return ReturnedValidator
     */
    public function validateLoginRequest(Request $request)
    {
        $rules = [
            'email'    => 'required|email',
            'password' => 'required'
        ];

        $messages = [
            'email.required'         => Lang::get('errors.validation.email.required'),
            'email.email'            => Lang::get('errors.validation.email.required'),
            'password.required'      => Lang::get('errors.validation.password.required')
        ];

        return Validator::make($request->all(), $rules, $messages);
    }

    /**
     * Validate request on register
     *
     * @param  Request  $request
     *
     * @return ReturnedValidator
     */
    public function validateRegisterRequest(Request $request)
    {
        $rules = [
            'user_name'           => 'required|user_name',
            'email'          => 'required|email|unique_encrypted:users,email',
            'password'       => 'required',
            'retypePassword' => 'required|same:password'
        ];

        $messages = [
            'user_name.required'      => Lang::get('errors.validation.email.required'),
            'user_name.user_name'     => Lang::get('errors.validation.email.required'),
            'email.required'          => Lang::get('errors.validation.email.required'),
            'email.email'             => Lang::get('errors.validation.email.required'),
            'email.unique_encrypted'  => Lang::get('errors.validation.email.required'),
            'password.required'       => Lang::get('errors.validation.email.required'),
            'password.min'            => Lang::get('errors.validation.email.required'),
            'password.regex'          => Lang::get('errors.validation.email.required'),
            'retypePassword.required' => Lang::get('errors.validation.email.required'),
            'retypePassword.same'     => Lang::get('errors.validation.email.required'),
        ];

        return Validator::make($request->all(), $rules, $messages);
    }

    /**
     * Validate request on update user
     *
     * @param  Request  $request
     *
     * @return ReturnedValidator
     */
    public function validateUpdateUserRequest(Request $request)
    {
        $rules = [
            'user_name'   => 'string',
            'full_name'   => 'string',
            'email'       => 'required|email',
            'phone'       => 'string',
            'gender'      => 'required|integer',
            'birthday'    => 'string',
            'province'    => 'string',
            'city'        => 'string',
            'address'     => 'string'
        ];

        $messages = [
            'user_name.string'   => Lang::get('errors.validation.user_name.string'),
            'email.required'     => Lang::get('errors.validation.email.required'),
            'email.email'        => Lang::get('errors.validation.email.email'),
            'phone.string'       => Lang::get('errors.validation.phone.string'),
            'gender.required'             => Lang::get('errors.validation.gender.required'),
            'gender.integer'             => Lang::get('errors.validation.gender.integer'),
            'birthday'           => Lang::get('errors.validation.birthday.string'),
            'province'           => Lang::get('errors.validation.province.string'),
            'city'               => Lang::get('errors.validation.city.string'),
            'address'            => Lang::get('errors.validation.address.string'),
        ];

        return Validator::make($request->all(), $rules, $messages);
    }

    /**
     * Update logged user
     *
     * @param  User  $user
     * @param  Request  $request
     * @param  Language  $language
     */
    public function updateLoggedUser(User &$user, Request $request)
    {
        $user->user_name           = $request->get('user_name');
        $user->full_name           = $request->get('full_name');
        $user->phone               = $request->get('phone');
        $user->gender              = $request->get('gender');
        $user->birthday            = $request->get('birthday');
        $user->province            = $request->get('province');
        $user->city                = $request->get('city');
        $user->address           = $request->get('address');
        
        $user->save();
    }

    /**
     * Validate request on update user picture
     *
     * @param  Request  $request
     *
     * @return ReturnedValidator
     */
    public function validateUpdateUserPictureRequest(Request $request)
    {
        $rules = [
            'picture' => 'required|image',
        ];

        $messages = [
            'picture.required' => Lang::get('errors.validation.email.required'),
            'picture.image'    => Lang::get('errors.validation.email.required'),
        ];

        return Validator::make($request->all(), $rules, $messages);
    }

    /**
     * Change logged user picture
     *
     * @param $user
     * @param $picture
     */
    public function updateLoggedUserPicture(&$user, $picture)
    {
        /** @var User $user */
        $user = Auth::user();

        $pictureExtension     = $picture->getClientOriginalExtension();
        $generatedPictureName = str_replace(' ', '_', $user->name) . '_' . time() . '.' . $pictureExtension;

        $path = 'uploads/users/';
        File::makeDirectory($path, 0777, true, true);

        $baseService = new BaseService();

        $pictureData = $baseService->processImage($path, $picture, $generatedPictureName, true);

        if ($pictureData) {
            if ($user->picture) {
                foreach ($user->picture as $oldPicture) {
                    if ($oldPicture && file_exists($oldPicture)) {
                        unlink($oldPicture);
                    }
                }
            }

            $user->picture = $pictureData;
        }

        $user->save();
    }

    /**
     * Validate request on forgot password
     *
     * @param  Request  $request
     *
     * @return ReturnedValidator
     */
    public function validateForgotPasswordRequest(Request $request)
    {
        $rules = [
            'email' => 'required|email'
        ];

        $messages = [
            'email.required'         => Lang::get('errors.validation.email.required'),
            'email.email'            => Lang::get('errors.validation.email.invalid'),
        ];

        return Validator::make($request->all(), $rules, $messages);
    }

    /**
     * Send code on email for forgot password
     *
     * @param  User  $user
     */
    public function sendForgotPasswordCode(User $user)
    {
        $randomPass = strtoupper(Str::random(8));
        $user->password = app('hash')->make(md5($randomPass));
        
        $emailService = new EmailService();

        $emailService->sendForgotPasswordCode($user, $randomPass);

        $user->save();
    }

    /**
     * Validate request on forgot change password
     *
     * @param  Request  $request
     *
     * @return ReturnedValidator
     */
    public function validateChangePasswordRequest(Request $request)
    {
        $rules = [
            'oldPassword'          => 'required',
            'newPassword'       => 'required',
        ];

        $messages = [
            'oldPassword.required'    => Lang::get('errors.validation.oldPassword.required'),
            'newPassword.required'       => Lang::get('errors.validation.password.required'),
            //'newPassword.min'            => Lang::get('errors.validation.password.min'),
        ];

        return Validator::make($request->all(), $rules, $messages);
    }

    /**
     * Update user password after reset
     *
     * @param  User  $user
     * @param $password
     */
    public function updatePassword(User $user, $password)
    {
        $user->password  =  app('hash')->make($password);

        $user->save();
    }

    /**
     * Validate activate account
     *
     * @param  Request  $request
     *
     * @return ReturnedValidator
     */
    public function validateActivateAccountOrChangeEmailRequest(Request $request)
    {
        $rules = [
            'email' => 'required|email',
            'code'  => 'required'
        ];

        $messages = [
            'email.required' => Lang::get('errors.validation.email.required'),
            'email.email'    => Lang::get('errors.validation.email.required'),
            'code.required'  => Lang::get('errors.validation.email.required'),
        ];

        return Validator::make($request->all(), $rules, $messages);
    }

    /**
     * Validate request on resend
     *
     * @param  Request  $request
     *
     * @return ReturnedValidator
     */
    public function validateResendActivationCodeRequest(Request $request)
    {
        $rules = [
            'email' => 'required|email'
        ];

        $messages = [
            'email.required' => Lang::get('errors.validation.email.required'),
            'email.email'    => Lang::get('errors.validation.email.required'),
        ];

        return Validator::make($request->all(), $rules, $messages);
    }

    /**
     * Get user from email and password
     *
     * @param  array  $credentials
     *
     * @return User|null
     */
    public function loginUser(array $credentials)
    {
        $builder = self::getUserBuilderForLogin();

        /** @var User|null $user */
        $user = $builder->where('email', $credentials['email'])->where('user_type', User::USER_TYPE_CASUAL)
                        ->first();

        if (!$user) {
            return null;
        }
        
        if (! $token = Auth::attempt($credentials)) {
            return null;
        }

        return ['user' => $user, 'token' => $token];
    }

    /**
     * Get user builder for login
     *
     * @return Builder|BaseModel
     */
    public static function getUserBuilderForLogin()
    {
        return User::query();
    }

    /**
     * Generate returned data on login
     *
     * @param  User  $user
     * @param  bool  $remember
     *
     * @return array
     */
    public function generateLoginData(User $user, $token)
    {
        $data = [
            'user'  => $this->transformerUserData($user),
            'token' => $token
        ];
        if ($user->status == User::$INACTIVE) {
            $user->status = User::$ACTIVE;
            $user->save();
        }
        //$this->saveUserToken($user->id, $days = 14, $token);
        return $data;
    }

    /**
     * Store user token
     *
     * @param $userId
     * @param $days
     *
     * @return string
     */
    public function saveUserToken($userId, $days = 14, $token)
    {
        $userToken = new UserToken();

        $userToken->user_id   = $userId;
        $userToken->access_token     = $token;
        //$userToken->expire_on = Carbon::now()->addDays($days)->format('Y-m-d H:i:s');

        $userToken->save();

        return $userToken->token;
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh() {
        return $this->createNewToken(Auth::refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function createNewToken($token){
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => Auth::factory()->getTTL() * 60,
            'user' => Auth::user()
        ]);
    }
    public function transformerUserData(User $user) {
        $userInt = User::query()->where('id', $user->id)->get();
        $manager = new Manager();
        $formatUser = new Collection($userInt, new UserTransformer());
        $formatUser = $manager->createData($formatUser)->toArray();
        $formatUser = $formatUser['data'][0];
        return $formatUser;
    }
}
