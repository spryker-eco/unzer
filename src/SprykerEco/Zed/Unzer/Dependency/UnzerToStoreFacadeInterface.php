<?php

namespace SprykerEco\Zed\Unzer\Dependency;

interface UnzerToStoreFacadeInterface
{
    /**
     * @return \Generated\Shared\Transfer\StoreTransfer
     */
    public function getCurrentStore();
}
