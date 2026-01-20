<?php

namespace Wotz\Seo\Http\Middleware;

use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Wotz\Seo\Models\SeoRoute;
use Wotz\Seo\SeoRoutes;

class SeoMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $route = $request->route();

        if (! $route) {
            return $next($request);
        }

        $entity = collect($route->parameters)
            ->filter(fn ($entity) => $entity instanceof Model)
            ->last();

        $routeName = $route->getName();
        if (isset($route->wheres['translatable_prefix'])) {
            $routeName = Str::after($routeName, $route->wheres['translatable_prefix'] . '.');
        }

        $seoRoute = SeoRoute::where('route', $routeName)
            ->where('online->' . app()->getLocale(), true)
            ->first();

        if ($seoRoute) {
            SeoRoutes::build($seoRoute, $entity);
        }

        if ($entity && method_exists($entity, 'withSeoFields')) {
            $entity->withSeoFields();
        }

        return $next($request);
    }
}
