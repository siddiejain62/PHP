<?php

namespace Vanguard\Http\Requests\Auth;

class ApiLoginRequest extends LoginRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            'can_edit_report' => 'required|integer:0,1',
        ]);
    }

    /**
     * Get the needed authorization credentials from the request.
     *
     * @return array
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function getCredentials()
    {
        $credentials = parent::getCredentials();

        unset($credentials['password']);

        return $credentials;
    }
}
