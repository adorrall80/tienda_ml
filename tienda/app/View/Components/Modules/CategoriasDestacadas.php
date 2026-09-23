<?php

namespace App\View\Components\Modules;

use App\Models\Category;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class CategoriasDestacadas extends Component
{
    public $categorias;

    public function __construct()
    {
        $this->categorias = Category::activas()->raiz()->get();
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.modules.categorias-destacadas');
    }
}
