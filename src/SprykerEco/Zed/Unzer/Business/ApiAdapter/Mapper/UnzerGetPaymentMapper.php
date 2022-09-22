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
use SprykerEco\Zed\Unzer\UnzerConstants;

class UnzerGetPaymentMapper extends UnzerErrorMapper implements UnzerGetPaymentMapperInterface
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
        // do not change to fromArray-toArray because Unzer sends some already known fields as empty!
        $unzerPaymentTransfer = $unzerPaymentTransfer
            ->setStateId($unzerApiGetPaymentResponseTransfer->getStateId())
            ->setStateName($unzerApiGetPaymentResponseTransfer->getStateName())
            ->setAmountTotal((int)($unzerApiGetPaymentResponseTransfer->getAmountTotal() * UnzerConstants::INT_TO_FLOAT_DIVIDER))
            ->setAmountCharged((int)($unzerApiGetPaymentResponseTransfer->getAmountCharged() * UnzerConstants::INT_TO_FLOAT_DIVIDER))
            ->setAmountCanceled((int)($unzerApiGetPaymentResponseTransfer->getAmountCanceled() * UnzerConstants::INT_TO_FLOAT_DIVIDER))
            ->setAmountRemaining((int)($unzerApiGetPaymentResponseTransfer->getAmountRemaining() * UnzerConstants::INT_TO_FLOAT_DIVIDER));

        $unzerPaymentTransactions = $this->mapUnzerApiGetPaymentResponseTransferToUnzerTransactionTransfers($unzerApiGetPaymentResponseTransfer);

        return $unzerPaymentTransfer->setTransactions($unzerPaymentTransactions);
    }

    /**
     * @param \Generated\Shared\Transfer\UnzerApiGetPaymentResponseTransfer $unzerApiGetPaymentResponseTransfer
     *
     * @return \ArrayObject<int, \Generated\Shared\Transfer\UnzerTransactionTransfer>
     */
    protected function mapUnzerApiGetPaymentResponseTransferToUnzerTransactionTransfers(
        UnzerApiGetPaymentResponseTransfer $unzerApiGetPaymentResponseTransfer
    ): ArrayObject {
        $unzerTransactionTransfers = new ArrayObject();
        foreach ($unzerApiGetPaymentResponseTransfer->getTransactions() as $unzerApiTransactionTransfer) {
            $unzerTransactionTransfer = (new UnzerTransactionTransfer())
                ->fromArray($unzerApiTransactionTransfer->toArray(), true)
                ->setAmount((float)$unzerApiTransactionTransfer->getAmount() * UnzerConstants::INT_TO_FLOAT_DIVIDER);

            $unzerTransactionTransfers->append($unzerTransactionTransfer);
        }

        return $unzerTransactionTransfers;
    }
}
