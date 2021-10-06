<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Business\ApiAdapter\Mapper;

use ArrayObject;
use Generated\Shared\Transfer\UnzerApiGetPaymentRequestTransfer;
use Generated\Shared\Transfer\UnzerApiGetPaymentResponseTransfer;
use Generated\Shared\Transfer\UnzerPaymentTransfer;
use Generated\Shared\Transfer\UnzerTransactionTransfer;

class UnzerGetPaymentMapper implements UnzerGetPaymentMapperInterface
{
    /**
     * @param \Generated\Shared\Transfer\UnzerPaymentTransfer $unzerPaymentTransfer
     * @param \Generated\Shared\Transfer\UnzerApiGetPaymentRequestTransfer $unzerApiGetPaymentRequestTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerApiGetPaymentRequestTransfer
     */
    public function mapUnzerPaymentTransferToUnzerApiGetPaymentRequestTransfer(
        UnzerPaymentTransfer $unzerPaymentTransfer,
        UnzerApiGetPaymentRequestTransfer $unzerApiGetPaymentRequestTransfer
    ): UnzerApiGetPaymentRequestTransfer {
        return $unzerApiGetPaymentRequestTransfer->setPaymentId($unzerPaymentTransfer->getId());
    }

    /**
     * @param \Generated\Shared\Transfer\UnzerApiGetPaymentResponseTransfer $unzerApiGetPaymentResponseTransfer
     * @param \Generated\Shared\Transfer\UnzerPaymentTransfer $unzerPaymentTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerPaymentTransfer
     */
    public function mapUnzerApiGetPaymentResponseTransferToUnzerPaymentTransfer(
        UnzerApiGetPaymentResponseTransfer $unzerApiGetPaymentResponseTransfer,
        UnzerPaymentTransfer $unzerPaymentTransfer
    ): UnzerPaymentTransfer {
        return $unzerPaymentTransfer
            ->fromArray($unzerApiGetPaymentResponseTransfer->toArray(), true)
            ->setTransactions($this->mapUnzerTransactions($unzerApiGetPaymentResponseTransfer));
    }

    /**
     * @param \Generated\Shared\Transfer\UnzerApiGetPaymentResponseTransfer $unzerApiGetPaymentResponseTransfer
     *
     * @return \ArrayObject|\Generated\Shared\Transfer\UnzerTransactionTransfer[]
     */
    protected function mapUnzerTransactions(UnzerApiGetPaymentResponseTransfer $unzerApiGetPaymentResponseTransfer): ArrayObject
    {
        $unzerPaymentTransactions = new ArrayObject();
        foreach ($unzerApiGetPaymentResponseTransfer->getTransactions() as $unzerApiTransactionTransfer) {
            $unzerTransactionTransfer = (new UnzerTransactionTransfer())
                ->fromArray($unzerApiTransactionTransfer->toArray(), true)
                ->setAmount($unzerApiTransactionTransfer->getAmount() * 100);

            $unzerPaymentTransactions->append($unzerTransactionTransfer);
        }

        return $unzerPaymentTransactions;
    }
}