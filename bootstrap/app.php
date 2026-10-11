<?php

namespace Symfony\Component\ErrorHandler\ErrorRenderer {
    if (!function_exists('Symfony\Component\ErrorHandler\ErrorRenderer\highlight_file')) {
        function highlight_file(string $filename, bool $return = false): string|bool {
            if (function_exists('\highlight_file')) {
                try {
                    $res = @\highlight_file($filename, $return);
                    if ($res !== false) {
                        return $res;
                    }
                } catch (\Throwable $e) {
                    // Fallback to safe HTML escaping
                }
            }
            $content = @file_get_contents($filename);
            if ($content === false) {
                return false;
            }
            $html = '<pre><code>' . htmlspecialchars($content) . '</code></pre>';
            if ($return) {
                return $html;
            }
            echo $html;
            return true;
        }
    }
}

namespace {
    use Illuminate\Foundation\Application;
    use Illuminate\Foundation\Configuration\Exceptions;
    use Illuminate\Foundation\Configuration\Middleware;
    use Illuminate\Http\Request;

    return Application::configure(basePath: dirname(__DIR__))
        ->withRouting(
            web: __DIR__.'/../routes/web.php',
            api: __DIR__.'/../routes/api.php',
            commands: __DIR__.'/../routes/console.php',
            channels: __DIR__.'/../routes/channels.php',
            health: '/up',
        )
        ->withMiddleware(function (Middleware $middleware): void {
            $middleware->alias([
                'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
                'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
                'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
                'employee_password_reset' => \App\Http\Middleware\EnsureEmployeePasswordReset::class,
                'is_teamleader' => \App\Http\Middleware\IsTeamleader::class,
                'employee_profile_completed' => \App\Http\Middleware\ProfileCompleted::class,
            ]);
            
            // Redirect guests and authenticated users to correct routes depending on guard context
            $middleware->redirectGuestsTo(function (Request $request) {
                if ($request->is('employee/*') || $request->is('employee')) {
                    return route('employee.login');
                }
                return route('login');
            });
            $middleware->redirectUsersTo(function (Request $request) {
                if (\Illuminate\Support\Facades\Auth::guard('employee')->check()) {
                    return route('employee.dashboard');
                }
                return route('admin.dashboard');
            });
        })
        ->withExceptions(function (Exceptions $exceptions): void {
            $exceptions->shouldRenderJsonWhen(
                fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
            );
        })->create();
}
