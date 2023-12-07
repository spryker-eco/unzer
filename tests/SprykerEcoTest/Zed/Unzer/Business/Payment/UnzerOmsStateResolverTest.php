<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEcoTest\Zed\Unzer\Business\Payment;

use Codeception\Test\Unit;
use Generated\Shared\Transfer\UnzerPaymentTransfer;
use Generated\Shared\Transfer\UnzerTransactionTransfer;
use SprykerEco\Zed\Unzer\Business\Payment\OmsStateResolver\UnzerOmsStateResolver;
use SprykerEco\Zed\Unzer\UnzerConstants;
use SprykerEcoTest\Zed\Unzer\UnzerBusinessTester;

class UnzerOmsStateResolverTest extends Unit
{
    /**
     * @var \SprykerEcoTest\Zed\Unzer\UnzerBusinessTester
     */
    protected UnzerBusinessTester $tester;

    /**
     * @return void
     */
    public function getUnzerPaymentOmsStatusShouldReturnStatusAuthorizePendingWhenTransactionAuthorizePending(): void
    {
        // Arrange
        $paymentTransfer = (new UnzerPaymentTransfer())
            ->setStateId(UnzerConstants::UNZER_PAYMENT_STATUS_PENDING)
            ->addTransaction(
                (new UnzerTransactionTransfer())
                    ->setType(UnzerConstants::TRANSACTION_TYPE_AUTHORIZE)
                    ->setStatus(UnzerConstants::TRANSACTION_STATUS_PENDING),
            );

        // Act
        $omsState = (new UnzerOmsStateResolver($this->tester->createConfig()))->getUnzerPaymentOmsStatus($paymentTransfer);

        // Assert
        $this->assertSame(UnzerConstants::OMS_STATUS_AUTHORIZE_PENDING, $omsState);
    }

    /**
     * @return void
     */
    public function getUnzerPaymentOmsStatusShouldReturnStatusAuthorizeSuccessWhenTransactionAuthorizeSuccess(): void
    {
        // Arrange
        $paymentTransfer = (new UnzerPaymentTransfer())
            ->setStateId(UnzerConstants::UNZER_PAYMENT_STATUS_PENDING)
            ->addTransaction(
                (new UnzerTransactionTransfer())
                    ->setType(UnzerConstants::TRANSACTION_TYPE_AUTHORIZE)
                    ->setStatus(UnzerConstants::TRANSACTION_STATUS_SUCCESS),
            );

        // Act
        $omsState = (new UnzerOmsStateResolver($this->tester->createConfig()))->getUnzerPaymentOmsStatus($paymentTransfer);

        // Assert
        $this->assertSame(UnzerConstants::OMS_STATUS_AUTHORIZE_SUCCEEDED, $omsState);
    }

    /**
     * @return void
     */
    public function getUnzerPaymentOmsStatusShouldReturnStatusAuthorizeFailedWhenTransactionAuthorizeFailed(): void
    {
        // Arrange
        $paymentTransfer = (new UnzerPaymentTransfer())
            ->setStateId(UnzerConstants::UNZER_PAYMENT_STATUS_PENDING)
            ->addTransaction(
                (new UnzerTransactionTransfer())
                    ->setType(UnzerConstants::TRANSACTION_TYPE_AUTHORIZE)
                    ->setStatus(UnzerConstants::TRANSACTION_STATUS_PENDING),
            );

        // Act
        $omsState = (new UnzerOmsStateResolver($this->tester->createConfig()))->getUnzerPaymentOmsStatus($paymentTransfer);

        // Assert
        $this->assertSame(UnzerConstants::OMS_STATUS_AUTHORIZE_FAILED, $omsState);
    }

    /**
     * @return void
     */
    public function getUnzerPaymentOmsStatusShouldReturnStatusChargeFailedWhenTransactionChargeFailed(): void
    {
        // Arrange
        $paymentTransfer = (new UnzerPaymentTransfer())
            ->setStateId(UnzerConstants::UNZER_PAYMENT_STATUS_CANCELED)
            ->addTransaction(
                (new UnzerTransactionTransfer())
                    ->setType(UnzerConstants::TRANSACTION_TYPE_AUTHORIZE)
                    ->setStatus(UnzerConstants::TRANSACTION_STATUS_SUCCESS),
            )
            ->addTransaction(
                (new UnzerTransactionTransfer())
                    ->setType(UnzerConstants::TRANSACTION_TYPE_CHARGE)
                    ->setStatus(UnzerConstants::TRANSACTION_STATUS_ERROR),
            );

        // Act
        $omsState = (new UnzerOmsStateResolver($this->tester->createConfig()))->getUnzerPaymentOmsStatus($paymentTransfer);

        // Assert
        $this->assertSame(UnzerConstants::OMS_STATUS_CHARGE_FAILED, $omsState);
    }

    /**
     * @return void
     */
    public function getUnzerPaymentOmsStatusShouldReturnPaymentCompletedWhenPaymentCompleted(): void
    {
        // Arrange
        $paymentTransfer = (new UnzerPaymentTransfer())
            ->setStateId(UnzerConstants::UNZER_PAYMENT_STATUS_PENDING)
            ->addTransaction(
                (new UnzerTransactionTransfer())
                    ->setType(UnzerConstants::TRANSACTION_TYPE_AUTHORIZE)
                    ->setStatus(UnzerConstants::TRANSACTION_STATUS_SUCCESS),
            )
            ->addTransaction(
                (new UnzerTransactionTransfer())
                    ->setType(UnzerConstants::TRANSACTION_TYPE_CHARGE)
                    ->setStatus(UnzerConstants::TRANSACTION_STATUS_SUCCESS),
            );

        // Act
        $omsState = (new UnzerOmsStateResolver($this->tester->createConfig()))->getUnzerPaymentOmsStatus($paymentTransfer);

        // Assert
        $this->assertSame(UnzerConstants::OMS_STATUS_PAYMENT_COMPLETED, $omsState);
    }

    /**
     * @return void
     */
    public function getUnzerPaymentOmsStatusShouldThrowExceptionWhenTransactionStatusIsUnknown(): void
    {
        // Arrange
        $paymentTransfer = (new UnzerPaymentTransfer())
            ->setStateId(UnzerConstants::UNZER_PAYMENT_STATUS_PENDING)
            ->addTransaction(
                (new UnzerTransactionTransfer())
                    ->setType(UnzerConstants::TRANSACTION_TYPE_AUTHORIZE)
                    ->setStatus(UnzerConstants::TRANSACTION_STATUS_SUCCESS),
            )
            ->addTransaction(
                (new UnzerTransactionTransfer())
                    ->setType(UnzerConstants::TRANSACTION_TYPE_CHARGE)
                    ->setStatus(UnzerConstants::TRANSACTION_STATUS_SUCCESS),
            );

        // Act
        $omsState = (new UnzerOmsStateResolver($this->tester->createConfig()))->getUnzerPaymentOmsStatus($paymentTransfer);

        // Assert
        $this->assertSame(UnzerConstants::OMS_STATUS_PAYMENT_COMPLETED, $omsState);
    }
}
