<?php

namespace Maxfactor\Checkout\Contracts;

interface Checkout
{
    /**
     * This method is where you should either make an api call to a remote checkout store or call
     * the local checkout store to return the checkout array.
     *
     * @return Array
     */
    public function processContent();
}
