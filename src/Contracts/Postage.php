<?php

namespace Maxfactor\Checkout\Contracts;

interface Postage
{
    /**
     * Format the output array to be compatible with requirements for Maxfactor Cart
     *
     * @param Array $postage
     * @return array
     */
    public function toMaxfactorCart($postage);
}
