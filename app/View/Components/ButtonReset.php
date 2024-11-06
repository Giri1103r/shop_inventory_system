<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ButtonReset extends Component
{
    /**
     * Create a new component instance.
     */

     public $dataId;
     public $class;
     public $href;

    public function __construct($dataId = '', $class = '' , $href = '')
    {

        $this->dataId = $dataId;
        $this->class = $class;
        $this->href = $href;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.button-reset');
    }
}
