<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Business\Payment\Filter;

use Generated\Shared\Transfer\PaymentMethodTransfer;
use SprykerEco\Shared\Unzer\UnzerConfig as SharedUnzerConfig;
use SprykerEco\Zed\Unzer\Business\Checker\QuoteMerchantCheckerInterface;
use SprykerEco\Zed\Unzer\UnzerConfig;

abstract class AbstractUnzerPaymentMethodFilter
{
    /**
     * @var string
     */
    protected const MAIN_SELLER_REFERENCE = 'main';

    /**
     * @var \SprykerEco\Zed\Unzer\UnzerConfig
     */
    protected $unzerConfig;

    /**
     * @var \SprykerEco\Zed\Unzer\Business\Checker\QuoteMerchantCheckerInterface
     */
    protected $quoteMerchantChecker;

    /**
     * @param \SprykerEco\Zed\Unzer\UnzerConfig $unzerConfig
     * @param \SprykerEco\Zed\Unzer\Business\Checker\QuoteMerchantCheckerInterface $quoteMerchantChecker
     */
    public function __construct(UnzerConfig $unzerConfig, QuoteMerchantCheckerInterface $quoteMerchantChecker)
    {
        $this->unzerConfig = $unzerConfig;
        $this->quoteMerchantChecker = $quoteMerchantChecker;
    }

    /**
     * @param \Generated\Shared\Transfer\PaymentMethodTransfer $paymentMethodTransfer
     *
     * @return bool
     */
    protected function isUnzerPaymentProvider(PaymentMethodTransfer $paymentMethodTransfer): bool
    {
        return strpos($paymentMethodTransfer->getPaymentMethodKeyOrFail(), $this->unzerConfig->getPaymentProviderType()) !== false;
    }

    /**
     * @param \Generated\Shared\Transfer\PaymentMethodTransfer $paymentMethodTransfer
     *
     * @return bool
     */
    protected function isMarketplaceUnzerPaymentMethod(PaymentMethodTransfer $paymentMethodTransfer): bool
    {
        return $this->isUnzerPaymentProvider($paymentMethodTransfer) && strpos($paymentMethodTransfer->getPaymentMethodKeyOrFail(), SharedUnzerConfig::PLATFORM_MARKETPLACE) !== false;
    }
}
