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
