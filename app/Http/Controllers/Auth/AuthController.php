<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Validator;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;
use Illuminate\Support\Facades\View;


use Illuminate\Http\Request;
use App\Models\ApiResult;
use App\Models\Enums\ErrorEnum;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Illuminate\Mail\Message;

class AuthController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Registration & Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users, as well as the
    | authentication of existing users. By default, this controller uses
    | a simple trait to add these behaviors. Why don't you explore it?
    |
    */

//    use AuthenticatesAndRegistersUsers, ThrottlesLogins;

    /**
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    protected $redirectTo = '/auth/login';

    /**
     * Create a new authentication controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest', ['except' => 'getLogout']);
    }

    /**
     * 登录
     *
     * @return \Illuminate\Http\Response
     */
    public function getLogin()
    {
        $params = [
            'page'=>'page-login',
        ];
        return View::make('auth.login',$params);

    }

    public function postLogin(Request $request)
    {
        $patternMap = array(
            'email' => 'required|email|max:255',
            'password' => 'required|min:6',
        );
        $this->validate($request,$patternMap);
        $input = $request->only(array_keys($patternMap));

        if (Auth::attempt($input,true)) {
            $redirectUrl = '';
            $redirectObj  = redirect()->intended();
            if ($redirectObj->getStatusCode() == 200) {
                $redirectUrl = $redirectObj->getTargetUrl();
            }

            $data = [
                'url' => $redirectUrl ? $redirectUrl : '/order/list',
            ];
            return response()->json((new ApiResult(0, ErrorEnum::transform(ErrorEnum::Success), $data))->toJson());
        } else {
            return response()->json((new ApiResult(-1, ErrorEnum::transform(ErrorEnum::UserOrPswError), ''))->toJson());
        }

    }

    /**
     * 注册
     *
     * @return \Illuminate\Http\Response
     */
    public function getRegister()
    {
        $params = [
            'page'=>'page-register',
        ];
        return View::make('auth.register',$params);

    }

    public function postRegister(Request $request)
    {
        $patternMap = array(
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|min:6|confirmed',
            'name' => 'required|max:255',
            'mobile'    => 'sometimes',
            'job_id'    => 'sometimes',
        );
        $this->validate($request,$patternMap);
        $input = $request->only(array_keys($patternMap));

        $input['password'] = bcrypt($input['password']);
        $uid = User::create($input);


        if ($uid) {
            $data = [
                'url' => '/auth/login',
            ];
            return response()->json((new ApiResult(0, ErrorEnum::transform(ErrorEnum::Success), $data))->toJson());
        } else {
            return response()->json((new ApiResult(-1, ErrorEnum::transform(ErrorEnum::Failed), ''))->toJson());
        }
    }


    public function getEmail()
    {
        $params = [
            'page'=>'page-email',
        ];
        return View::make('auth.email',$params);
    }

    /**
     * Send a reset link to the given user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function postEmail(Request $request)
    {
        $this->validate($request, ['email' => 'required|email']);
        $patternMap = array(
            'email' => 'required|email',
        );
        $this->validate($request,$patternMap);
        $input = $request->only(array_keys($patternMap));

        $user = User::where('email',$input['email'])->first();;
        if (!$user) {
            return response()->json((new ApiResult(-1, ErrorEnum::transform(ErrorEnum::UserNotExistError), ''))->toJson());
        }

        $response = Password::sendResetLink($input, function (Message $message) {
            $message->subject('重置密码链接');
        });
        if ($response == Password::RESET_LINK_SENT) {
            $redirectUrl =  '';
            $redirectObj  = redirect()->intended();
            if ($redirectObj->getStatusCode() == 200) {
                $redirectUrl = $redirectObj->getTargetUrl();
            }

            $data = [
                'url' => $redirectUrl ? $redirectUrl : '/auth/login',
            ];

            return response()->json((new ApiResult(0, ErrorEnum::transform(ErrorEnum::Success), $data))->toJson());
        } else {
            return response()->json((new ApiResult(-1, ErrorEnum::transform(ErrorEnum::Failed), ''))->toJson());
        }


    }

    /**
     * Display the password reset view for the given token.
     *
     * @param null $email
     * @param null $token
     * @return mixed
     */
    public function getReset($email = null,$token = null)
    {
        $params = [
            'page'=>'page-reset',
            'email'=>$email,
            'token'=>$token,
        ];
        return View::make('auth.reset',$params);
    }

    /**
     * Reset the given user's password.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function postReset(Request $request)
    {
        $this->validate($request, [
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed|min:6',
        ]);

        $credentials = $request->only(
            'email', 'password', 'password_confirmation', 'token'
        );

        $response = Password::reset($credentials, function ($user, $password) {
            $user->password = bcrypt($password);
            $user->save();
//            Auth::login($user);
        });
        if ($response == Password::PASSWORD_RESET) {
            $data = [
                'url' => '/auth/login',
            ];

            return response()->json((new ApiResult(0, ErrorEnum::transform(ErrorEnum::Success), $data))->toJson());
        } else {
            return response()->json((new ApiResult(-1, ErrorEnum::transform(ErrorEnum::Failed), ''))->toJson());
        }

    }

}
