<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Kullanıcı giriş yapmış mı?
        // 2. Giriş yapan kullanıcı admin mi?
        if (Auth::check() && Auth::user()->is_admin) {
            // Evet, admin. İsteğin devam etmesine izin ver.
            return $next($request);
        }
        
        // Hayır, admin değil. Ana sayfaya yönlendir ve bir hata göster.
        return redirect('/')->with('error', 'Bu sayfaya erişim yetkiniz yok.');
    }
}