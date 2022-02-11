<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Communication;

use Spryker\Zed\Calculation\Business\CalculationFacadeInterface;
use Spryker\Zed\Kernel\Communication\AbstractCommunicationFactory;
use Spryker\Zed\Refund\Business\RefundFacadeInterface;
use SprykerEco\Zed\Unzer\Communication\Oms\Command\ChargeUnzerOmsCommand;
use SprykerEco\Zed\Unzer\Communication\Oms\Command\RefundUnzerOmsCommand;
use SprykerEco\Zed\Unzer\Communication\Oms\Command\UnzerOmsCommandInterface;
use SprykerEco\Zed\Unzer\Communication\Oms\UnzerOmsMapper;
use SprykerEco\Zed\Unzer\Communication\Oms\UnzerOmsMapperInterface;
use SprykerEco\Zed\Unzer\Dependency\UnzerToCalculationFacadeInterface;
use SprykerEco\Zed\Unzer\Dependency\UnzerToSalesFacadeInterface;
use SprykerEco\Zed\Unzer\UnzerDependencyProvider;

/**
 * @method \SprykerEco\Zed\Unzer\UnzerConfig getConfig()
 * @method \SprykerEco\Zed\Unzer\Business\UnzerFacadeInterface getFacade()
 * @method \SprykerEco\Zed\Unzer\Persistence\UnzerRepositoryInterface getRepository()
 * @method \SprykerEco\Zed\Unzer\Persistence\UnzerEntityManagerInterface getEntityManager()
 */
class UnzerCommunicationFactory extends AbstractCommunicationFactory
{
    /**
     * @return \SprykerEco\Zed\Unzer\Communication\Oms\Command\UnzerOmsCommandInterface
     */
    public function createChargeOmsCommand(): UnzerOmsCommandInterface
    {
        return new ChargeUnzerOmsCommand(
            $this->getFacade(),
            $this->createUnzerOmsMapper(),
        );
    }

    /**
     * @return \SprykerEco\Zed\Unzer\Communication\Oms\Command\UnzerOmsCommandInterface
     */
    public function createRefundOmsCommand(): UnzerOmsCommandInterface
    {
        return new RefundUnzerOmsCommand(
            $this->getFacade(),
            $this->getRefundFacade(),
            $this->createUnzerOmsMapper(),
        );
    }

    /**
     * @return \SprykerEco\Zed\Unzer\Communication\Oms\UnzerOmsMapperInterface
     */
    public function createUnzerOmsMapper(): UnzerOmsMapperInterface
    {
        return new UnzerOmsMapper(
            $this->getSalesFacade(),
            $this->getCalculationFacade(),
        );
    }

    /**
     * @return \SprykerEco\Zed\Unzer\Dependency\UnzerToSalesFacadeInterface
     */
    public function getSalesFacade(): UnzerToSalesFacadeInterface
    {
        /** @var \SprykerEco\Zed\Unzer\Dependency\UnzerToSalesFacadeInterface $salesFacade */
        $salesFacade = $this->getProvidedDependency(UnzerDependencyProvider::FACADE_SALES);

        return $salesFacade;
    }

    /**
     * @return UnzerToCalculationFacadeInterface
     */
    public function getCalculationFacade(): UnzerToCalculationFacadeInterface
    {
        return $this->getProvidedDependency(UnzerDependencyProvider::FACADE_CALCULATION);
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
