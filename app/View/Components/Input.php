<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Input extends Component
{
    public $type;
    public $name;
    public $id;
    public $value;

    public function __construct($type = 'text', $name = null, $id = null, $value = null)
    {
        $this->type = $type;
        $this->name = $name;
        $this->id = $id;
        $this->value = $value;
    }

    public function render()
    {
        return view('components.input');
    }
}
