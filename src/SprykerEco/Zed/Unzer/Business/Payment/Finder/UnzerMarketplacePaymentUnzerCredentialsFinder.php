<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Business\Payment\Finder;

use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\UnzerCredentialsConditionsTransfer;
use Generated\Shared\Transfer\UnzerCredentialsCriteriaTransfer;
use Generated\Shared\Transfer\UnzerCredentialsTransfer;
use Generated\Shared\Transfer\UnzerMarketplacePaymentCredentialsFinderCriteriaTransfer;
use SprykerEco\Shared\Unzer\UnzerConstants;
use SprykerEco\Zed\Unzer\Business\ApiAdapter\UnzerPaymentMethodsAdapterInterface;
use SprykerEco\Zed\Unzer\Business\Exception\UnzerException;
use SprykerEco\Zed\Unzer\Business\Reader\UnzerReaderInterface;

class UnzerMarketplacePaymentUnzerCredentialsFinder implements UnzerMarketplacePaymentUnzerCredentialsFinderInterface
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
     * @param \Generated\Shared\Transfer\UnzerMarketplacePaymentCredentialsFinderCriteriaTransfer $unzerMarketplacePaymentCredentialsFinderCriteriaTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerCredentialsTransfer
     */
    public function findMarketplacePaymentUnzerCredentials(
        UnzerMarketplacePaymentCredentialsFinderCriteriaTransfer $unzerMarketplacePaymentCredentialsFinderCriteriaTransfer
    ): UnzerCredentialsTransfer {
        $quoteTransfer = $unzerMarketplacePaymentCredentialsFinderCriteriaTransfer->getQuoteOrFail();
        $paymentMethodKey = $unzerMarketplacePaymentCredentialsFinderCriteriaTransfer->getPaymentMethodKeyOrFail();

        if ($this->hasMarketplaceMerchantUnzerCredentials($quoteTransfer)) {
            $unzerCredentialsTransfer = $this->getMainMarketplaceUnzerCredentials($quoteTransfer);
            $paymentMethodsTransfer = $this->unzerPaymentMethodsAdapter->getPaymentMethods($unzerCredentialsTransfer->getUnzerKeypairOrFail());

            foreach ($paymentMethodsTransfer->getMethods() as $paymentMethodTransfer) {
                if ($paymentMethodTransfer->getPaymentMethodKey() === $paymentMethodKey) {
                    return $unzerCredentialsTransfer;
                }
            }
        }

        return $quoteTransfer->getUnzerCredentialsOrFail();
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
        );
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @throws \SprykerEco\Zed\Unzer\Business\Exception\UnzerException
     *
     * @return \Generated\Shared\Transfer\UnzerCredentialsTransfer
     */
    protected function getMainMarketplaceUnzerCredentials(QuoteTransfer $quoteTransfer): UnzerCredentialsTransfer
    {
        $unzerCredentialsConditions = (new UnzerCredentialsConditionsTransfer())
            ->addStoreName($quoteTransfer->getStoreOrFail()->getNameOrFail())
            ->setTypes([
                UnzerConstants::UNZER_CREDENTIALS_TYPE_MAIN_MARKETPLACE,
            ]);
        $unzerCredentialsCriteriaTransfer = (new UnzerCredentialsCriteriaTransfer())->setUnzerCredentialsConditions($unzerCredentialsConditions);
        $unzerCredentialsTransfer = $this->unzerReader->findUnzerCredentialsByCriteria($unzerCredentialsCriteriaTransfer);

        if ($unzerCredentialsTransfer === null) {
            throw new UnzerException('Unzer Credentials for current Store configuration not found.');
        }

        return $unzerCredentialsTransfer;
    }
}
