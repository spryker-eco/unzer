<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Business\Import;

use Generated\Shared\Transfer\UnzerKeypairTransfer;
use SprykerEco\Zed\Unzer\Business\ApiAdapter\UnzerPaymentMethodsAdapterInterface;
use SprykerEco\Zed\Unzer\Business\Import\Adapter\PaymentImportAdapterInterface;
use SprykerEco\Zed\Unzer\Business\Import\Filter\UnzerPaymentMethodImportFilterInterface;
use SprykerEco\Zed\Unzer\UnzerConfig;

class UnzerPaymentMethodImporter implements UnzerPaymentMethodImporterInterface
{
    /**
     * @var \SprykerEco\Zed\Unzer\UnzerConfig
     */
    protected $unzerConfig;

    /**
     * @var \SprykerEco\Zed\Unzer\Business\Import\Filter\UnzerPaymentMethodImportFilterInterface
     */
    protected $unzerPaymentMethodImportFilter;

    /**
     * @var \SprykerEco\Zed\Unzer\Business\ApiAdapter\UnzerPaymentMethodsAdapterInterface
     */
    protected $unzerPaymentMethodsAdapter;

    /**
     * @var \SprykerEco\Zed\Unzer\Business\Import\Adapter\PaymentImportAdapterInterface
     */
    protected $paymentImportAdapter;

    /**
     * @param \SprykerEco\Zed\Unzer\UnzerConfig $unzerConfig
     * @param \SprykerEco\Zed\Unzer\Business\Import\Filter\UnzerPaymentMethodImportFilterInterface $unzerPaymentMethodImportFilter
     * @param \SprykerEco\Zed\Unzer\Business\Import\Adapter\PaymentImportAdapterInterface $paymentImportAdapter
     * @param \SprykerEco\Zed\Unzer\Business\ApiAdapter\UnzerPaymentMethodsAdapterInterface $unzerPaymentMethodsAdapter
     */
    public function __construct(
        UnzerConfig $unzerConfig,
        UnzerPaymentMethodImportFilterInterface $unzerPaymentMethodImportFilter,
        PaymentImportAdapterInterface $paymentImportAdapter,
        UnzerPaymentMethodsAdapterInterface $unzerPaymentMethodsAdapter
    ) {
        $this->unzerConfig = $unzerConfig;
        $this->unzerPaymentMethodImportFilter = $unzerPaymentMethodImportFilter;
        $this->paymentImportAdapter = $paymentImportAdapter;
        $this->unzerPaymentMethodsAdapter = $unzerPaymentMethodsAdapter;
    }

    /**
     * @param \Generated\Shared\Transfer\UnzerKeypairTransfer $unzerKeypairTransfer
     *
     * @return void
     */
    public function performPaymentMethodsImport(UnzerKeypairTransfer $unzerKeypairTransfer): void
    {
        $paymentMethodsTransfer = $this->unzerPaymentMethodsAdapter->getPaymentMethods($unzerKeypairTransfer);

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
}
