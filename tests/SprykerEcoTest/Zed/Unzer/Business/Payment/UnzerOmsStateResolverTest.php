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
use SprykerEco\Zed\Unzer\UnzerConfig;
use SprykerEco\Zed\Unzer\UnzerConstants;

class UnzerOmsStateResolverTest extends Unit
{
    /**
     * @dataProvider unzerPaymentStatusDataProvider
     *
     * @param string $expectedOmsState
     * @param \Generated\Shared\Transfer\UnzerPaymentTransfer $paymentTransfer
     *
     * @return void
     */
    public function testShouldReturn(string $expectedOmsState, UnzerPaymentTransfer $paymentTransfer): void
    {
        // Act
        $omsState = (new UnzerOmsStateResolver(new UnzerConfig()))->getUnzerPaymentOmsStatus($paymentTransfer);

        // Assert
        $this->assertSame($expectedOmsState, $omsState);
    }

    /**
     * @return array<string, mixed>
     */
    protected function unzerPaymentStatusDataProvider(): array
    {
        return [
            'Payment status authorize pending' => [
                UnzerConstants::OMS_STATUS_AUTHORIZE_PENDING,
                (new UnzerPaymentTransfer())
                    ->setStateId(UnzerConstants::UNZER_PAYMENT_STATUS_PENDING)
                    ->addTransaction(
                        (new UnzerTransactionTransfer())
                            ->setType(UnzerConstants::TRANSACTION_TYPE_AUTHORIZE)
                            ->setStatus(UnzerConstants::TRANSACTION_STATUS_PENDING),
                    ),
            ],
            'Payment status authorize success' => [
                UnzerConstants::OMS_STATUS_AUTHORIZE_SUCCEEDED,
                (new UnzerPaymentTransfer())
                    ->setStateId(UnzerConstants::UNZER_PAYMENT_STATUS_PENDING)
                    ->addTransaction(
                        (new UnzerTransactionTransfer())
                            ->setType(UnzerConstants::TRANSACTION_TYPE_AUTHORIZE)
                            ->setStatus(UnzerConstants::TRANSACTION_STATUS_SUCCESS),
                    ),
            ],
            'Payment status authorize failed' => [
                UnzerConstants::OMS_STATUS_AUTHORIZE_FAILED,
                (new UnzerPaymentTransfer())
                    ->setStateId(UnzerConstants::UNZER_PAYMENT_STATUS_PENDING)
                    ->addTransaction(
                        (new UnzerTransactionTransfer())
                            ->setType(UnzerConstants::TRANSACTION_TYPE_AUTHORIZE)
                            ->setStatus(UnzerConstants::TRANSACTION_STATUS_PENDING),
                    ),
            ],
            'Payment status charge failed' => [
                UnzerConstants::OMS_STATUS_CHARGE_FAILED,
                (new UnzerPaymentTransfer())
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
                    ),
            ],
            'Payment status payment completed' => [
                UnzerConstants::OMS_STATUS_PAYMENT_COMPLETED,
                (new UnzerPaymentTransfer())
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
                    ),
            ],
        ];
    }
}
