<?php

namespace App\Http\Controllers;

use App\Events\NewFollowerNotificationEvent;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Exception;
use Throwable;

class UserController extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function postLogin(Request $request): JsonResponse
    {
        try {
            $credentials = $request->validate([
                'email' => ['required', 'email'],
                'password' => ['required'],
            ]);
     
            if (Auth::attempt($credentials)) {
                $request->session()->regenerate();
     
                return response()->json('successful');
            } else {
                throw new Exception('Failed login');
            }

        } catch (Throwable $t) {
            return response()->json([
                'title' => 'api.general.error.title',
                'message' => 'api.general.error.message'
            ], 400);
        }
    }

    public function postLogout(Request $request): JsonResponse
    {
        try {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
         
            return response()->json('successful');
        } catch (Throwable $t) {
            return response()->json([
                'title' => 'api.general.error.title',
                'message' => 'api.general.error.message'
            ], 400);
        }
    }

    public function postFollowUser(Request $request): JsonResponse
    {
        try {
            $userToFollowId = $request->get('user_id');
            $userToFollow = User::where('id', $userToFollowId)->firstOrFail();

            /** @var User $loggedUser */
            $loggedUser = Auth::user();
            $loggedUser->followedUsers()->save($userToFollow);

            NewFollowerNotificationEvent::dispatch($userToFollow);

            return response()->json('successful');
        } catch (Throwable $t) {
            return response()->json([
                'title' => 'api.general.error.title',
                'message' => 'api.general.error.message'
            ], 400);
        }
    }
}
