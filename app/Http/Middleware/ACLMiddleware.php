<?php

namespace App\Http\Middleware;

use App\Repositories\UserRepository;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Symfony\Component\HttpFoundation\Response;

class ACLMiddleware
{
    public function __construct(private UserRepository $userRepository)
    {
    }

    public function handle(Request $request, Closure $next): Response
    {
        $routeName = $request->route()->getName();

        if (!$this->userRepository->hasPermission($request->user(), $routeName)) {
            abort(403, 'Not authorized');
        }

        return $next($request);
    }
}
