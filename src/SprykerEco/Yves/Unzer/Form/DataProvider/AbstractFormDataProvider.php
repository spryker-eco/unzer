<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Yves\Unzer\Form\DataProvider;

use Generated\Shared\Transfer\PaymentTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\UnzerPaymentTransfer;
use Spryker\Shared\Kernel\Transfer\AbstractTransfer;
use Spryker\Yves\StepEngine\Dependency\Form\StepEngineFormDataProviderInterface;
use SprykerEco\Yves\Unzer\Dependency\UnzerToQuoteClientInterface;

abstract class AbstractFormDataProvider implements StepEngineFormDataProviderInterface
{
    /**
     * @var string
     */
    protected const PAYMENT_PROVIDER_UNZER = 'Unzer';

    /**
     * @var \SprykerEco\Yves\Unzer\Dependency\UnzerToQuoteClientInterface
     */
    protected $quoteClient;

    /**
     * @param \SprykerEco\Yves\Unzer\Dependency\UnzerToQuoteClientInterface $quoteClient
     */
    public function __construct(UnzerToQuoteClientInterface $quoteClient)
    {
        $this->quoteClient = $quoteClient;
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return array
     */
    public function getOptions(AbstractTransfer $quoteTransfer): array
    {
        return [];
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\QuoteTransfer
     */
    protected function updateQuoteWithPaymentData(QuoteTransfer $quoteTransfer): QuoteTransfer
    {
        $paymentTransfer = $quoteTransfer->getPayment();

        if ($paymentTransfer === null) {
            $paymentTransfer = new PaymentTransfer();
            $paymentTransfer->setPaymentProvider(static::PAYMENT_PROVIDER_UNZER);
            $quoteTransfer->setPayment($paymentTransfer);
        }

        if ($paymentTransfer->getUnzerPayment() === null) {
            $paymentTransfer->setUnzerPayment(new UnzerPaymentTransfer());
        }

        return $quoteTransfer;
    }
}
