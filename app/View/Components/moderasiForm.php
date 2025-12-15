<?php

namespace App\View\Components;

use Illuminate\View\Component;

class ModerasiForm extends Component
{
    public $targetType;
    public $targetId;

    // BUAT PARAMETER OPTIONAL
    public function __construct(string $targetType = 'artwork', ?int $targetId = null)
    {
        $this->targetType = $targetType;
        $this->targetId   = $targetId;
    }

    public function render()
    {
        return view('components.moderasi-form');
    }
}
