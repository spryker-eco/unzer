<?php

namespace SprykerEco\Zed\Unzer\Business\Oms\Condition;

use SprykerEco\Zed\Unzer\Business\Reader\UnzerReaderInterface;
use SprykerEco\Zed\Unzer\UnzerConfig;

class IsChargebackOmsCondition implements UnzerConditionInterface
{
    /**
     * @var \SprykerEco\Zed\Unzer\Business\Reader\UnzerReaderInterface
     */
    protected $unzerReader;

    /**
     * @var \SprykerEco\Zed\Unzer\UnzerConfig
     */
    protected $unzerConfig;

    /**
     * @param \SprykerEco\Zed\Unzer\Business\Reader\UnzerReaderInterface $unzerReader
     * @param \SprykerEco\Zed\Unzer\UnzerConfig $unzerConfig
     */
    public function __construct(UnzerReaderInterface $unzerReader, UnzerConfig $unzerConfig)
    {
        $this->unzerReader = $unzerReader;
        $this->unzerConfig = $unzerConfig;
    }

    /**
     * @param int $idSalesOrderItem
     *
     * @return bool
     */
    public function check(int $idSalesOrderItem): bool
    {
        $paymentUnzerOrderItemTransfer = $this->unzerReader->getPaymentUnzerOrderItemByIdSalesOrderItem($idSalesOrderItem);

        return $paymentUnzerOrderItemTransfer->getStatus() === $this->unzerConfig->getOmsStatusChargeback();
    }
}
