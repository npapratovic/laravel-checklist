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
