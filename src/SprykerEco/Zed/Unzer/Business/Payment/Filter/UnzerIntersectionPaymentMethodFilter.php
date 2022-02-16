<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Business\Payment\Filter;

use ArrayObject;
use Generated\Shared\Transfer\PaymentMethodsTransfer;
use Generated\Shared\Transfer\PaymentMethodTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\StoreTransfer;
use Generated\Shared\Transfer\UnzerCredentialsCollectionTransfer;
use Generated\Shared\Transfer\UnzerCredentialsConditionsTransfer;
use Generated\Shared\Transfer\UnzerCredentialsCriteriaTransfer;
use Generated\Shared\Transfer\UnzerKeypairTransfer;
use SprykerEco\Shared\Unzer\UnzerConstants;
use SprykerEco\Zed\Unzer\Business\ApiAdapter\UnzerPaymentMethodsAdapterInterface;
use SprykerEco\Zed\Unzer\Business\Checker\QuoteMerchantCheckerInterface;
use SprykerEco\Zed\Unzer\Business\Exception\UnzerException;
use SprykerEco\Zed\Unzer\Business\Reader\UnzerReaderInterface;
use SprykerEco\Zed\Unzer\UnzerConfig;

class UnzerIntersectionPaymentMethodFilter extends AbstractUnzerPaymentMethodFilter implements UnzerPaymentMethodFilterInterface
{
    /**
     * @var \SprykerEco\Zed\Unzer\Business\Reader\UnzerReaderInterface
     */
    protected $unzerReader;

    /**
     * @var \SprykerEco\Zed\Unzer\Business\ApiAdapter\UnzerPaymentMethodsAdapterInterface
     */
    protected $unzerPaymentMethodsAdapter;

    /**
     * @param \SprykerEco\Zed\Unzer\UnzerConfig $unzerConfig
     * @param \SprykerEco\Zed\Unzer\Business\Checker\QuoteMerchantCheckerInterface $quoteMerchantChecker
     * @param \SprykerEco\Zed\Unzer\Business\Reader\UnzerReaderInterface $unzerReader
     * @param \SprykerEco\Zed\Unzer\Business\ApiAdapter\UnzerPaymentMethodsAdapterInterface $unzerPaymentMethodsAdapter
     */
    public function __construct(
        UnzerConfig $unzerConfig,
        QuoteMerchantCheckerInterface $quoteMerchantChecker,
        UnzerReaderInterface $unzerReader,
        UnzerPaymentMethodsAdapterInterface $unzerPaymentMethodsAdapter
    ) {
        parent::__construct($unzerConfig, $quoteMerchantChecker);

        $this->unzerReader = $unzerReader;
        $this->unzerPaymentMethodsAdapter = $unzerPaymentMethodsAdapter;
    }

    /**
     * @param \Generated\Shared\Transfer\PaymentMethodsTransfer $paymentMethodsTransfer
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\PaymentMethodsTransfer
     */
    public function filterPaymentMethods(
        PaymentMethodsTransfer $paymentMethodsTransfer,
        QuoteTransfer $quoteTransfer
    ): PaymentMethodsTransfer {
        $filteredPaymentMethodTransfers = $this->quoteMerchantChecker->hasMultipleMerchants($quoteTransfer)
            ? $this->getMarketplaceFilteredPaymentMethods($paymentMethodsTransfer, $quoteTransfer)
            : $this->getStandardFilteredPaymentMethods($paymentMethodsTransfer, $quoteTransfer);

        return $paymentMethodsTransfer->setMethods($filteredPaymentMethodTransfers);
    }

    /**
     * @param \Generated\Shared\Transfer\PaymentMethodsTransfer $paymentMethodsTransfer
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \ArrayObject<array-key, \Generated\Shared\Transfer\PaymentMethodTransfer>
     */
    protected function getStandardFilteredPaymentMethods(
        PaymentMethodsTransfer $paymentMethodsTransfer,
        QuoteTransfer $quoteTransfer
    ): ArrayObject {
        $unzerKeypairTransfer = $this->getMainMerchantUnzerKeypair($quoteTransfer->getStoreOrFail(), UnzerConstants::UNZER_CONFIG_TYPE_STANDARD);
        $unzerPaymentMethodsTransfer = $this->unzerPaymentMethodsAdapter->getPaymentMethods($unzerKeypairTransfer);

        return $this->filterEnabledPaymentMethods($paymentMethodsTransfer, $unzerPaymentMethodsTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\PaymentMethodsTransfer $paymentMethodsTransfer
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \ArrayObject<array-key, \Generated\Shared\Transfer\PaymentMethodTransfer>
     */
    protected function getMarketplaceFilteredPaymentMethods(
        PaymentMethodsTransfer $paymentMethodsTransfer,
        QuoteTransfer $quoteTransfer
    ): ArrayObject {
        $unzerKeypairTransfer = $this->getMainMerchantUnzerKeypair($quoteTransfer->getStoreOrFail(), UnzerConstants::UNZER_CONFIG_TYPE_MARKETPLACE_MAIN_MERCHANT);
        $unzerPaymentMethodsTransfer = $this->unzerPaymentMethodsAdapter->getPaymentMethods($unzerKeypairTransfer);
        $filteredPaymentMethods = $this->filterEnabledPaymentMethods($paymentMethodsTransfer, $unzerPaymentMethodsTransfer);

        return $this->filterMerchantIntersections($quoteTransfer, $filteredPaymentMethods);
    }

    /**
     * @param \Generated\Shared\Transfer\PaymentMethodsTransfer $paymentMethodsTransfer
     * @param \Generated\Shared\Transfer\PaymentMethodsTransfer $unzerPaymentMethodsTransfer
     *
     * @return \ArrayObject<array-key, \Generated\Shared\Transfer\PaymentMethodTransfer>
     */
    protected function filterEnabledPaymentMethods(
        PaymentMethodsTransfer $paymentMethodsTransfer,
        PaymentMethodsTransfer $unzerPaymentMethodsTransfer
    ): ArrayObject {
        $activePaymentMethods = new ArrayObject();
        $unzerPaymentMethodKeys = $this->getPaymentMethodKeys($unzerPaymentMethodsTransfer);

        foreach ($paymentMethodsTransfer->getMethods() as $paymentMethodTransfer) {
            if (!$this->isUnzerPaymentProvider($paymentMethodTransfer) || in_array($paymentMethodTransfer->getPaymentMethodKey(), $unzerPaymentMethodKeys)) {
                $activePaymentMethods->append($paymentMethodTransfer);
            }
        }

        return $activePaymentMethods;
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \ArrayObject<array-key, \Generated\Shared\Transfer\PaymentMethodTransfer> $filteredPaymentMethods
     *
     * @return \ArrayObject<array-key, \Generated\Shared\Transfer\PaymentMethodTransfer>
     */
    protected function filterMerchantIntersections(QuoteTransfer $quoteTransfer, ArrayObject $filteredPaymentMethods): ArrayObject
    {
        $unzerCredentialsCollectionTransfer = $this->getMerchantUnzerCredentialsCollection($quoteTransfer);

        foreach ($unzerCredentialsCollectionTransfer->getUnzerCredentials() as $unzerCredentialsTransfer) {
            $merchantUnzerKeypair = $unzerCredentialsTransfer->getUnzerKeypairOrFail();
            $merchantPaymentMethodsTransfer = $this->unzerPaymentMethodsAdapter->getPaymentMethods($merchantUnzerKeypair);
            $merchantPaymentMethodKeys = $this->getPaymentMethodKeys($merchantPaymentMethodsTransfer);
            $filteredPaymentMethods = new ArrayObject(
                array_filter((array)$filteredPaymentMethods, function (PaymentMethodTransfer $paymentMethodTransfer) use ($merchantPaymentMethodKeys) {
                        return !$this->isUnzerPaymentProvider($paymentMethodTransfer) || in_array($paymentMethodTransfer->getPaymentMethodKey(), $merchantPaymentMethodKeys);
                }),
            );
        }

        return $filteredPaymentMethods;
    }

    /**
     * @param \Generated\Shared\Transfer\PaymentMethodsTransfer $paymentMethodsTransfer
     *
     * @return array<string|null>
     */
    protected function getPaymentMethodKeys(PaymentMethodsTransfer $paymentMethodsTransfer): array
    {
        return array_map(function (PaymentMethodTransfer $paymentMethodTransfer) {
            return $paymentMethodTransfer->getPaymentMethodKey();
        }, (array)$paymentMethodsTransfer->getMethods());
    }

    /**
     * @param \Generated\Shared\Transfer\StoreTransfer $storeTransfer
     * @param int $type
     *
     * @throws \SprykerEco\Zed\Unzer\Business\Exception\UnzerException
     *
     * @return \Generated\Shared\Transfer\UnzerKeypairTransfer
     */
    protected function getMainMerchantUnzerKeypair(StoreTransfer $storeTransfer, int $type): UnzerKeypairTransfer
    {
        $unzerCredentialsCriteriaTransfer = (new UnzerCredentialsCriteriaTransfer())->setUnzerCredentialsConditions(
            (new UnzerCredentialsConditionsTransfer())
                ->addStoreName($storeTransfer->getNameOrFail())
                ->addType($type),
        );

        $unzerCredentialsTransfer = $this->unzerReader->findUnzerCredentialsByCriteria($unzerCredentialsCriteriaTransfer);

        if ($unzerCredentialsTransfer === null) {
            throw new UnzerException('Unable to request main merchant keypair while credentials unknown.');
        }

        return $unzerCredentialsTransfer->getUnzerKeypairOrFail();
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerCredentialsCollectionTransfer
     */
    protected function getMerchantUnzerCredentialsCollection(QuoteTransfer $quoteTransfer): UnzerCredentialsCollectionTransfer
    {
        $merchantReferences = $this->getUniqueMerchantReferences($quoteTransfer);
        $unzerCredentialsCriteriaTransfer = (new UnzerCredentialsCriteriaTransfer())->setUnzerCredentialsConditions(
            (new UnzerCredentialsConditionsTransfer())
                ->setMerchantReferences($merchantReferences)
                ->addStoreName($quoteTransfer->getStoreOrFail()->getNameOrFail()),
        );

        return $this->unzerReader->getUnzerCredentialsCollectionByCriteria($unzerCredentialsCriteriaTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return array<array-key, string>
     */
    protected function getUniqueMerchantReferences(QuoteTransfer $quoteTransfer): array
    {
        $merchantReferences = [];

        foreach ($quoteTransfer->getItems() as $itemTransfer) {
            if ($itemTransfer->getMerchantReference() !== null) {
                $merchantReferences[] = $itemTransfer->getMerchantReferenceOrFail();
            }
        }

        return array_unique($merchantReferences);
    }
}
