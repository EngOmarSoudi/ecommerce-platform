<?php

namespace App\View\Components;

use App\Models\Category;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\Component;

class MegaMenu extends Component
{
    public $categories;

    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        $this->categories = Cache::tags(['categories'])->remember('mega-menu.categories', 3600, function () {
            return Category::with('children:id,parent_id,name,slug')
                ->whereNull('parent_id')
                ->orderBy('name')
                ->get();
        });
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.mega-menu');
    }
}
