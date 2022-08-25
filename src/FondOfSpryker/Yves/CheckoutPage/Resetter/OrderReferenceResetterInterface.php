<?php

namespace FondOfSpryker\Yves\CheckoutPage\Resetter;

use Generated\Shared\Transfer\QuoteTransfer;

interface OrderReferenceResetterInterface
{
    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\QuoteTransfer
     */
    public function reset(QuoteTransfer $quoteTransfer): QuoteTransfer;
}
