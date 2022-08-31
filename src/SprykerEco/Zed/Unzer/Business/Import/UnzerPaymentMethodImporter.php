<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Business\Import;

use Generated\Shared\Transfer\PaymentMethodsTransfer;
use Generated\Shared\Transfer\UnzerCredentialsCollectionTransfer;
use Generated\Shared\Transfer\UnzerCredentialsConditionsTransfer;
use Generated\Shared\Transfer\UnzerCredentialsCriteriaTransfer;
use Generated\Shared\Transfer\UnzerKeypairTransfer;
use SprykerEco\Zed\Unzer\Business\ApiAdapter\UnzerPaymentMethodsAdapterInterface;
use SprykerEco\Zed\Unzer\Business\Import\Adapter\PaymentImportAdapterInterface;
use SprykerEco\Zed\Unzer\Business\Import\Filter\UnzerPaymentMethodImportFilterInterface;
use SprykerEco\Zed\Unzer\Business\Reader\UnzerReaderInterface;
use SprykerEco\Zed\Unzer\UnzerConfig;

class UnzerPaymentMethodImporter implements UnzerPaymentMethodImporterInterface
{
    /**
     * @var \SprykerEco\Zed\Unzer\UnzerConfig
     */
    protected UnzerConfig $unzerConfig;

    /**
     * @var \SprykerEco\Zed\Unzer\Business\Import\Filter\UnzerPaymentMethodImportFilterInterface
     */
    protected UnzerPaymentMethodImportFilterInterface $unzerPaymentMethodImportFilter;

    /**
     * @var \SprykerEco\Zed\Unzer\Business\ApiAdapter\UnzerPaymentMethodsAdapterInterface
     */
    protected UnzerPaymentMethodsAdapterInterface $unzerPaymentMethodsAdapter;

    /**
     * @var \SprykerEco\Zed\Unzer\Business\Import\Adapter\PaymentImportAdapterInterface
     */
    protected PaymentImportAdapterInterface $paymentImportAdapter;

    /**
     * @var \SprykerEco\Zed\Unzer\Business\Reader\UnzerReaderInterface
     */
    protected UnzerReaderInterface $unzerReader;

    /**
     * @param \SprykerEco\Zed\Unzer\UnzerConfig $unzerConfig
     * @param \SprykerEco\Zed\Unzer\Business\Import\Filter\UnzerPaymentMethodImportFilterInterface $unzerPaymentMethodImportFilter
     * @param \SprykerEco\Zed\Unzer\Business\Import\Adapter\PaymentImportAdapterInterface $paymentImportAdapter
     * @param \SprykerEco\Zed\Unzer\Business\ApiAdapter\UnzerPaymentMethodsAdapterInterface $unzerPaymentMethodsAdapter
     * @param \SprykerEco\Zed\Unzer\Business\Reader\UnzerReaderInterface $unzerReader
     */
    public function __construct(
        UnzerConfig $unzerConfig,
        UnzerPaymentMethodImportFilterInterface $unzerPaymentMethodImportFilter,
        PaymentImportAdapterInterface $paymentImportAdapter,
        UnzerPaymentMethodsAdapterInterface $unzerPaymentMethodsAdapter,
        UnzerReaderInterface $unzerReader
    ) {
        $this->unzerConfig = $unzerConfig;
        $this->unzerPaymentMethodImportFilter = $unzerPaymentMethodImportFilter;
        $this->paymentImportAdapter = $paymentImportAdapter;
        $this->unzerPaymentMethodsAdapter = $unzerPaymentMethodsAdapter;
        $this->unzerReader = $unzerReader;
    }

    /**
     * @param \Generated\Shared\Transfer\UnzerKeypairTransfer $unzerKeypairTransfer
     *
     * @return void
     */
    public function performPaymentMethodsImport(UnzerKeypairTransfer $unzerKeypairTransfer): void
    {
        $paymentMethodsTransfer = $this->unzerPaymentMethodsAdapter->getPaymentMethods($unzerKeypairTransfer);
        $unzerCredentialsCollectionTransfer = $this->getChildUnzerCredentialsCollectionTransfer($unzerKeypairTransfer);
        foreach ($unzerCredentialsCollectionTransfer->getUnzerCredentials() as $unzerCredentialsTransfer) {
            if ($unzerCredentialsTransfer->getUnzerKeypair()) {
                $paymentMethodsTransfer = $this->appendChildPaymentMethods(
                    $paymentMethodsTransfer,
                    $this->unzerPaymentMethodsAdapter->getPaymentMethods($unzerCredentialsTransfer->getUnzerKeypairOrFail()),
                );
            }
        }

        if ($paymentMethodsTransfer->getMethods()->count() === 0) {
            return;
        }

        $paymentProviderTransfer = $this->paymentImportAdapter->findPaymentProvider($this->unzerConfig->getPaymentProviderName());

        if ($paymentProviderTransfer === null) {
            $this->paymentImportAdapter->createPaymentProvider(
                $this->unzerConfig->getPaymentProviderName(),
                $paymentMethodsTransfer->getMethods(),
            );

            return;
        }

        $paymentMethodTransfers = $this->unzerPaymentMethodImportFilter->filterOutStoredPaymentMethods(
            $paymentMethodsTransfer->getMethods(),
            $paymentProviderTransfer->getPaymentMethods(),
        );

        if ($paymentMethodTransfers->count() === 0) {
            return;
        }

        $this->paymentImportAdapter->createPaymentMethods($paymentProviderTransfer, $paymentMethodTransfers);
    }

    /**
     * @param \Generated\Shared\Transfer\PaymentMethodsTransfer $paymentMethodsTransfer
     * @param \Generated\Shared\Transfer\PaymentMethodsTransfer $childPaymentMethodsTransfer
     *
     * @return \Generated\Shared\Transfer\PaymentMethodsTransfer
     */
    protected function appendChildPaymentMethods(
        PaymentMethodsTransfer $paymentMethodsTransfer,
        PaymentMethodsTransfer $childPaymentMethodsTransfer
    ): PaymentMethodsTransfer {
        $unzerPaymentMethodKeys = $this->extractPaymentMethodKeysFromPaymentMethodsTransfer($paymentMethodsTransfer);

        foreach ($childPaymentMethodsTransfer->getMethods() as $paymentMethodTransfer) {
            if (!in_array($paymentMethodTransfer->getPaymentMethodKeyOrFail(), $unzerPaymentMethodKeys, true)) {
                $paymentMethodsTransfer->getMethods()->append($paymentMethodTransfer);
            }
        }

        return $paymentMethodsTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\UnzerKeypairTransfer $unzerKeypairTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerCredentialsCollectionTransfer
     */
    protected function getChildUnzerCredentialsCollectionTransfer(
        UnzerKeypairTransfer $unzerKeypairTransfer
    ): UnzerCredentialsCollectionTransfer {
        $unzerCredentialsConditionsTransfer = (new UnzerCredentialsConditionsTransfer())->addParentId($unzerKeypairTransfer->getIdUnzerCredentialsOrFail());
        $unzerCredentialsCriteriaTransfer = (new UnzerCredentialsCriteriaTransfer())->setUnzerCredentialsConditions($unzerCredentialsConditionsTransfer);

        return $this->unzerReader->getUnzerCredentialsCollectionByCriteria($unzerCredentialsCriteriaTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\PaymentMethodsTransfer $paymentMethodsTransfer
     *
     * @return array<string>
     */
    protected function extractPaymentMethodKeysFromPaymentMethodsTransfer(PaymentMethodsTransfer $paymentMethodsTransfer): array
    {
        $paymentMethodKeys = [];
        foreach ($paymentMethodsTransfer->getMethods() as $paymentMethodTransfer) {
            $paymentMethodKeys[] = $paymentMethodTransfer->getPaymentMethodKeyOrFail();
        }

        return $paymentMethodKeys;
    }
}
