<?php

namespace SprykerEco\Zed\Unzer\Communication;

use SprykerEco\Zed\Sales\Business\SalesFacadeInterface;
use SprykerEco\Zed\Unzer\Communication\Oms\Command\ChargeCommand;
use SprykerEco\Zed\Unzer\Communication\Oms\Command\RefundCommand;
use SprykerEco\Zed\Unzer\Communication\Oms\Command\UnzerOmsCommandByOrderInterface;
use SprykerEco\Zed\Unzer\Communication\Oms\UnzerOmsMapper;
use SprykerEco\Zed\Unzer\Communication\Oms\UnzerOmsMapperInterface;
use SprykerEco\Zed\Unzer\UnzerDependencyProvider;
use Spryker\Zed\Calculation\Business\CalculationFacadeInterface;
use Spryker\Zed\Kernel\Communication\AbstractCommunicationFactory;
use Spryker\Zed\Refund\Business\RefundFacadeInterface;

/**
 * @method \SprykerEco\Zed\Unzer\UnzerConfig getConfig()
 * @method \SprykerEco\Zed\Unzer\Business\UnzerFacadeInterface getFacade()
 * @method \SprykerEco\Zed\Unzer\Persistence\UnzerRepositoryInterface getRepository()
 * @method \SprykerEco\Zed\Unzer\Persistence\UnzerEntityManagerInterface getEntityManager()
 */
class UnzerCommunicationFactory extends AbstractCommunicationFactory
{
    /**
     * @return \SprykerEco\Zed\Unzer\Communication\Oms\Command\UnzerOmsCommandByOrderInterface
     */
    public function createChargeOmsCommand(): UnzerOmsCommandByOrderInterface
    {
        return new ChargeCommand(
            $this->getFacade(),
            $this->createUnzerOmsMapper()
        );
    }

    /**
     * @return \SprykerEco\Zed\Unzer\Communication\Oms\Command\UnzerOmsCommandByOrderInterface
     */
    public function createRefundOmsCommand(): UnzerOmsCommandByOrderInterface
    {
        return new RefundCommand(
            $this->getFacade(),
            $this->getRefundFacade(),
            $this->createUnzerOmsMapper()
        );
    }

    /**
     * @return \SprykerEco\Zed\Unzer\Communication\Oms\UnzerOmsMapperInterface
     */
    public function createUnzerOmsMapper(): UnzerOmsMapperInterface
    {
        return new UnzerOmsMapper(
            $this->getSalesFacade(),
            $this->getCalculationFacade()
        );
    }

    /**
     * @return \SprykerEco\Zed\Sales\Business\SalesFacadeInterface
     */
    public function getSalesFacade(): SalesFacadeInterface
    {
        /** @var \SprykerEco\Zed\Sales\Business\SalesFacadeInterface $salesFacade */
        $salesFacade = $this->getProvidedDependency(UnzerDependencyProvider::FACADE_SALES);

        return $salesFacade;
    }

    /**
     * @return \Spryker\Zed\Calculation\Business\CalculationFacadeInterface
     */
    public function getCalculationFacade(): CalculationFacadeInterface
    {
        /** @var \Spryker\Zed\Calculation\Business\CalculationFacadeInterface $calculationFacade */
        $calculationFacade = $this->getProvidedDependency(UnzerDependencyProvider::FACADE_CALCULATION);

        return $calculationFacade;
    }

    /**
     * @return \Spryker\Zed\Refund\Business\RefundFacadeInterface
     */
    public function getRefundFacade(): RefundFacadeInterface
    {
        /** @var \Spryker\Zed\Refund\Business\RefundFacadeInterface $refundFacade */
        $refundFacade = $this->getProvidedDependency(UnzerDependencyProvider::FACADE_REFUND);

        return $refundFacade;
    }
}
