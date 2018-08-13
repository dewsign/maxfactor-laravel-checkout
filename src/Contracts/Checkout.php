<?php

namespace Maxfactor\Checkout\Contracts;

interface Checkout
{
    /**
     * Within the construct, the `content` attribute must be populated with the data from the
     * checkout store / handler. (e.g. remote api or local class)
     */

    /**
     * Must return true or false depending on whether payment should be taken
     *
     * @return boolean
     */
    public function isPaymentRequired();
}
