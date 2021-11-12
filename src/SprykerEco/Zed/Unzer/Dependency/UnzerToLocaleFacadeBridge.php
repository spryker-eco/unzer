<?php

namespace SprykerEco\Zed\Unzer\Dependency;

use Spryker\Zed\Locale\Business\LocaleFacadeInterface;

class UnzerToLocaleFacadeBridge implements UnzerToLocaleFacadeInterface
{
    /**
     * @var LocaleFacadeInterface
     */
    protected $localeFacade;

    /**
     * @param LocaleFacadeInterface $localeFacade
     */
    public function __construct($localeFacade)
    {
        $this->localeFacade = $localeFacade;
    }

    /**
     * @return \Generated\Shared\Transfer\LocaleTransfer
     */
    public function getCurrentLocale()
    {
        return $this->localeFacade->getCurrentLocale();
    }
}
