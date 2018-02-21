<?php

namespace Maxfactor\Checkout\Traits;

use Illuminate\Support\Carbon;
use Maxfactor\Checkout\Handlers\Postage;

trait HandlesPostage
{
    /**
     * Format the output array to be compatible with requirements for Maxfactor Cart
     *
     * @param Array $postage
     * @return array
     */
    public function toMaxfactorCart($postage)
    {
        return $postage->toArray();
    }
}
