<?php

namespace App\View\Components\Layout;

use App\Models\Category;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class NavCategorias extends Component
{
    public $categorias;

    public function __construct()
    {
        $this->categorias = Category::activas()->raiz()->get();
    }

    public function render(): View|Closure|string
    {
        return view('components.layout.nav-categorias');
    }
}
