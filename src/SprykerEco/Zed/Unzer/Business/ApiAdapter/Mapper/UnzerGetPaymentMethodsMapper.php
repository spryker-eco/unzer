<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Business\ApiAdapter\Mapper;

use Generated\Shared\Transfer\PaymentMethodsTransfer;
use Generated\Shared\Transfer\PaymentMethodTransfer;
use Generated\Shared\Transfer\UnzerApiGetPaymentMethodsResponseTransfer;
use SprykerEco\Zed\Unzer\UnzerConfig;

class UnzerGetPaymentMethodsMapper implements UnzerGetPaymentMethodsMapperInterface
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
     * @param \Generated\Shared\Transfer\UnzerApiGetPaymentMethodsResponseTransfer $unzerApiGetPaymentMethodsResponseTransfer
     * @param \Generated\Shared\Transfer\PaymentMethodsTransfer $paymentMethodsTransfer
     *
     * @return \Generated\Shared\Transfer\PaymentMethodsTransfer
     */
    public function mapUnzerApiGetPaymentMethodsResponseTransferToPaymentMethodsTransfer(
        UnzerApiGetPaymentMethodsResponseTransfer $unzerApiGetPaymentMethodsResponseTransfer,
        PaymentMethodsTransfer $paymentMethodsTransfer
    ): PaymentMethodsTransfer {
        foreach ($unzerApiGetPaymentMethodsResponseTransfer->getPaymentMethods() as $unzerApiPaymentMethodTransfer) {
            $paymentMethodKeys = $this->unzerConfig->getPaymentMethodKeys($unzerApiPaymentMethodTransfer->getPaymentMethodKeyOrFail());
            $paymentMethodsTransfer = $this->mapPaymentMethodKeysToPaymentMethodTransfers($paymentMethodsTransfer, $paymentMethodKeys);
        }

        return $paymentMethodsTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\PaymentMethodsTransfer $paymentMethodsTransfer
     * @param array<array-key, string> $paymentMethodKeys
     *
     * @return \Generated\Shared\Transfer\PaymentMethodsTransfer
     */
    protected function mapPaymentMethodKeysToPaymentMethodTransfers(
        PaymentMethodsTransfer $paymentMethodsTransfer,
        array $paymentMethodKeys
    ): PaymentMethodsTransfer {
        foreach ($paymentMethodKeys as $paymentMethodKey) {
            $paymentMethodTransfer = $this->createPaymentMethodTransfer($paymentMethodKey);
            $paymentMethodsTransfer->addMethod($paymentMethodTransfer);
        }

        return $paymentMethodsTransfer;
    }

    /**
     * @param string $paymentMethodKey
     *
     * @return \Generated\Shared\Transfer\PaymentMethodTransfer
     */
    protected function createPaymentMethodTransfer(string $paymentMethodKey): PaymentMethodTransfer
    {
        $paymentMethodName = $this->unzerConfig->getPaymentMethodName($paymentMethodKey);

        return (new PaymentMethodTransfer())
            ->setPaymentMethodKey($paymentMethodKey)
            ->setName($paymentMethodName);
    }
}
