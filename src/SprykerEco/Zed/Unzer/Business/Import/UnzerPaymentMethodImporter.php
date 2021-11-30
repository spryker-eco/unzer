<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Business\Import;

use ArrayObject;
use Generated\Shared\Transfer\PaymentMethodCollectionRequestTransfer;
use Generated\Shared\Transfer\PaymentProviderCollectionRequestTransfer;
use Generated\Shared\Transfer\PaymentProviderCollectionTransfer;
use Generated\Shared\Transfer\PaymentProviderConditionsTransfer;
use Generated\Shared\Transfer\PaymentProviderCriteriaTransfer;
use Generated\Shared\Transfer\PaymentProviderTransfer;
use Generated\Shared\Transfer\UnzerApiGetPaymentTypesRequestTransfer;
use Generated\Shared\Transfer\UnzerApiGetPaymentTypesResponseTransfer;
use Generated\Shared\Transfer\UnzerApiRequestTransfer;
use Generated\Shared\Transfer\UnzerApiResponseTransfer;
use SprykerEco\Zed\Unzer\Business\Import\Filter\UnzerPaymentMethodImportFilterInterface;
use SprykerEco\Zed\Unzer\Business\Import\Mapper\UnzerPaymentMethodMapperInterface;
use SprykerEco\Zed\Unzer\Dependency\UnzerToPaymentFacadeInterface;
use SprykerEco\Zed\Unzer\Dependency\UnzerToUnzerApiFacadeInterface;
use SprykerEco\Zed\Unzer\UnzerConfig;

class UnzerPaymentMethodImporter implements UnzerPaymentMethodImporterInterface
{
    /**
     * @var \SprykerEco\Zed\Unzer\UnzerConfig
     */
    protected UnzerConfig $unzerConfig;

    /**
     * @var \SprykerEco\Zed\Unzer\Dependency\UnzerToUnzerApiFacadeInterface
     */
    protected $unzerApiFacade;

    /**
     * @var \SprykerEco\Zed\Unzer\Business\Import\Mapper\UnzerPaymentMethodMapperInterface
     */
    protected $unzerPaymentMethodMapper;

    /**
     * @var \SprykerEco\Zed\Unzer\Business\Import\Filter\UnzerPaymentMethodImportFilterInterface
     */
    protected $unzerPaymentMethodImportFilter;

    /**
     * @var \SprykerEco\Zed\Unzer\Dependency\UnzerToPaymentFacadeInterface
     */
    protected $paymentFacade;

    /**
     * @param \SprykerEco\Zed\Unzer\UnzerConfig $unzerConfig
     * @param \SprykerEco\Zed\Unzer\Dependency\UnzerToUnzerApiFacadeInterface $unzerApiFacade
     * @param \SprykerEco\Zed\Unzer\Business\Import\Mapper\UnzerPaymentMethodMapperInterface $unzerPaymentMethodMapper
     * @param \SprykerEco\Zed\Unzer\Business\Import\Filter\UnzerPaymentMethodImportFilterInterface $unzerPaymentMethodImportFilter
     * @param \SprykerEco\Zed\Unzer\Dependency\UnzerToPaymentFacadeInterface $paymentFacade
     */
    public function __construct(
        UnzerConfig $unzerConfig,
        UnzerToUnzerApiFacadeInterface $unzerApiFacade,
        UnzerPaymentMethodMapperInterface $unzerPaymentMethodMapper,
        UnzerPaymentMethodImportFilterInterface $unzerPaymentMethodImportFilter,
        UnzerToPaymentFacadeInterface $paymentFacade
    ) {
        $this->unzerConfig = $unzerConfig;
        $this->unzerApiFacade = $unzerApiFacade;
        $this->unzerPaymentMethodMapper = $unzerPaymentMethodMapper;
        $this->unzerPaymentMethodImportFilter = $unzerPaymentMethodImportFilter;
        $this->paymentFacade = $paymentFacade;
    }

    /**
     * @return void
     */
    public function performPaymentMethodImport(): void
    {
        $unzerApiResponseTransfer = $this->performGetPaymentTypesApiCall();

        if (!$unzerApiResponseTransfer->getIsSuccessful() || !$this->hasPaymentMethodTypesToImport($unzerApiResponseTransfer)) {
            return;
        }

        $paymentMethodTransfers = $this->createPaymentMethodTransfers($unzerApiResponseTransfer->getGetPaymentTypesResponse());
        $paymentProviderCollectionTransfer = $this->getPaymentProviderCollectionTransfer();

        if ($paymentProviderCollectionTransfer->getPaymentProviders()->count() === 0) {
            $paymentProviderCollectionRequestTransfer = $this->createPaymentProviderCollectionRequestTransfer($paymentMethodTransfers);

            $this->paymentFacade->createPaymentProviderCollection($paymentProviderCollectionRequestTransfer);

            return;
        }

        $paymentProviderTransfer = $paymentProviderCollectionTransfer->getPaymentProviders()->offsetGet(0);
        $paymentMethodTransfers = $this->unzerPaymentMethodImportFilter->filterStoredPaymentMethods(
            $paymentMethodTransfers,
            $paymentProviderTransfer->getPaymentMethods(),
        );

        if ($paymentMethodTransfers->count() === 0) {
            return;
        }

        $paymentMethodCollectionRequestTransfer = $this->createPaymentMethodCollectionRequestTransfer($paymentProviderTransfer, $paymentMethodTransfers);

        $this->paymentFacade->createPaymentMethodCollection($paymentMethodCollectionRequestTransfer);
    }

    /**
     * @param \ArrayObject $paymentMethodTransfers
     *
     * @return \Generated\Shared\Transfer\PaymentProviderCollectionRequestTransfer
     */
    protected function createPaymentProviderCollectionRequestTransfer(ArrayObject $paymentMethodTransfers): PaymentProviderCollectionRequestTransfer
    {
        $paymentProviderTransfer = $this->createPaymentProviderTransfer($paymentMethodTransfers);

        return (new PaymentProviderCollectionRequestTransfer())->addPaymentProvider($paymentProviderTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\PaymentProviderTransfer $paymentProviderTransfer
     * @param \ArrayObject $paymentMethodTransfers
     *
     * @return \Generated\Shared\Transfer\PaymentMethodCollectionRequestTransfer
     */
    protected function createPaymentMethodCollectionRequestTransfer(
        PaymentProviderTransfer $paymentProviderTransfer,
        ArrayObject $paymentMethodTransfers
    ): PaymentMethodCollectionRequestTransfer {
        foreach ($paymentMethodTransfers as $paymentMethodTransfer) {
            $paymentMethodTransfer->setIdPaymentProvider($paymentProviderTransfer->getIdPaymentProvider());
        }

        return (new PaymentMethodCollectionRequestTransfer())->setPaymentMethods($paymentMethodTransfers);
    }

    /**
     * @param \ArrayObject $paymentMethodTransfers
     *
     * @return \Generated\Shared\Transfer\PaymentProviderTransfer
     */
    protected function createPaymentProviderTransfer(ArrayObject $paymentMethodTransfers): PaymentProviderTransfer
    {
        $paymentProviderName = $this->unzerConfig->getPaymentProviderName();

        return (new PaymentProviderTransfer())->setName($paymentProviderName)
            ->setPaymentProviderKey($paymentProviderName)
            ->setPaymentMethods($paymentMethodTransfers);
    }

    /**
     * @param \Generated\Shared\Transfer\UnzerApiGetPaymentTypesResponseTransfer $unzerApiGetPaymentTypesResponseTransfer
     *
     * @return \ArrayObject
     */
    protected function createPaymentMethodTransfers(UnzerApiGetPaymentTypesResponseTransfer $unzerApiGetPaymentTypesResponseTransfer): ArrayObject
    {
        $unzerApiPaymentTypeTransfers = $unzerApiGetPaymentTypesResponseTransfer->getTypes();

        return $this->unzerPaymentMethodMapper->mapUnzerApiPaymentTypeTransfersToPaymentMethodTransfers($unzerApiPaymentTypeTransfers);
    }

    /**
     * @return \Generated\Shared\Transfer\PaymentProviderCollectionTransfer
     */
    protected function getPaymentProviderCollectionTransfer(): PaymentProviderCollectionTransfer
    {
        $paymentProviderCriteriaTransfer = $this->createPaymentProviderCriteriaTransfer();

        return $this->paymentFacade->getPaymentProviderCollection($paymentProviderCriteriaTransfer);
    }

    /**
     * @return \Generated\Shared\Transfer\PaymentProviderCriteriaTransfer
     */
    protected function createPaymentProviderCriteriaTransfer(): PaymentProviderCriteriaTransfer
    {
        $paymentProviderCriteriaTransfer = new PaymentProviderCriteriaTransfer();
        $paymentProviderConditionsTransfer = $this->createPaymentProviderConditionsTransfer();

        $paymentProviderCriteriaTransfer->setPaymentProviderConditions($paymentProviderConditionsTransfer);

        return $paymentProviderCriteriaTransfer;
    }

    /**
     * @return \Generated\Shared\Transfer\PaymentProviderConditionsTransfer
     */
    protected function createPaymentProviderConditionsTransfer(): PaymentProviderConditionsTransfer
    {
        return (new PaymentProviderConditionsTransfer())->addName($this->unzerConfig->getPaymentProviderName());
    }

    /**
     * @param \Generated\Shared\Transfer\UnzerApiResponseTransfer $unzerApiResponseTransfer
     *
     * @return bool
     */
    protected function hasPaymentMethodTypesToImport(UnzerApiResponseTransfer $unzerApiResponseTransfer): bool
    {
        return $unzerApiResponseTransfer->getGetPaymentTypesResponse() && $unzerApiResponseTransfer->getGetPaymentTypesResponseOrFail()->getTypes()->count() !== 0;
    }

    /**
     * @return \Generated\Shared\Transfer\UnzerApiResponseTransfer
     */
    protected function performGetPaymentTypesApiCall(): UnzerApiResponseTransfer
    {
        $unzerApiRequestTransfer = $this->createUnzerApiRequestTransfer();

        return $this->unzerApiFacade->performGetPaymentTypesApiCall($unzerApiRequestTransfer);
    }

    /**
     * @return \Generated\Shared\Transfer\UnzerApiGetPaymentTypesRequestTransfer
     */
    protected function createUnzerApiGetPaymentTypesRequestTransfer(): UnzerApiGetPaymentTypesRequestTransfer
    {
        return new UnzerApiGetPaymentTypesRequestTransfer();
    }

    /**
     * @return \Generated\Shared\Transfer\UnzerApiRequestTransfer
     */
    protected function createUnzerApiRequestTransfer(): UnzerApiRequestTransfer
    {
        $unzerApiGetPaymentTypesRequestTransfer = $this->createUnzerApiGetPaymentTypesRequestTransfer();

        return (new UnzerApiRequestTransfer())->setGetPaymentTypesRequest($unzerApiGetPaymentTypesRequestTransfer);
    }
}
