<?php

namespace Wotz\Seo\Console\Commands;

use Illuminate\Console\Command;
use Wotz\Seo\SeoRoutes;

class ImportSeoRoutes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'seo:import';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync routes with seo middleware to database';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $seoRouteModel = config('filament-seo.models.seo-route');
        $count = SeoRoutes::list()
            ->each(function ($route) use ($seoRouteModel) {
                $seoRouteModel::updateOrCreate(
                    [
                        'route' => $route['as'],
                    ],
                    [
                        'route' => $route['as'],
                        'og_type' => 'website',
                    ]
                );
            })
            ->count();

        if ($count === 1) {
            $this->info('1 seo route has been added/updated');
        } else {
            $this->info(sprintf('%s seo routes has been added/updated', $count));
        }
    }
}
