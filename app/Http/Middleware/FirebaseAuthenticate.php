<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class FirebaseAuthenticate
{
    public function handle(Request $request, Closure $next)
    {
        if (!session()->has('firebase_uid')) {
            $redirect = $request->fullUrl();
            
            // Check if request is AJAX
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'authenticated' => false,
                    'message' => 'Authentication required',
                    'redirect' => route('login', ['redirect' => $redirect])
                ], 401);
            }
            
            // For regular requests, show the auth modal via JavaScript
            // We'll set a session flag that will be checked in the layout to show the modal
            session()->flash('show_auth_modal', true);
            session()->flash('auth_redirect', $redirect);
            
            // Return a response that will show the modal via JavaScript
            // instead of redirecting to the login page
            if ($request->header('X-Requested-With') == 'XMLHttpRequest') {
                return response()->json([
                    'authenticated' => false,
                    'message' => 'Authentication required'
                ], 401);
            }
            
            // Return to the same page where the modal will be shown
            return back();
        }

        return $next($request);
    }
}


