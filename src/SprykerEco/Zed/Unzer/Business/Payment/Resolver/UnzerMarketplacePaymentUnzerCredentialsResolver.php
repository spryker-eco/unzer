<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Business\Payment\Resolver;

use Generated\Shared\Transfer\PaymentMethodsTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\UnzerCredentialsConditionsTransfer;
use Generated\Shared\Transfer\UnzerCredentialsCriteriaTransfer;
use Generated\Shared\Transfer\UnzerCredentialsTransfer;
use Generated\Shared\Transfer\UnzerMarketplacePaymentCredentialsResolverCriteriaTransfer;
use SprykerEco\Shared\Unzer\UnzerConstants;
use SprykerEco\Zed\Unzer\Business\ApiAdapter\UnzerPaymentMethodsAdapterInterface;
use SprykerEco\Zed\Unzer\Business\Reader\UnzerReaderInterface;

class UnzerMarketplacePaymentUnzerCredentialsResolver implements UnzerMarketplacePaymentUnzerCredentialsResolverInterface
{
 /**
  * @var \SprykerEco\Zed\Unzer\Business\Reader\UnzerReaderInterface
  */
    protected UnzerReaderInterface $unzerReader;

    /**
     * @var \SprykerEco\Zed\Unzer\Business\ApiAdapter\UnzerPaymentMethodsAdapterInterface
     */
    protected UnzerPaymentMethodsAdapterInterface $unzerPaymentMethodsAdapter;

    /**
     * @param \SprykerEco\Zed\Unzer\Business\Reader\UnzerReaderInterface $unzerReader
     * @param \SprykerEco\Zed\Unzer\Business\ApiAdapter\UnzerPaymentMethodsAdapterInterface $unzerPaymentMethodsAdapter
     */
    public function __construct(UnzerReaderInterface $unzerReader, UnzerPaymentMethodsAdapterInterface $unzerPaymentMethodsAdapter)
    {
        $this->unzerReader = $unzerReader;
        $this->unzerPaymentMethodsAdapter = $unzerPaymentMethodsAdapter;
    }

    /**
     * @param \Generated\Shared\Transfer\UnzerMarketplacePaymentCredentialsResolverCriteriaTransfer $unzerMarketplacePaymentCredentialsResolverCriteriaTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerCredentialsTransfer
     */
    public function resolveMarketplacePaymentUnzerCredentials(
        UnzerMarketplacePaymentCredentialsResolverCriteriaTransfer $unzerMarketplacePaymentCredentialsResolverCriteriaTransfer
    ): UnzerCredentialsTransfer {
        $quoteTransfer = $unzerMarketplacePaymentCredentialsResolverCriteriaTransfer->getQuoteOrFail();
        $paymentMethodKey = $unzerMarketplacePaymentCredentialsResolverCriteriaTransfer->getPaymentMethodKeyOrFail();

        if ($this->hasMarketplaceMerchantUnzerCredentials($quoteTransfer)) {
            $unzerCredentialsTransfer = $this->getMainMarketplaceUnzerCredentials($quoteTransfer);
            $paymentMethodsTransfer = $this->unzerPaymentMethodsAdapter->getPaymentMethods($unzerCredentialsTransfer->getUnzerKeypairOrFail());

            if ($this->isMainMarketplacePaymentMethodAvailable($paymentMethodsTransfer, $paymentMethodKey)) {
                return $unzerCredentialsTransfer;
            }
        }

        return $quoteTransfer->getUnzerCredentialsOrFail();
    }

    /**
     * @param \Generated\Shared\Transfer\PaymentMethodsTransfer $paymentMethodsTransfer
     * @param string $paymentMethodKey
     *
     * @return bool
     */
    protected function isMainMarketplacePaymentMethodAvailable(PaymentMethodsTransfer $paymentMethodsTransfer, string $paymentMethodKey): bool
    {
        foreach ($paymentMethodsTransfer->getMethods() as $paymentMethodTransfer) {
            if ($paymentMethodTransfer->getPaymentMethodKey() === $paymentMethodKey) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return bool
     */
    protected function hasMarketplaceMerchantUnzerCredentials(QuoteTransfer $quoteTransfer): bool
    {
        return !in_array(
            $quoteTransfer->getUnzerCredentialsOrFail()->getTypeOrFail(),
            [
                UnzerConstants::UNZER_CREDENTIALS_TYPE_STANDARD,
                UnzerConstants::UNZER_CREDENTIALS_TYPE_MAIN_MARKETPLACE,
            ],
            true,
        );
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerCredentialsTransfer
     */
    protected function getMainMarketplaceUnzerCredentials(QuoteTransfer $quoteTransfer): UnzerCredentialsTransfer
    {
        $unzerCredentialsConditionsTransfer = (new UnzerCredentialsConditionsTransfer())
            ->addStoreName($quoteTransfer->getStoreOrFail()->getNameOrFail())
            ->setTypes([
                UnzerConstants::UNZER_CREDENTIALS_TYPE_MAIN_MARKETPLACE,
            ]);
        $unzerCredentialsCriteriaTransfer = (new UnzerCredentialsCriteriaTransfer())->setUnzerCredentialsConditions($unzerCredentialsConditionsTransfer);
        $unzerCredentialsTransfer = $this->unzerReader->findUnzerCredentialsByCriteria($unzerCredentialsCriteriaTransfer);

        return $unzerCredentialsTransfer ?? new UnzerCredentialsTransfer();
    }
}
