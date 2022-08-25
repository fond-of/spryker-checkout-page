<?php

namespace FondOfSpryker\Yves\CheckoutPage\Resetter;

use Generated\Shared\Transfer\QuoteTransfer;
use Psr\Log\LoggerInterface;

class OrderReferenceResetter implements OrderReferenceResetterInterface
{
    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\QuoteTransfer
     */
    public function reset(QuoteTransfer $quoteTransfer): QuoteTransfer
    {
        if ($quoteTransfer->getCheckoutConfirmed() === true) {
            $this->logger->notice(
                sprintf(
                    '[ORDER RESET] Order with reference %s has been reseted.',
                    $quoteTransfer->getOrderReference(),
                ),
            );
            $quoteTransfer->setCheckoutConfirmed(false);
            $quoteTransfer->setOrderReference(null);
            $quoteTransfer->setIdSalesOrder(null);

            return $quoteTransfer;
        }

        return $quoteTransfer;
    }
}
