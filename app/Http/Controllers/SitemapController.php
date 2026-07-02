<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Support\Facades\Cache;

class SitemapController extends Controller
{
    /**
     * Main Sitemap Index (sitemap.xml)
     */
    public function index()
    {
        // Child sitemap URLs (each one points to a separate sitemap file)
        $sitemaps = [
            url('/sitemap-pages.xml'),
            url('/sitemap-products.xml'),
        ];

        $content = view('sitemap-index', compact('sitemaps'));

        return response($content, 200)
            ->header('Content-Type', 'application/xml');
    }

    /**
     * Static Pages Sitemap (sitemap-pages.xml)
     */
    public function pages()
    {
        $urls = Cache::remember('sitemap_pages', now()->addDays(2), function () {
            return [
                [
                    'loc' => url('/'),
                    'lastmod' => now()->toAtomString(),
                    'changefreq' => 'daily',
                    'priority' => '1.0'
                ],
                [
                    'loc' => url('/about'),
                    'lastmod' => now()->toAtomString(),
                    'changefreq' => 'monthly',
                    'priority' => '0.8'
                ],
                [
                    'loc' => url('/contact'),
                    'lastmod' => now()->toAtomString(),
                    'changefreq' => 'monthly',
                    'priority' => '0.7'
                ],
            ];
        });

        $content = view('sitemap', compact('urls'));

        return response($content, 200)
            ->header('Content-Type', 'application/xml');
    }

    /**
     * Products Sitemap (sitemap-products.xml)
     */
    public function products()
    {
        $urls = Cache::remember('sitemap_products', now()->addDays(2), function () {
            return Product::where('name', '!=', 'Wallet')
                ->get()
                ->map(function ($product) {
                    return [
                        'loc' => url('/product/' . $product->slug),
                        'lastmod' => optional($product->updated_at)->toAtomString(),
                        'changefreq' => 'weekly',
                        'priority' => '0.8'
                    ];
                })
                ->toArray();
        });

        $content = view('sitemap', compact('urls'));

        return response($content, 200)
            ->header('Content-Type', 'application/xml');
    }
}
