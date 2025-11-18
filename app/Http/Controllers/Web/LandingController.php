<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\HomepageSection;
use App\Models\Banner;
use App\Services\BannerService;
use Illuminate\Support\Carbon;

class LandingController extends Controller
{
    public function index()
    {
        $sections = HomepageSection::where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        $now = Carbon::now();
        $bannersAll = Banner::where('is_active', true)
            ->where(function ($q) use ($now) {
                $q->whereNull('starts_at')->orWhere('starts_at', '<=', $now);
            })
            ->where(function ($q) use ($now) {
                $q->whereNull('ends_at')->orWhere('ends_at', '>=', $now);
            })
            ->orderBy('sort_order')
            ->get();

        $banners = app(BannerService::class)->stickyVariants($bannersAll);

        return view('landing', compact('sections', 'banners'));
    }
}
