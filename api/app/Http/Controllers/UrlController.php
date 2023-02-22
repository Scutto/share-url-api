<?php

namespace App\Http\Controllers;

use App\Events\NewUrlNotificationEvent;
use App\Events\UrlLikedNotificationEvent;
use App\Models\Url;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Throwable;

class UrlController extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    const AMOUNT_PER_PAGE = 15;

    public function getAllUrls(): JsonResponse
    {
        try {
            $urls = Url::with(['user'])
                ->orderByDesc('created_at')
                ->paginate(self::AMOUNT_PER_PAGE);

            return response()->json([
                'urls' => $urls,
            ]);
        } catch (Throwable $t) {
            return response()->json([
                'title' => 'api.general.error.title',
                'message' => 'api.general.error.message'
            ], 400);
        }
    }

    public function getUrlsFollowed(): JsonResponse
    {
        try {
            /** @var User $loggedUser */
            $loggedUser = Auth::user();
            $followedUserIds = $loggedUser->followedUsers->pluck('id');

            $urls = Url::with(['user'])
                ->whereIn('user_id', $followedUserIds->toArray())
                ->orderByDesc('created_at')
                ->paginate(self::AMOUNT_PER_PAGE);

            return response()->json([
                'urls' => $urls,
            ]);
        } catch (Throwable $t) {
            return response()->json([
                'title' => 'api.general.error.title',
                'message' => 'api.general.error.message'
            ], 400);
        }
    }

    public function getUrlsByCreatorId(string $creatorId): JsonResponse
    {
        try {
            $urls = Url::with(['user'])
                ->where('user_id', $creatorId)
                ->orderByDesc('created_at')
                ->paginate(self::AMOUNT_PER_PAGE);

            return response()->json([
                'urls' => $urls,
            ]);
        } catch (Throwable $t) {
            return response()->json([
                'title' => 'api.general.error.title',
                'message' => 'api.general.error.message'
            ], 400);
        }
    }

    public function getUrlsByTags(string $tags): JsonResponse
    {
        try {
            $urls = DB::table('urls')
                ->selectRaw("urls.*, users.*")
                ->join('urls_tags_pivot', 'urls.id', '=', 'urls_tags_pivot.url_id')
                ->join('tags', 'tags.id', '=', 'urls_tags_pivot.tag_id')
                ->join('users', 'users.id', '=', 'urls.user_id')
                ->whereIn('tags.name', explode(',', $tags))
                ->orderByDesc('urls.created_at')
                ->paginate(self::AMOUNT_PER_PAGE);

            return response()->json([
                'urls' => $urls,
            ]);
        } catch (Throwable $t) {
            return response()->json([
                'title' => 'api.general.error.title',
                'message' => 'api.general.error.message'
            ], 400);
        }
    }

    public function postCreateNewUrl(Request $request): JsonResponse
    {
        try {
            $new_link = $request->get('new_link');

            $newUrl = new Url();
            $newUrl->user_id = Auth::user()->id;
            $newUrl->link = $new_link;
            $newUrl->save();

            NewUrlNotificationEvent::dispatch(Auth::user());

            return response()->json('successful');
        } catch (Throwable $t) {
            return response()->json([
                'title' => 'api.general.error.title',
                'message' => 'api.general.error.message'
            ], 400);
        }
    }

    public function postLikeUrl(Request $request): JsonResponse
    {
        try {
            $urlIdToLike = $request->get('url_id');
            $url = Url::where('id', $urlIdToLike)->firstOrFail();
            $url->likes()->save(Auth::user());

            UrlLikedNotificationEvent::dispatch($url->user);

            return response()->json('successful');
        } catch (Throwable $t) {
            return response()->json([
                'title' => 'api.general.error.title',
                'message' => 'api.general.error.message'
            ], 400);
        }
    }
}
