<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEcoTest\Zed\Unzer\Helper;

use Codeception\Module;
use Generated\Shared\DataBuilder\PaymentUnzerBuilder;
use Generated\Shared\DataBuilder\PaymentUnzerOrderItemBuilder;
use Generated\Shared\Transfer\PaymentUnzerOrderItemTransfer;
use Generated\Shared\Transfer\PaymentUnzerTransfer;
use Orm\Zed\Unzer\Persistence\SpyPaymentUnzer;
use Orm\Zed\Unzer\Persistence\SpyPaymentUnzerOrderItem;
use Orm\Zed\Unzer\Persistence\SpyPaymentUnzerOrderItemQuery;
use Orm\Zed\Unzer\Persistence\SpyPaymentUnzerQuery;
use SprykerTest\Shared\Testify\Helper\DataCleanupHelperTrait;
use SprykerTest\Shared\Testify\Helper\LocatorHelperTrait;

class UnzerHelper extends Module
{
    use DataCleanupHelperTrait;
    use LocatorHelperTrait;

    /**
     * @param array $override
     *
     * @return \Generated\Shared\Transfer\PaymentUnzerTransfer
     */
    public function havePaymentUnzer(array $override = []): PaymentUnzerTransfer
    {
        $paymentUnzerTransfer = (new PaymentUnzerBuilder($override))->build();
        $spyPaymentUnzer = (new SpyPaymentUnzer())->fromArray($paymentUnzerTransfer->toArray())
            ->setFkSalesOrder($paymentUnzerTransfer->getIdSalesOrder())
            ->setUnzerKeypairId($paymentUnzerTransfer->getKeypairId());

        $spyPaymentUnzer->save();

        $paymentUnzerTransfer->setIdPaymentUnzer($spyPaymentUnzer->getIdPaymentUnzer());

        $this->getDataCleanupHelper()->_addCleanup(function () use ($paymentUnzerTransfer) {
            SpyPaymentUnzerQuery::create()
                ->filterByIdPaymentUnzer($paymentUnzerTransfer->getIdPaymentUnzer())
                ->delete();
        });

        return $paymentUnzerTransfer;
    }

    /**
     * @param array $override
     *
     * @return \Generated\Shared\Transfer\PaymentUnzerOrderItemTransfer
     */
    public function havePaymentUnzerOrderItem(array $override = []): PaymentUnzerOrderItemTransfer
    {
        $paymentUnzerOrderItemTransfer = (new PaymentUnzerOrderItemBuilder($override))->build();
        $spyPaymentUnzerOrderItem = (new SpyPaymentUnzerOrderItem())->fromArray($paymentUnzerOrderItemTransfer->toArray())
            ->setFkPaymentUnzer($paymentUnzerOrderItemTransfer->getIdPaymentUnzer())
            ->setFkSalesOrderItem($paymentUnzerOrderItemTransfer->getIdSalesOrderItem());

        $spyPaymentUnzerOrderItem->save();

        $paymentUnzerOrderItemTransfer->setIdPaymentUnzerOrderItem($spyPaymentUnzerOrderItem->getIdPaymentUnzerOrderItem());

        $this->getDataCleanupHelper()->_addCleanup(function () use ($paymentUnzerOrderItemTransfer) {
            SpyPaymentUnzerOrderItemQuery::create()
                ->filterByIdPaymentUnzerOrderItem($paymentUnzerOrderItemTransfer->getIdPaymentUnzerOrderItem())
                ->delete();
        });

        return $paymentUnzerOrderItemTransfer;
    }
}
