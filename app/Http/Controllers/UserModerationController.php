<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class UserModerationController extends Controller
{
    public function lock(Request $request, User $user): RedirectResponse
    {
        abort_unless($request->user()->canLockAccount($user), 403);

        $user->forceFill([
            'locked_at' => now(),
            'locked_by_user_id' => $request->user()->id,
        ])->save();

        return back()->with('status', $user->name.' has been locked.');
    }

    public function unlock(Request $request, User $user): RedirectResponse
    {
        abort_unless($request->user()->canLockAccount($user), 403);

        $user->forceFill([
            'locked_at' => null,
            'locked_by_user_id' => null,
        ])->save();

        return back()->with('status', $user->name.' has been unlocked.');
    }
}
