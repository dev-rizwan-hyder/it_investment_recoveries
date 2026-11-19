<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $publicDirs = [
            'data_destruction_files',
            'ebay',
            'ebay-product',
            'ebay-product-temp',
            'ebay-product-trash',
            'ecommerce',
            'employee-profile',
            'employee-signature',
            'it_assets_files',
            'pallets',
            'refurbishing',
            'repair',
            'repairentry',
            'sales',
            'universal-waste',
        ];

        foreach ($publicDirs as $dir) {
            $path = public_path($dir);
            if (is_link($path) && !file_exists($path)) {
                @unlink($path);
            }
            if (!file_exists($path)) {
                @mkdir($path, 0755, true);
            }
        }

        $placeholder = public_path('placeholder.png');
        if (is_link($placeholder) && !file_exists($placeholder)) {
            @unlink($placeholder);
        }

        $storageLink = public_path('storage');
        if (is_link($storageLink) && !file_exists($storageLink)) {
            @unlink($storageLink);
        }
    }
}
