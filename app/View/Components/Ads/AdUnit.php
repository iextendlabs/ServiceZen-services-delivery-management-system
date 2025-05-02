<?php

namespace App\View\Components\Ads;

use Illuminate\View\Component;

class AdUnit extends Component
{
    public $slot;
    public $format;
    public $style;
    
    public function __construct($slot = null, $format = 'auto', $style = 'display:block')
    {
        $this->slot = $slot;
        $this->format = $format;
        $this->style = $style;
    }

    public function render()
    {
        return view('site.ads.ad-unit');
    }
}