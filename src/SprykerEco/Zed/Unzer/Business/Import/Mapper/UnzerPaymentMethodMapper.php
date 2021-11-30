<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Business\Import\Mapper;

use ArrayObject;
use Generated\Shared\Transfer\PaymentMethodTransfer;
use Generated\Shared\Transfer\UnzerApiPaymentTypeTransfer;
use SprykerEco\Zed\Unzer\UnzerConfig;

class UnzerPaymentMethodMapper implements UnzerPaymentMethodMapperInterface
{
    /**
     * @var \SprykerEco\Zed\Unzer\UnzerConfig
     */
    protected $unzerConfig;

    /**
     * @param \SprykerEco\Zed\Unzer\UnzerConfig $unzerConfig
     */
    public function __construct(UnzerConfig $unzerConfig)
    {
        $this->unzerConfig = $unzerConfig;
    }

    /**
     * @param \ArrayObject $unzerApiPaymentTypeTransfers
     *
     * @return \ArrayObject
     */
    public function mapUnzerApiPaymentTypeTransfersToPaymentMethodTransfers(ArrayObject $unzerApiPaymentTypeTransfers): ArrayObject
    {
        $paymentMethodTransfers = new ArrayObject();

        foreach ($unzerApiPaymentTypeTransfers as $unzerApiPaymentTypeTransfer) {
            $paymentMethodKeys = $this->getPaymentMethodKeys($unzerApiPaymentTypeTransfer);

            foreach ($paymentMethodKeys as $paymentMethodKey) {
                $paymentMethodTransfer = $this->createPaymentMethodTransfer($paymentMethodKey);
                $paymentMethodTransfers->append($paymentMethodTransfer);
            }
        }

        return $paymentMethodTransfers;
    }

    /**
     * @param string $paymentMethodKey
     *
     * @return \Generated\Shared\Transfer\PaymentMethodTransfer
     */
    protected function createPaymentMethodTransfer(string $paymentMethodKey): PaymentMethodTransfer
    {
        $paymentMethodName = $this->getPaymentMethodName($paymentMethodKey);

        return (new PaymentMethodTransfer())
            ->setPaymentMethodKey($paymentMethodKey)
            ->setName($paymentMethodName);
    }

    /**
     * @param string $paymentMethodKey
     *
     * @return string
     */
    protected function getPaymentMethodName(string $paymentMethodKey): string
    {
        return $this->unzerConfig->getPaymentMethodName($paymentMethodKey);
    }

    /**
     * @param \Generated\Shared\Transfer\UnzerApiPaymentTypeTransfer $unzerApiPaymentTypeTransfer
     *
     * @return array<array-key, string>
     */
    protected function getPaymentMethodKeys(UnzerApiPaymentTypeTransfer $unzerApiPaymentTypeTransfer): array
    {
        return $this->unzerConfig->getPaymentMethodKeys($unzerApiPaymentTypeTransfer->getTypeOrFail());
    }
}
