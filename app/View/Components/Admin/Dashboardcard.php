<?php

namespace App\View\Components\Admin;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Dashboardcard extends Component
{
    public $image;
    public $title;
    public $count;

    /**
     * Create a new component instance.
     */
    public function __construct($image = 'image.png', $title = 'Total Orders', $count = 0)
    {
        $this->image = $image;
        $this->title = $title;
        $this->count = $count;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.admin.dashboardcard');
    }
}