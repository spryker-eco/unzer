<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEcoTest\Zed\Unzer\Business;

use Codeception\TestCase\Test;
use Generated\Shared\Transfer\LocaleTransfer;
use Spryker\Service\UtilText\UtilTextService;
use Spryker\Zed\Locale\Business\LocaleFacade;
use Spryker\Zed\Vault\Business\VaultFacade;
use SprykerEco\Zed\Unzer\Business\UnzerBusinessFactory;
use SprykerEco\Zed\Unzer\Dependency\UnzerToLocaleFacadeBridge;
use SprykerEco\Zed\Unzer\Dependency\UnzerToMerchantFacadeBridge;
use SprykerEco\Zed\Unzer\Dependency\UnzerToMerchantFacadeInterface;
use SprykerEco\Zed\Unzer\Dependency\UnzerToPaymentFacadeBridge;
use SprykerEco\Zed\Unzer\Dependency\UnzerToPaymentFacadeInterface;
use SprykerEco\Zed\Unzer\Dependency\UnzerToUnzerApiFacadeBridge;
use SprykerEco\Zed\Unzer\Dependency\UnzerToUtilTextServiceBridge;
use SprykerEco\Zed\Unzer\Dependency\UnzerToUtilTextServiceInterface;
use SprykerEco\Zed\Unzer\Dependency\UnzerToVaultFacadeBridge;
use SprykerEco\Zed\UnzerApi\Business\UnzerApiFacade;
use SprykerEcoTest\Zed\Unzer\UnzerBusinessTester;

class UnzerFacadeBaseTest extends Test
{
    /**
     * @var string
     */
    protected const UNZER_BANK_TRANSFER_STATE_MACHINE_PROCESS_NAME = 'UnzerBankTransfer01';

    /**
     * @var string
     */
    protected const UNZER_SOFORT_STATE_MACHINE_PROCESS_NAME = 'UnzerSofort01';

    /**
     * @var string
     */
    protected const UNZER_CREDIT_CARD_STATE_MACHINE_PROCESS_NAME = 'UnzerCreditCard01';

    /**
     * @var string
     */
    protected const UNZER_MARKETPLACE_BANK_TRANSFER_STATE_MACHINE_PROCESS_NAME = 'UnzerMarketplaceBankTransfer01';

    /**
     * @var string
     */
    protected const UNZER_MARKETPLACE_SOFORT_STATE_MACHINE_PROCESS_NAME = 'UnzerMarketplaceSofort01';

    /**
     * @var string
     */
    protected const UNZER_MARKETPLACE_CREDIT_CARD_STATE_MACHINE_PROCESS_NAME = 'UnzerMarketplaceCreditCard01';

    /**
     * @var \SprykerEcoTest\Zed\Unzer\UnzerBusinessTester
     */
    protected $tester;

    /**
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->tester->setFacade(
            $this->tester->getLocator()->unzer()->facade()->setFactory($this->createFactoryMock()),
        );
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
                'getLocaleFacade',
                'getUtilTextService',
                'getMerchantFacade',
                'getPaymentFacade',
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
        $stub->method('getLocaleFacade')
            ->willReturn($this->getLocaleFacade());
        $stub->method('getUtilTextService')
            ->willReturn($this->getUtilTextService());
        $stub->method('getMerchantFacade')
            ->willReturn($this->getMerchantFacade());
        $stub->method('getPaymentFacade')
            ->willReturn($this->getPaymentFacade());

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
     * @return \PHPUnit\Framework\MockObject\MockObject|\SprykerEco\Zed\UnzerApi\Business\UnzerApiFacade
     */
    protected function createUnzerApiFacadeMock(): UnzerApiFacade
    {
        return $this->makeEmpty(
            UnzerApiFacade::class, function () {
            return
                [
                    'performSetNotificationUrlApiCall' => $this->tester->createUnzerApiResponseTransfer(),
                    'performCreateCustomerApiCall' => $this->tester->createUnzerApiResponseTransfer(),
                    'performUpdateCustomerApiCall' => $this->tester->createUnzerApiResponseTransfer(),
                    'performCreateMetadataApiCall' => $this->tester->createUnzerApiResponseTransfer(),
                    'performCreateBasketApiCall' => $this->tester->createUnzerApiResponseTransfer(),
                    'performCreateMarketplaceBasketApiCall' => $this->tester->createUnzerApiResponseTransfer(),
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
                    'performGetPaymentMethodsApiCall' => $this->tester->createUnzerApiResponseTransfer(),
                ];
        }
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
                'retrieve' => UnzerBusinessTester::UNZER_PRIVATE_KEY,
            ],
        );
    }

    /**
     * @return \SprykerEco\Zed\Unzer\Dependency\UnzerToLocaleFacadeBridge
     */
    protected function getLocaleFacade(): UnzerToLocaleFacadeBridge
    {
        return new UnzerToLocaleFacadeBridge($this->createLocaleFacadeMock());
    }

    /**
     * @return \SprykerEco\Zed\Unzer\Dependency\UnzerToUtilTextServiceInterface
     */
    protected function getUtilTextService(): UnzerToUtilTextServiceInterface
    {
        return new UnzerToUtilTextServiceBridge($this->createUtilTextServiceMock());
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|\Spryker\Zed\Locale\Business\LocaleFacade
     */
    protected function createLocaleFacadeMock(): LocaleFacade
    {
        return $this->makeEmpty(
            LocaleFacade::class,
            [
                'getCurrentLocale' => (new LocaleTransfer()),
            ],
        );
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|\Spryker\Service\UtilText\UtilTextService|mixed
     */
    protected function createUtilTextServiceMock()
    {
        return $this->makeEmpty(
            UtilTextService::class,
            [
                'generateUniqueId' => function () {
                    return uniqid('', true);
                },
            ],
        );
    }

    /**
     * @return \SprykerEco\Zed\Unzer\Dependency\UnzerToMerchantFacadeInterface
     */
    protected function getMerchantFacade(): UnzerToMerchantFacadeInterface
    {
        return new UnzerToMerchantFacadeBridge(
            $this->tester->getLocator()->merchant()->facade(),
        );
    }

    /**
     * @return \SprykerEco\Zed\Unzer\Dependency\UnzerToPaymentFacadeInterface
     */
    protected function getPaymentFacade(): UnzerToPaymentFacadeInterface
    {
        return new UnzerToPaymentFacadeBridge(
            $this->tester->getLocator()->payment()->facade(),
        );
    }
}
