<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function index(): Response
    {
        $urls = [];

        $base = config('app.url');
        $urls[] = [ 'loc' => $base.'/', 'changefreq' => 'daily', 'priority' => '1.0' ];
        $urls[] = [ 'loc' => $base.'/brand-guidelines', 'changefreq' => 'monthly', 'priority' => '0.3' ];

        foreach (Category::all() as $cat) {
            $urls[] = [ 'loc' => $base.'/categories/'.$cat->slug, 'changefreq' => 'weekly', 'priority' => '0.6' ];
        }
        
        foreach (Product::where('is_active', true)->get() as $p) {
            $urls[] = [ 'loc' => $base.'/products/'.$p->slug, 'changefreq' => 'daily', 'priority' => '0.8' ];
        }

        $xml = view('sitemap.xml', compact('urls'));

        return response($xml, 200)->header('Content-Type', 'application/xml');
    }
}
