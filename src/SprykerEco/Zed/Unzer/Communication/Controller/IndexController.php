<?php

namespace SprykerEco\Zed\Unzer\Communication\Controller;

use Spryker\Zed\Kernel\Communication\Controller\AbstractController;

/**
 * @method \Pyz\Zed\Unzer\Business\UnzerFacade getFacade()
 * @method \Pyz\Zed\Unzer\Communication\UnzerCommunicationFactory getFactory()
 * @method \Pyz\Zed\Unzer\Persistence\UnzerQueryContainer getQueryContainer()
 */
class IndexController extends AbstractController
{

    /**
     * @return array
     */
    public function indexAction()
    {
        return $this->viewResponse([
            'test' => 'Greetings!',
        ]);
    }

}
