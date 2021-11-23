<?php

namespace SprykerEcoTest\Zed\Unzer\Business;

use Codeception\TestCase\Test;
use SprykerEco\Zed\CrefoPay\Business\CrefoPayBusinessFactory;
use SprykerEco\Zed\CrefoPay\Business\CrefoPayFacade;
use SprykerEco\Zed\Unzer\Business\UnzerBusinessFactory;
use SprykerEco\Zed\Unzer\Business\UnzerFacadeInterface;

class UnzerFacadeBaseTest extends Test
{
    /**
     * @var \SprykerEcoTest\Zed\Unzer\UnzerZedTester
     */
    protected $tester;

    /**
     * @var UnzerFacadeInterface
     */
    protected $facade;

    /**
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->facade = $this->tester->getFacade();
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\SprykerEco\Zed\Unzer\Business\UnzerBusinessFactory
     */
    protected function createFactoryMock(): UnzerBusinessFactory
    {
        $builder = $this->getMockBuilder(UnzerBusinessFactory::class);
        $builder->setMethods(
            [
                'getConfig',
                'getRepository',
                'getEntityManager',
                'getCalculationFacade',
                'getQuoteClient',
                'getRefundFacade',
                'getStoreFacade',
                'getLocaleFacade',
                'getSalesFacade',
            ]
        );

        $stub = $builder->getMock();
        $stub->method('getConfig')
            ->willReturn($this->tester->createConfig());
        $stub->method('getRepository')
            ->willReturn($this->tester->createRepository());
        $stub->method('getEntityManager')
            ->willReturn($this->tester->createEntityManager());

        $stub->method('getCalculationFacade')
            ->willReturn($this->createCalculationFacade());
        $stub->method('getQuoteClient')
            ->willReturn($this->createQuoteClient());
        $stub->method('getRefundFacade')
            ->willReturn($this->createRefundFacade());


        return $stub;
    }

    protected function createCalculationFacade()
    {
    }
}
