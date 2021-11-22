<?php

namespace SprykerEco\Zed\Unzer\Dependency;

use Spryker\Zed\Store\Business\StoreFacadeInterface;

class UnzerToStoreFacadeBridge implements UnzerToStoreFacadeInterface
{
    /**
     * @var StoreFacadeInterface
     */
    protected $storeFacade;

    /**
     * @param StoreFacadeInterface $storeFacade
     */
    public function __construct($storeFacade)
    {
        $this->storeFacade = $storeFacade;
    }

    /**
     * @return \Generated\Shared\Transfer\StoreTransfer|void
     */
    public function getCurrentStore()
    {
        return $this->storeFacade->getCurrentStore();
    }
}
