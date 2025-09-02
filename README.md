# laravel-checklist

laravel new project-name, cd project-name, composer require laravel/breeze --dev, php artisan breeze:install react, npm install, npm run dev


Checklist on starting a new Laravel project

- [ ] start new laravel app in 2 commands: `1) laravel new +app_name` `2) composer run dev`
- [ ] Use Laravel Pint for code  formatting tool for PHP / code structure / style https://www.youtube.com/watch?v=s4PonV1wLRQ  https://youtu.be/JUDQuymlsh0?si=e3BK0DKCPHBQQrUO&t=1140  
- [ ] Use Larastan for code analysis https://github.com/larastan/larastan
- [ ] Use Log viewer to simplify vieweing logs
- [ ] Use flareapp to quickly receive notification if error happens
- [ ] Use postmark for transactional emails
- [ ] Use PEST for Laravel tests pestphp/pest
- [ ] use phpstan/phpstan
- [ ] avoid database cascade deletes and default values https://www.youtube.com/watch?v=OZGbySrPhX0  focus on maintaining data integrity at the database level and handling business logic within the application layer. In other words, data doesn’t get deleted unless it’s explicitly defined in the application domain. 
- [ ] use rectorphp if you use legacy code rector/rector https://www.youtube.com/watch?v=15tsiv6AvnE 
- [ ] See Nuno Maduro essentials checklist: https://github.com/nunomaduro/essentials
- [ ] https://www.youtube.com/live/DR1o-u2AFPA?si=0wslGiW6VcDU0Yf3&t=1662  <-- see here for defaults also LIVE 35
- [ ] https://www.youtube.com/watch?v=Ku7sGWUKEao Fresh App - Migrations + Models + First Tests Using Factories LIVE 36
- [ ] https://www.youtube.com/watch?v=-r1UDrQJJdQ Laravel + API + Actions + React + TypeScript LIVE 37
- [ ] https://www.youtube.com/watch?v=vDcCmP0q8Kw Chat, Merging PRs on new SaaS, testing LIVE 38
- [ ] https://www.youtube.com/watch?v=uMQFMw4wfu0 Finishing models + coding a queue job and testing it! LIVE 39
- [ ] https://github.com/aamimi/offerly-backend vidi tetsove, modele, controllere, invokable klase, composer.json ...

Append db query info in JSON response: 

File: AppServiceProvider: 
 
<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register()
    {
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // zend.exception_ignore_args: This PHP configuration directive controls whether arguments to exceptions are included in the stack trace. Setting it to 0 ensures that arguments are included, which can be helpful for debugging
        ini_set('zend.exception_ignore_args', 0);

        // This simple listener will log all database queries, and log a critical error if the query takes longer than 100ms
        DB::listen(function ($query) {
            $slowQueryThreshold = 100; // Threshold in milliseconds
            if ($query->time > $slowQueryThreshold) {
                Log::critical('Slow Database Query Detected', [
                    'query' => $query->sql,
                    'bindings' => $query->bindings,
                    'time' => $query->time,
                ]);
            }
        });

        // see details, comment if not needed: https://laravel.com/docs/10.x/eloquent#preventing-lazy-loading
        if (!$this->app->isProduction()) {
            // enable prevention
            Model::preventLazyLoading();

            Model::handleLazyLoadingViolationUsing(function (Model $model, string $relation) {
                $class = $model::class;

                // make info (or critical, depending on preferences) log that lazy loading was attempted
                Log::info('Lazy Loading Violation', [
                    'model' => $class, // The name of the model that caused the violation (e.g., 'App\Models\Post')
                    'relation' => $relation, // The name of the relationship that was lazy-loaded (e.g., 'user')
                    'trace'    => collect(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 10))
                    ->map(fn ($frame) => ($frame['file'] ?? '') . ':' . ($frame['line'] ?? ''))
                    ->filter()
                    ->take(55)
                    ->values()
                    ->all(),
                    // The debug_backtrace() function creates an array of the call stack.
                    // The subsequent collection methods then format this trace to show you
                    // exactly which file and line number the lazy loading happened on.
                ]);
            });
        }
    }
}


bootstrap/app.php:

<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->api(prepend: [
            \App\Http\Middleware\AppendQueryCount::class, // this middleware appends the query count to the response in local environment
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();


File \App\Http\Middleware\AppendQueryCount.php

<?php

namespace App\Http\Middleware;

use App\Traits\LazyLoadingFlag;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;

class AppendQueryCount
{
    protected int $queryCount = 0;
    protected array $queries = [];

    public function handle($request, Closure $next)
    {
        if (!App::environment('local')) {
            return $next($request);
        }

        // reset lazy loading flag at start of request
        LazyLoadingFlag::reset();

        DB::listen(function ($query) {
            $this->queryCount++;
            $this->queries[] = [
                'sql' => $query->sql,
                'bindings' => $query->bindings,
                'time_ms' => $query->time,
            ];
        });

        $response = $next($request);

        // Only modify JSON responses
        if ($response instanceof JsonResponse) {
            $data = $response->getData(true);

            // force debug keys to top by rebuilding array
            $debug_data = [
                // Add a flag to indicate if lazy loading was used
                // AppServiceProvider is where lazy loading violations are logged
                'lazy_load_noticed' => LazyLoadingFlag::wasUsed(),
                'db_query_count' => $this->queryCount,
            ];

            $debug_queries = [
                'debug_queries' => $this->queries,
            ];

            $data = $debug_data + $data + $debug_queries;

            $response->setData($data);
        }

        return $response;
    }
}

File App\Traits\LazyLoadingFlag.php

<?php

namespace App\Traits;

class LazyLoadingFlag
{
    protected static bool $used = false;

    public static function markUsed(): void
    {
        static::$used = true;
    }

    public static function wasUsed(): bool
    {
        return static::$used;
    }

    public static function reset(): void
    {
        static::$used = false;
    }
}
