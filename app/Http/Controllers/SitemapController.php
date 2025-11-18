<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function index()
    {
        $sitemap = '<?xml version="1.0" encoding="UTF-8"?>';
        $sitemap .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
        
        // Homepage
        $sitemap .= $this->addUrl(url('/'), '1.0', 'daily');
        
        // Login page
        $sitemap .= $this->addUrl(route('login'), '0.8', 'monthly');
        
        // Register page
        $sitemap .= $this->addUrl(route('register'), '0.8', 'monthly');
        
        // Add more URLs as needed
        
        $sitemap .= '</urlset>';
        
        return response($sitemap, 200)
            ->header('Content-Type', 'text/xml');
    }
    
    private function addUrl($loc, $priority = '0.5', $changefreq = 'weekly')
    {
        return '<url>
            <loc>' . htmlspecialchars($loc) . '</loc>
            <lastmod>' . date('Y-m-d') . '</lastmod>
            <changefreq>' . $changefreq . '</changefreq>
            <priority>' . $priority . '</priority>
        </url>';
    }
}
