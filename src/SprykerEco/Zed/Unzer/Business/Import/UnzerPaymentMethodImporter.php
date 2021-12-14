<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Business\Import;

use Generated\Shared\Transfer\UnzerKeypairTransfer;
use SprykerEco\Zed\Unzer\Business\ApiAdapter\UnzerPaymentMethodsAdapterInterface;
use SprykerEco\Zed\Unzer\Business\Import\Adapter\PaymentAdapterInterface;
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
    private $unzerPaymentMethodsAdapter;

    /**
     * @var \SprykerEco\Zed\Unzer\Business\Import\Adapter\PaymentAdapterInterface
     */
    protected $paymentAdapter;

    /**
     * @param \SprykerEco\Zed\Unzer\UnzerConfig $unzerConfig
     * @param \SprykerEco\Zed\Unzer\Business\Import\Filter\UnzerPaymentMethodImportFilterInterface $unzerPaymentMethodImportFilter
     * @param \SprykerEco\Zed\Unzer\Business\Import\Adapter\PaymentAdapterInterface $paymentAdapter
     * @param \SprykerEco\Zed\Unzer\Business\ApiAdapter\UnzerPaymentMethodsAdapterInterface $unzerPaymentMethodsAdapter
     */
    public function __construct(
        UnzerConfig $unzerConfig,
        UnzerPaymentMethodImportFilterInterface $unzerPaymentMethodImportFilter,
        PaymentAdapterInterface $paymentAdapter,
        UnzerPaymentMethodsAdapterInterface $unzerPaymentMethodsAdapter
    ) {
        $this->unzerConfig = $unzerConfig;
        $this->unzerPaymentMethodImportFilter = $unzerPaymentMethodImportFilter;
        $this->paymentAdapter = $paymentAdapter;
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

        $paymentProviderTransfer = $this->paymentAdapter->findPaymentProvider($this->unzerConfig->getPaymentProviderName());

        if ($paymentProviderTransfer === null) {
            $this->paymentAdapter->createPaymentProvider(
                $this->unzerConfig->getPaymentProviderName(),
                $paymentMethodsTransfer->getMethods(),
            );

            return;
        }

        $paymentMethodTransfers = $this->unzerPaymentMethodImportFilter->filterStoredPaymentMethods(
            $paymentMethodsTransfer->getMethods(),
            $paymentProviderTransfer->getPaymentMethods(),
        );

        if ($paymentMethodTransfers->count() === 0) {
            return;
        }

        $this->paymentAdapter->createPaymentMethods($paymentProviderTransfer, $paymentMethodTransfers);
    }
}
