<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Business\Import\Adapter;

use ArrayObject;
use Generated\Shared\Transfer\PaymentMethodCollectionRequestTransfer;
use Generated\Shared\Transfer\PaymentMethodCollectionResponseTransfer;
use Generated\Shared\Transfer\PaymentProviderCollectionRequestTransfer;
use Generated\Shared\Transfer\PaymentProviderCollectionResponseTransfer;
use Generated\Shared\Transfer\PaymentProviderConditionsTransfer;
use Generated\Shared\Transfer\PaymentProviderCriteriaTransfer;
use Generated\Shared\Transfer\PaymentProviderTransfer;
use SprykerEco\Zed\Unzer\Dependency\UnzerToPaymentFacadeInterface;

class PaymentImportAdapter implements PaymentImportAdapterInterface
{
    /**
     * @var \SprykerEco\Zed\Unzer\Dependency\UnzerToPaymentFacadeInterface
     */
    protected $paymentFacade;

    /**
     * @param \SprykerEco\Zed\Unzer\Dependency\UnzerToPaymentFacadeInterface $paymentFacade
     */
    public function __construct(UnzerToPaymentFacadeInterface $paymentFacade)
    {
        $this->paymentFacade = $paymentFacade;
    }

    /**
     * @param string $paymentProviderName
     *
     * @return \Generated\Shared\Transfer\PaymentProviderTransfer|null
     */
    public function findPaymentProvider(string $paymentProviderName): ?PaymentProviderTransfer
    {
        $paymentProviderConditionsTransfer = (new PaymentProviderConditionsTransfer())->addName($paymentProviderName);
        $paymentProviderCriteriaTransfer = (new PaymentProviderCriteriaTransfer())->setPaymentProviderConditions($paymentProviderConditionsTransfer);
        $paymentProviderCollectionTransfer = $this->paymentFacade->getPaymentProviderCollection($paymentProviderCriteriaTransfer);

        if ($paymentProviderCollectionTransfer->getPaymentProviders()->count() === 0) {
            return null;
        }

        return $paymentProviderCollectionTransfer->getPaymentProviders()->offsetGet(0);
    }

    /**
     * @param string $paymentProviderName
     * @param \ArrayObject<\Generated\Shared\Transfer\PaymentMethodTransfer> $paymentMethodTransfers
     *
     * @return \Generated\Shared\Transfer\PaymentProviderCollectionResponseTransfer
     */
    public function createPaymentProvider(string $paymentProviderName, ArrayObject $paymentMethodTransfers): PaymentProviderCollectionResponseTransfer
    {
        $paymentProviderTransfer = $this->createPaymentProviderTransfer($paymentProviderName, $paymentMethodTransfers);
        $paymentProviderCollectionRequestTransfer = (new PaymentProviderCollectionRequestTransfer())->addPaymentProvider($paymentProviderTransfer);

        return $this->paymentFacade->createPaymentProviderCollection($paymentProviderCollectionRequestTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\PaymentProviderTransfer $paymentProviderTransfer
     * @param \ArrayObject<\Generated\Shared\Transfer\PaymentMethodTransfer> $paymentMethodTransfers
     *
     * @throws \Spryker\Shared\Kernel\Transfer\Exception\NullValueException
     *
     * @return \Generated\Shared\Transfer\PaymentMethodCollectionResponseTransfer
     */
    public function createPaymentMethods(PaymentProviderTransfer $paymentProviderTransfer, ArrayObject $paymentMethodTransfers): PaymentMethodCollectionResponseTransfer
    {
        foreach ($paymentMethodTransfers as $paymentMethodTransfer) {
            $paymentMethodTransfer->setIdPaymentProvider($paymentProviderTransfer->getIdPaymentProviderOrFail());
        }

        $paymentMethodCollectionRequestTransfer = (new PaymentMethodCollectionRequestTransfer())->setPaymentMethods($paymentMethodTransfers);

        return $this->paymentFacade->createPaymentMethodCollection($paymentMethodCollectionRequestTransfer);
    }

    /**
     * @param string $paymentProviderName
     * @param \ArrayObject<\Generated\Shared\Transfer\PaymentMethodTransfer> $paymentMethodTransfers
     *
     * @return \Generated\Shared\Transfer\PaymentProviderTransfer
     */
    protected function createPaymentProviderTransfer(string $paymentProviderName, ArrayObject $paymentMethodTransfers): PaymentProviderTransfer
    {
        return (new PaymentProviderTransfer())->setName($paymentProviderName)
            ->setPaymentProviderKey($paymentProviderName)
            ->setPaymentMethods($paymentMethodTransfers);
    }
}
