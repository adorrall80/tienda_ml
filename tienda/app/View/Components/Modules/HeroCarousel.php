<?php

namespace App\View\Components\Modules;

use App\Models\Banner;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class HeroCarousel extends Component
{
    public $slides;

    public function __construct()
    {
        $this->slides = Banner::activos()->get();
    }

    public function render(): View|Closure|string
    {
        return view('components.modules.hero-carousel');
    }
}
