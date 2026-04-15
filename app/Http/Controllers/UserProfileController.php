<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserProfileController extends Controller
{
    private const SORTS = [
        'date_desc' => ['label' => 'Newest first', 'column' => 'created_at', 'direction' => 'desc'],
        'date_asc' => ['label' => 'Oldest first', 'column' => 'created_at', 'direction' => 'asc'],
        'title_asc' => ['label' => 'Title A-Z', 'column' => 'title', 'direction' => 'asc'],
        'title_desc' => ['label' => 'Title Z-A', 'column' => 'title', 'direction' => 'desc'],
        'likes_desc' => ['label' => 'Most likes', 'column' => 'likes_count', 'direction' => 'desc'],
        'likes_asc' => ['label' => 'Fewest likes', 'column' => 'likes_count', 'direction' => 'asc'],
        'dislikes_desc' => ['label' => 'Most dislikes', 'column' => 'dislikes_count', 'direction' => 'desc'],
        'dislikes_asc' => ['label' => 'Fewest dislikes', 'column' => 'dislikes_count', 'direction' => 'asc'],
        'views_desc' => ['label' => 'Most views', 'column' => 'view_count', 'direction' => 'desc'],
        'views_asc' => ['label' => 'Fewest views', 'column' => 'view_count', 'direction' => 'asc'],
    ];

    public function show(Request $request, User $user): View
    {
        $sort = (string) $request->query('sort', 'date_desc');

        if (! array_key_exists($sort, self::SORTS)) {
            $sort = 'date_desc';
        }

        return view('pages.users.show', [
            'profileUser' => $user,
            'sort' => $sort,
            'sortOptions' => self::SORTS,
            'threads' => $this->threadsForUser($user, $sort),
        ]);
    }

    private function threadsForUser(User $user, string $sort): LengthAwarePaginator
    {
        $sortConfig = self::SORTS[$sort];

        return $user->threads()
            ->select([
                'id',
                'forum_category_id',
                'user_id',
                'title',
                'slug',
                'created_at',
                'view_count',
            ])
            ->with([
                'category:id,name,slug',
            ])
            ->withCount([
                'replies',
                'reactions as likes_count' => fn (Builder $query) => $query->where('type', 'like'),
                'reactions as dislikes_count' => fn (Builder $query) => $query->where('type', 'dislike'),
            ])
            ->orderBy($sortConfig['column'], $sortConfig['direction'])
            ->orderBy('id', 'desc')
            ->paginate(15)
            ->withQueryString();
    }
}
