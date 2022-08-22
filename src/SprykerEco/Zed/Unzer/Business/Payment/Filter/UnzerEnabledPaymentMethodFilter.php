<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Business\Payment\Filter;

use ArrayObject;
use Generated\Shared\Transfer\PaymentMethodsTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\StoreTransfer;
use Generated\Shared\Transfer\UnzerCredentialsConditionsTransfer;
use Generated\Shared\Transfer\UnzerCredentialsCriteriaTransfer;
use Generated\Shared\Transfer\UnzerKeypairTransfer;
use SprykerEco\Shared\Unzer\UnzerConfig as SharedUnzerConfig;
use SprykerEco\Shared\Unzer\UnzerConstants;
use SprykerEco\Zed\Unzer\Business\ApiAdapter\UnzerPaymentMethodsAdapterInterface;
use SprykerEco\Zed\Unzer\Business\Exception\UnzerException;
use SprykerEco\Zed\Unzer\Business\Reader\UnzerReaderInterface;
use SprykerEco\Zed\Unzer\UnzerConfig;

class UnzerEnabledPaymentMethodFilter extends AbstractUnzerPaymentMethodFilter implements UnzerPaymentMethodFilterInterface
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
     * @param \SprykerEco\Zed\Unzer\UnzerConfig $unzerConfig
     * @param \SprykerEco\Zed\Unzer\Business\Reader\UnzerReaderInterface $unzerReader
     * @param \SprykerEco\Zed\Unzer\Business\ApiAdapter\UnzerPaymentMethodsAdapterInterface $unzerPaymentMethodsAdapter
     */
    public function __construct(
        UnzerConfig $unzerConfig,
        UnzerReaderInterface $unzerReader,
        UnzerPaymentMethodsAdapterInterface $unzerPaymentMethodsAdapter
    ) {
        parent::__construct($unzerConfig);

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
        if (!$this->hasUnzerPaymentMethod($paymentMethodsTransfer)) {
            return $paymentMethodsTransfer;
        }

        if ($quoteTransfer->getUnzerCredentials() && $quoteTransfer->getUnzerCredentialsOrFail()->getType() === UnzerConstants::UNZER_CREDENTIALS_TYPE_STANDARD) {
            return $this->getStandardUnzerPaymentMethods($paymentMethodsTransfer, $quoteTransfer);
        }

        if ($this->hasMultipleMerchants($quoteTransfer)) {
            return $this->getMarketplaceUnzerPaymentMethods($paymentMethodsTransfer, $quoteTransfer);
        }

        return $this->getMerchantMarketplaceUnzerPaymentMethods($paymentMethodsTransfer, $quoteTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\PaymentMethodsTransfer $paymentMethodsTransfer
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\PaymentMethodsTransfer
     */
    protected function getStandardUnzerPaymentMethods(
        PaymentMethodsTransfer $paymentMethodsTransfer,
        QuoteTransfer $quoteTransfer
    ): PaymentMethodsTransfer {
        $standardUnzerKeypairTransfer = $this->getStandardUnzerKeypair($quoteTransfer);
        $standardPaymentMethods = new ArrayObject();

        foreach ($paymentMethodsTransfer->getMethods() as $paymentMethodTransfer) {
            if (!$this->isUnzerPaymentProvider($paymentMethodTransfer) xor !$this->isMarketplaceUnzerPaymentMethod($paymentMethodTransfer)) {
                $standardPaymentMethods->append($paymentMethodTransfer);
            }
        }

        return $this->filterEnabledPaymentMethods(
            $paymentMethodsTransfer->setMethods($standardPaymentMethods),
            $this->unzerPaymentMethodsAdapter->getPaymentMethods($standardUnzerKeypairTransfer),
        );
    }

    /**
     * @param \Generated\Shared\Transfer\PaymentMethodsTransfer $paymentMethodsTransfer
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\PaymentMethodsTransfer
     */
    protected function getMarketplaceUnzerPaymentMethods(
        PaymentMethodsTransfer $paymentMethodsTransfer,
        QuoteTransfer $quoteTransfer
    ): PaymentMethodsTransfer {
        $mainMarketplaceUnzerKeypairTransfer = $this->getMainMarketplaceUnzerKeypair($quoteTransfer);
        $marketplacePaymentMethods = new ArrayObject();

        foreach ($paymentMethodsTransfer->getMethods() as $paymentMethodTransfer) {
            if (!$this->isUnzerPaymentProvider($paymentMethodTransfer) xor $this->isMarketplaceUnzerPaymentMethod($paymentMethodTransfer)) {
                $marketplacePaymentMethods->append($paymentMethodTransfer);
            }
        }

        return $this->filterEnabledPaymentMethods(
            $paymentMethodsTransfer->setMethods($marketplacePaymentMethods),
            $this->unzerPaymentMethodsAdapter->getPaymentMethods($mainMarketplaceUnzerKeypairTransfer),
        );
    }

    /**
     * @param \Generated\Shared\Transfer\PaymentMethodsTransfer $paymentMethodsTransfer
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\PaymentMethodsTransfer
     */
    protected function getMerchantMarketplaceUnzerPaymentMethods(
        PaymentMethodsTransfer $paymentMethodsTransfer,
        QuoteTransfer $quoteTransfer
    ): PaymentMethodsTransfer {
        $merchantMarketplaceUnzerKeypairTransfer = $quoteTransfer->getUnzerCredentialsOrFail()->getUnzerKeypair();
        $merchantMarketplacePaymentMethods = new ArrayObject();

        foreach ($paymentMethodsTransfer->getMethods() as $paymentMethodTransfer) {
            if (!$this->isUnzerPaymentProvider($paymentMethodTransfer) xor !$this->isMarketplaceUnzerPaymentMethod($paymentMethodTransfer)) {
                $merchantMarketplacePaymentMethods->append($paymentMethodTransfer);
            }
        }

        $merchantMarketplacePaymentMethods = $this->filterEnabledPaymentMethods(
            $merchantMarketplacePaymentMethods,
            $this->unzerPaymentMethodsAdapter->getPaymentMethods($merchantMarketplaceUnzerKeypairTransfer),
        );

        return $this->appendMarketplacePaymentMethods(
            $merchantMarketplacePaymentMethods,
            $this->getMarketplaceUnzerPaymentMethods($paymentMethodsTransfer, $quoteTransfer),
        );
    }

    /**
     * @param \Generated\Shared\Transfer\PaymentMethodsTransfer $merchantMarketplacePaymentMethods
     * @param \Generated\Shared\Transfer\PaymentMethodsTransfer $marketplacePaymentMethods
     *
     * @return \Generated\Shared\Transfer\PaymentMethodsTransfer
     */
    protected function appendMarketplacePaymentMethods(
        PaymentMethodsTransfer $merchantMarketplacePaymentMethods,
        PaymentMethodsTransfer $marketplacePaymentMethods
    ): PaymentMethodsTransfer {
        $paymentMethodKeys = $this->getPaymentMethodKeys($marketplacePaymentMethods);

        foreach ($paymentMethodKeys as &$paymentMethodKey) {
            $paymentMethodKey = str_replace(SharedUnzerConfig::PLATFORM_MARKETPLACE, '', $paymentMethodKey);
        }

        foreach ($merchantMarketplacePaymentMethods->getMethods() as $paymentMethodTransfer) {
            if ($this->isUnzerPaymentProvider($paymentMethodTransfer) && !in_array($paymentMethodTransfer->getPaymentMethodKey(), $paymentMethodKeys, true)) {
                $marketplacePaymentMethods->getMethods()->append($paymentMethodTransfer);
            }
        }

        return $marketplacePaymentMethods;
    }

    /**
     * @param \Generated\Shared\Transfer\PaymentMethodsTransfer $paymentMethodsTransfer
     * @param \Generated\Shared\Transfer\PaymentMethodsTransfer $unzerPaymentMethodsTransfer
     *
     * @return \Generated\Shared\Transfer\PaymentMethodsTransfer
     */
    protected function filterEnabledPaymentMethods(
        PaymentMethodsTransfer $paymentMethodsTransfer,
        PaymentMethodsTransfer $unzerPaymentMethodsTransfer
    ): PaymentMethodsTransfer {
        $activePaymentMethods = new ArrayObject();
        $unzerPaymentMethodKeys = $this->getPaymentMethodKeys($unzerPaymentMethodsTransfer);

        foreach ($paymentMethodsTransfer->getMethods() as $paymentMethodTransfer) {
            if (!$this->isUnzerPaymentProvider($paymentMethodTransfer) xor in_array($paymentMethodTransfer->getPaymentMethodKey(), $unzerPaymentMethodKeys, true)) {
                $activePaymentMethods->append($paymentMethodTransfer);
            }
        }

        return $paymentMethodsTransfer->setMethods($activePaymentMethods);
    }

    /**
     * @param \Generated\Shared\Transfer\PaymentMethodsTransfer $paymentMethodsTransfer
     *
     * @return array<string|null>
     */
    protected function getPaymentMethodKeys(PaymentMethodsTransfer $paymentMethodsTransfer): array
    {
        $paymentMethodKeys = [];

        foreach ($paymentMethodsTransfer->getMethods() as $paymentMethodsTransfer) {
            $paymentMethodKeys[] = $paymentMethodTransfer->getPaymentMethodKey();
        }

        return $paymentMethodKeys;
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerKeypairTransfer
     */
    protected function getStandardUnzerKeypair(QuoteTransfer $quoteTransfer): UnzerKeypairTransfer
    {
        return $this->getUnzerKeypair(
            $quoteTransfer->getStoreOrFail(),
            [
                UnzerConstants::UNZER_CREDENTIALS_TYPE_STANDARD,
                UnzerConstants::UNZER_CREDENTIALS_TYPE_MARKETPLACE_MAIN_MERCHANT,
            ],
        );
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerKeypairTransfer
     */
    protected function getMainMarketplaceUnzerKeypair(QuoteTransfer $quoteTransfer): UnzerKeypairTransfer
    {
        return $this->getUnzerKeypair(
            $quoteTransfer->getStoreOrFail(),
            [
                UnzerConstants::UNZER_CREDENTIALS_TYPE_MAIN_MARKETPLACE,
            ],
        );
    }

    /**
     * @param \Generated\Shared\Transfer\StoreTransfer $storeTransfer
     * @param array<int> $unzerCredentialsTypes
     *
     * @throws \SprykerEco\Zed\Unzer\Business\Exception\UnzerException
     *
     * @return \Generated\Shared\Transfer\UnzerKeypairTransfer
     */
    protected function getUnzerKeypair(StoreTransfer $storeTransfer, array $unzerCredentialsTypes): UnzerKeypairTransfer
    {
        $unzerCredentialsCriteriaTransfer = (new UnzerCredentialsCriteriaTransfer())->setUnzerCredentialsConditions(
            (new UnzerCredentialsConditionsTransfer())
                ->addStoreName($storeTransfer->getNameOrFail())
                ->setTypes($unzerCredentialsTypes),
        );

        $unzerCredentialsTransfer = $this->unzerReader->findUnzerCredentialsByCriteria($unzerCredentialsCriteriaTransfer);
        if ($unzerCredentialsTransfer === null) {
            throw new UnzerException('Unzer Credentials for current Store configuration not found.');
        }

        return $unzerCredentialsTransfer->getUnzerKeypairOrFail();
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
                $merchantReferences[$itemTransfer->getMerchantReferenceOrFail()] = $itemTransfer->getMerchantReferenceOrFail();
            }
        }

        return $merchantReferences;
    }
}
