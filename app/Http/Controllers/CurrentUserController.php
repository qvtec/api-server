<?php

namespace App\Http\Controllers;

use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;
use Illuminate\Contracts\Auth\StatefulGuard;

class CurrentUserController extends ApiController
{
    /**
     * ユーザ情報
     * 
     * @param Request $request
     * @return JsonResponse
     */
    protected function user(Request $request)
    {
        $user = $request->user();
        $data = [
            'name' => $user->name,
            'email' => $user->email,
            'photo' => null,
        ];

        if (!empty($user->profile_photo_path)) {
            $data['photo'] = config("filesystems.disks.public.url") ."/". $user->profile_photo_path;
        }

        return $this->success($data);
    }

    /**
     * Delete the current user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Contracts\Auth\StatefulGuard  $auth
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, StatefulGuard $auth)
    {
        $request->validate([
            'password' => 'required|string|password',
        ]);

        $request->user()->delete();

        $auth->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return $this->success(true);
    }

    /**
     * Delete the current user's profile photo.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function photoDestroy(Request $request)
    {
        $request->user()->deleteProfilePhoto();

        return $this->success(true);
    }
}