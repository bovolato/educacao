<?php

namespace App\View\Components;

use Illuminate\View\Component;

class SigemLayout extends Component
{
    public function __construct(public ?string $title = null)
    {
    }

    public function render()
    {
        return view('layouts.sigem');
    }
}
