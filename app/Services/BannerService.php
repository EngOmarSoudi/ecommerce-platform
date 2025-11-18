<?php

namespace App\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Session;

class BannerService
{
    /**
     * Select sticky variants per banner title group.
     * If multiple variants exist for a title, persist choice in session.
     */
    public function stickyVariants(Collection $banners): array
    {
        $grouped = $banners->groupBy(fn ($b) => $b->title);
        $selected = [];

        foreach ($grouped as $title => $group) {
            if ($group->count() === 1) {
                $selected[] = $group->first();
                continue;
            }

            $sessionKey = 'banner_variant_'.md5($title);
            $chosenVariant = Session::get($sessionKey);

            if ($chosenVariant) {
                $match = $group->firstWhere('variant', $chosenVariant);
                if ($match) {
                    $selected[] = $match;
                    continue;
                }
            }

            // Pick random and store for stickiness
            $pick = $group->random();
            Session::put($sessionKey, $pick->variant);
            $selected[] = $pick;
        }

        return $selected;
    }
}
