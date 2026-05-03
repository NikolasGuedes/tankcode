<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class ImpersonationController extends Controller
{
    /**
     * Impersonate a user as an admin
     */
    public function impersonate(User $user): RedirectResponse
    {
        $originalUserId = Auth::id();
        $impersonatingUserId = $user->id;

        // Prevent impersonating yourself
        if ($originalUserId === $impersonatingUserId) {
            return redirect()->back()->with('error', 'Você não pode impersonar a si mesmo.');
        }

        // Store the original admin in session
        session(['impersonating' => true, 'original_user_id' => $originalUserId]);

        // Log the user in as the impersonated user
        Auth::loginUsingId($impersonatingUserId, remember: false);

        // Redirect to the user's dashboard
        return redirect()->route($user->homeRouteName());
    }

    /**
     * Stop impersonating and return to admin
     */
    public function stopImpersonation(): RedirectResponse
    {
        $originalUserId = session('original_user_id');

        if ($originalUserId) {
            Auth::loginUsingId($originalUserId, remember: false);
            session()->forget(['impersonating', 'original_user_id']);
        }

        return redirect()->route('admin.users.index');
    }
}
