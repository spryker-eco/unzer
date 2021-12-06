<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEcoTest\Zed\Unzer\Business;

use Codeception\TestCase\Test;
use Spryker\Zed\Vault\Business\VaultFacade;
use SprykerEco\Zed\Unzer\Business\UnzerBusinessFactory;
use SprykerEco\Zed\Unzer\Dependency\UnzerToUnzerApiFacadeBridge;
use SprykerEco\Zed\Unzer\Dependency\UnzerToVaultFacadeBridge;
use SprykerEco\Zed\UnzerApi\Business\UnzerApiFacade;
use SprykerEcoTest\Zed\Unzer\UnzerZedTester;

class UnzerFacadeBaseTest extends Test
{
    /**
     * @var \SprykerEcoTest\Zed\Unzer\UnzerZedTester
     */
    protected $tester;

    /**
     * @var \SprykerEco\Zed\Unzer\Business\UnzerFacadeInterface
     */
    protected $facade;

    /**
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->facade = $this->tester->getFacade()->setFactory($this->createFactoryMock());
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\SprykerEco\Zed\Unzer\Business\UnzerBusinessFactory
     */
    protected function createFactoryMock(): UnzerBusinessFactory
    {
        $builder = $this->getMockBuilder(UnzerBusinessFactory::class);
        $builder->onlyMethods(
            [
                'getConfig',
                'getRepository',
                'getEntityManager',
                'getUnzerApiFacade',
                'getVaultFacade',
            //                'getCalculationFacade',
            //                'getQuoteClient',
            //                'getRefundFacade',
            //                'getStoreFacade',
            //                'getLocaleFacade',
            //                'getSalesFacade',
            ],
        );

        $stub = $builder->getMock();
        $stub->method('getConfig')
            ->willReturn($this->tester->createConfig());
        $stub->method('getRepository')
            ->willReturn($this->tester->createRepository());
        $stub->method('getEntityManager')
            ->willReturn($this->tester->createEntityManager());
        $stub->method('getUnzerApiFacade')
            ->willReturn($this->getUnzerApiFacade());
        $stub->method('getVaultFacade')
            ->willReturn($this->getVaultFacade());

//        $stub->method('getCalculationFacade')
//            ->willReturn($this->createCalculationFacade());
//        $stub->method('getQuoteClient')
//            ->willReturn($this->createQuoteClient());
//        $stub->method('getRefundFacade')
//            ->willReturn($this->createRefundFacade());

        return $stub;
    }

    /**
     * @return \SprykerEco\Zed\Unzer\Dependency\UnzerToUnzerApiFacadeBridge
     */
    protected function getUnzerApiFacade(): UnzerToUnzerApiFacadeBridge
    {
        return new UnzerToUnzerApiFacadeBridge($this->createUnzerApiFacadeMock());
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|\SprykerEco\Zed\UnzerApi\Business\UnzerApiFacade|mixed
     */
    protected function createUnzerApiFacadeMock(): UnzerApiFacade
    {
        return $this->makeEmpty(
            UnzerApiFacade::class,
            [
                'performSetNotificationUrlApiCall' => $this->tester->createUnzerApiResponseTransfer(),
                'performCreateCustomerApiCall' => $this->tester->createUnzerApiResponseTransfer(),
                'performUpdateCustomerApiCall' => $this->tester->createUnzerApiResponseTransfer(),
                'performCreateMetadataApiCall' => $this->tester->createUnzerApiResponseTransfer(),
                'performCreateBasketApiCall' => $this->tester->createUnzerApiResponseTransfer(),
                'performMarketplaceAuthorizeApiCall' => $this->tester->createUnzerApiResponseTransfer(),
                'performAuthorizeApiCall' => $this->tester->createUnzerApiResponseTransfer(),
                'performMarketplaceGetPaymentApiCall' => $this->tester->createUnzerApiResponseTransfer(),
                'performGetPaymentApiCall' => $this->tester->createUnzerApiResponseTransfer(),
                'performMarketplaceChargeApiCall' => $this->tester->createUnzerApiResponseTransfer(),
                'performMarketplaceAuthorizableChargeApiCall' => $this->tester->createUnzerApiResponseTransfer(),
                'performAuthorizableChargeApiCall' => $this->tester->createUnzerApiResponseTransfer(),
                'performChargeApiCall' => $this->tester->createUnzerApiResponseTransfer(),
                'performCreatePaymentResourceApiCall' => $this->tester->createUnzerApiResponseTransfer(),
                'performRefundApiCall' => $this->tester->createUnzerApiResponseTransfer(),
                'performMarketplaceRefundApiCall' => $this->tester->createUnzerApiResponseTransfer(),
            ],
        );
    }

    /**
     * @return \SprykerEco\Zed\Unzer\Dependency\UnzerToVaultFacadeBridge
     */
    protected function getVaultFacade(): UnzerToVaultFacadeBridge
    {
        return new UnzerToVaultFacadeBridge($this->createVaultFacadeMock());
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|\Spryker\Zed\Vault\Business\VaultFacade
     */
    protected function createVaultFacadeMock(): VaultFacade
    {
        return $this->makeEmpty(
            VaultFacade::class,
            [
                'store' => true,
                'retrieve' => UnzerZedTester::UNZER_PRIVATE_KEY,
            ],
        );
    }
}
