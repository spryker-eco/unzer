<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Business\Payment\Processor\DirectCharge;

use Generated\Shared\Transfer\UnzerApiChargeResponseTransfer;
use Generated\Shared\Transfer\UnzerPaymentTransfer;
use SprykerEco\Zed\Unzer\Business\ApiAdapter\Mapper\UnzerChargeMapperInterface;
use SprykerEco\Zed\Unzer\Business\ApiAdapter\UnzerChargeAdapterInterface;
use SprykerEco\Zed\Unzer\Persistence\UnzerEntityManagerInterface;
use SprykerEco\Zed\Unzer\Persistence\UnzerRepositoryInterface;

class UnzerDirectChargeProcessor implements UnzerDirectChargeProcessorInterface
{
    /**
     * @var \SprykerEco\Zed\Unzer\Business\ApiAdapter\UnzerChargeAdapterInterface
     */
    protected $unzerChargeAdapter;

    /**
     * @var \SprykerEco\Zed\Unzer\Business\ApiAdapter\Mapper\UnzerChargeMapperInterface
     */
    protected $unzerChargeMapper;

    /**
     * @var \SprykerEco\Zed\Unzer\Persistence\UnzerRepositoryInterface
     */
    protected $unzerRepository;

    /**
     * @var \SprykerEco\Zed\Unzer\Persistence\UnzerEntityManagerInterface
     */
    protected $unzerEntityManager;

    /**
     * @param \SprykerEco\Zed\Unzer\Business\ApiAdapter\UnzerChargeAdapterInterface $unzerChargeAdapter
     * @param \SprykerEco\Zed\Unzer\Persistence\UnzerRepositoryInterface $unzerRepository
     * @param \SprykerEco\Zed\Unzer\Persistence\UnzerEntityManagerInterface $unzerEntityManager
     */
    public function __construct(
        UnzerChargeAdapterInterface $unzerChargeAdapter,
        UnzerChargeMapperInterface $unzerChargeMapper,
        UnzerRepositoryInterface $unzerRepository,
        UnzerEntityManagerInterface $unzerEntityManager
    ) {
        $this->unzerChargeAdapter = $unzerChargeAdapter;
        $this->unzerChargeMapper = $unzerChargeMapper;
        $this->unzerRepository = $unzerRepository;
        $this->unzerEntityManager = $unzerEntityManager;
    }

    /**
     * @param \Generated\Shared\Transfer\UnzerPaymentTransfer $unzerPaymentTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerPaymentTransfer
     */
    public function charge(UnzerPaymentTransfer $unzerPaymentTransfer): UnzerPaymentTransfer
    {
        $unzerApiChargeResponseTransfer = $this->unzerChargeAdapter->chargePayment($unzerPaymentTransfer);
        $this->updatePaymentUnzerOrderItemEntities($unzerPaymentTransfer, $unzerApiChargeResponseTransfer);

        return $this->unzerChargeMapper
            ->mapUnzerApiChargeResponseTransferToUnzerPaymentTransfer(
                $unzerApiChargeResponseTransfer,
                $unzerPaymentTransfer,
            );
    }

    /**
     * @param \Generated\Shared\Transfer\UnzerPaymentTransfer $unzerPaymentTransfer
     * @param \Generated\Shared\Transfer\UnzerApiChargeResponseTransfer $unzerApiChargeResponseTransfer
     *
     * @return void
     */
    protected function updatePaymentUnzerOrderItemEntities(
        UnzerPaymentTransfer $unzerPaymentTransfer,
        UnzerApiChargeResponseTransfer $unzerApiChargeResponseTransfer
    ): void {
        $paymentUnzerOrderItemCollectionTransfer = $this->unzerRepository
            ->getPaymentUnzerOrderItemCollectionByOrderId($unzerPaymentTransfer->getOrderIdOrFail());

        foreach ($paymentUnzerOrderItemCollectionTransfer->getPaymentUnzerOrderItems() as $paymentUnzerOrderItemTransfer) {
            $paymentUnzerOrderItemTransfer->setChargeId($unzerApiChargeResponseTransfer->getIdOrFail());
            $this->unzerEntityManager->updatePaymentUnzerOrderItemEntity($paymentUnzerOrderItemTransfer);
        }
    }
}
