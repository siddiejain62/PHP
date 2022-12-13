<?php

namespace Vanguard\Http\Controllers\Api\Auth;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Vanguard\Events\User\LoggedIn;
use Vanguard\Events\User\LoggedOut;
use Vanguard\Http\Controllers\Api\ApiController;
use Vanguard\Http\Requests\Auth\ApiLoginRequest;
use Vanguard\User;

/**
 * Class LoginController
 * @package Vanguard\Http\Controllers\Api\Auth
 */
class AuthController extends ApiController
{
    public function __construct()
    {
        $this->middleware('guest')->only('login');
        $this->middleware('auth')->only('logout');
    }

    /**
     * Attempt to log the user in and generate unique
     * JWT token on successful authentication.
     *
     * @param ApiLoginRequest $request
     * @return JsonResponse|Response
     * @throws BindingResolutionException
     * @throws ValidationException
     */
    public function token(ApiLoginRequest $request)
    {
        $input = $request->all();
        $user = $this->findUser($request,$input);
        
        if ($user->isBanned()) {
            return $this->errorUnauthorized(__('Your account is banned by administrators.'));
        }

        Auth::setUser($user);

        event(new LoggedIn);

        return $this->respondWithArray([
            'can_edit_report' => $user->can_edit_report,
            'token' => $user->createToken($request->password)->plainTextToken
        ]);
    }

    /**
     * Find the user instance from the API request.
     *
     * @param ApiLoginRequest $request
     * @return mixed
     * @throws BindingResolutionException
     * @throws ValidationException
     */
    private function findUser(ApiLoginRequest $request, $data)
    {
        $user = User::where($request->getCredentials())->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'username' => [trans('auth.failed')],
            ]);
        }

        $edit_user = $user->update(['can_edit_report'=>$data['can_edit_report']]);
        return $user;
    }

    /**
     * Logout user and invalidate token.
     * @return JsonResponse
     */
    public function logout()
    {
        event(new LoggedOut);

        auth()->user()->currentAccessToken()->delete();

        return $this->respondWithSuccess();
    }
}
