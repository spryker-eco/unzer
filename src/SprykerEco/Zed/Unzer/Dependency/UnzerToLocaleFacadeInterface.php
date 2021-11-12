<?php

namespace SprykerEco\Zed\Unzer\Dependency;

interface UnzerToLocaleFacadeInterface
{
    /**
     * @return \Generated\Shared\Transfer\LocaleTransfer
     */
    public function getCurrentLocale();
}
