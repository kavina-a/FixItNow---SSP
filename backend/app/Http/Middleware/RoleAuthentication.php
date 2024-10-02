<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Enums\UserRole;

class RoleAuthentication
{
    /**
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */
    public function handle(
        Request $request,
        Closure $next,
        string  ...$args
    ): Response
    {
        // Check if any roles are passed to the middleware
        if (empty($args)) {
            return response()->json([
                'message' => 'Access denied: No role provided for this route.'
            ], 403);
        }

        // Check if a user is authenticated
        if (!$request->user()) {
            return response()->json([
                'error' => 'Unauthorized. You need to log in to access this resource.'
            ], 401);
        }

        $userRole = $request->user()->role->value ?? (string) $request->user()->role;  // Get the enum value or string

        // Loop through the provided roles and check if the user's role matches any of them
        foreach ($args as $role) {
            $expectedRole = UserRole::from($role)->value;
            if ($userRole === $expectedRole) {
                return $next($request);  // Proceed if the user's role matches
            }
        }

        // If no roles match, deny access and return debug info
        return response()->json([
            'message' => 'Access denied: You do not have the necessary permissions to access this resource.',
            'debug' => [
                'user_role' => $userRole,  // The role of the authenticated user
                'expected_roles' => $args,  // The roles passed to the middleware
            ]
        ], 403);
    }
}
