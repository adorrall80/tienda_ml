<?php

namespace App\View\Components\Modules;

use App\Models\Product;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ProductosScroll extends Component
{
    public $productos;
    public string $verTodosUrl;

    public function __construct(
        public string $titulo = 'Productos',
        public string $tag    = '',
        public int    $limite = 7
    ) {
        $query = Product::activos()->with('tags');
        if ($this->tag) {
            $query->conTag($this->tag);
        }
        $this->productos   = $query->latest()->take($this->limite)->get();
        $this->verTodosUrl = route('productos.index');
    }

    public function render(): View|Closure|string
    {
        return view('components.modules.productos-scroll');
    }
}
