<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Business\Notification\Processor;

use Generated\Shared\Transfer\PaymentUnzerTransfer;
use Generated\Shared\Transfer\UnzerCredentialsConditionsTransfer;
use Generated\Shared\Transfer\UnzerCredentialsCriteriaTransfer;
use Generated\Shared\Transfer\UnzerNotificationTransfer;
use Generated\Shared\Transfer\UnzerPaymentTransfer;
use SprykerEco\Zed\Unzer\Business\ApiAdapter\UnzerPaymentAdapterInterface;
use SprykerEco\Zed\Unzer\Business\Credentials\UnzerCredentialsResolverInterface;
use SprykerEco\Zed\Unzer\Business\Payment\Mapper\UnzerPaymentMapperInterface;
use SprykerEco\Zed\Unzer\Business\Payment\OmsStateResolver\UnzerOmsStateResolverInterface;
use SprykerEco\Zed\Unzer\Business\Payment\Updater\UnzerPaymentUpdaterInterface;
use SprykerEco\Zed\Unzer\Business\Reader\UnzerReaderInterface;
use SprykerEco\Zed\Unzer\UnzerConfig;

class UnzerNotificationProcessor implements UnzerNotificationProcessorInterface
{
    /**
     * @var \SprykerEco\Zed\Unzer\Business\ApiAdapter\UnzerPaymentAdapterInterface
     */
    protected UnzerPaymentAdapterInterface $unzerPaymentAdapter;

    /**
     * @var \SprykerEco\Zed\Unzer\UnzerConfig
     */
    protected UnzerConfig $unzerConfig;

    /**
     * @var \SprykerEco\Zed\Unzer\Business\Reader\UnzerReaderInterface
     */
    protected UnzerReaderInterface $unzerReader;

    /**
     * @var \SprykerEco\Zed\Unzer\Business\Payment\Mapper\UnzerPaymentMapperInterface
     */
    protected UnzerPaymentMapperInterface $unzerPaymentMapper;

    /**
     * @var \SprykerEco\Zed\Unzer\Business\Payment\Updater\UnzerPaymentUpdaterInterface
     */
    protected UnzerPaymentUpdaterInterface $unzerPaymentUpdater;

    /**
     * @var \SprykerEco\Zed\Unzer\Business\Credentials\UnzerCredentialsResolverInterface
     */
    protected UnzerCredentialsResolverInterface $unzerCredentialsResolver;

    /**
     * @var \SprykerEco\Zed\Unzer\Business\Payment\OmsStateResolver\UnzerOmsStateResolverInterface
     */
    protected UnzerOmsStateResolverInterface $unzerOmsStateResolver;

    /**
     * @param \SprykerEco\Zed\Unzer\Business\ApiAdapter\UnzerPaymentAdapterInterface $unzerPaymentAdapter
     * @param \SprykerEco\Zed\Unzer\UnzerConfig $unzerConfig
     * @param \SprykerEco\Zed\Unzer\Business\Reader\UnzerReaderInterface $unzerReader
     * @param \SprykerEco\Zed\Unzer\Business\Payment\Mapper\UnzerPaymentMapperInterface $unzerPaymentMapper
     * @param \SprykerEco\Zed\Unzer\Business\Payment\Updater\UnzerPaymentUpdaterInterface $unzerPaymentUpdater
     * @param \SprykerEco\Zed\Unzer\Business\Credentials\UnzerCredentialsResolverInterface $unzerCredentialsResolver
     * @param \SprykerEco\Zed\Unzer\Business\Payment\OmsStateResolver\UnzerOmsStateResolverInterface $unzerOmsStateResolver
     */
    public function __construct(
        UnzerPaymentAdapterInterface $unzerPaymentAdapter,
        UnzerConfig $unzerConfig,
        UnzerReaderInterface $unzerReader,
        UnzerPaymentMapperInterface $unzerPaymentMapper,
        UnzerPaymentUpdaterInterface $unzerPaymentUpdater,
        UnzerCredentialsResolverInterface $unzerCredentialsResolver,
        UnzerOmsStateResolverInterface $unzerOmsStateResolver
    ) {
        $this->unzerPaymentAdapter = $unzerPaymentAdapter;
        $this->unzerConfig = $unzerConfig;
        $this->unzerReader = $unzerReader;
        $this->unzerPaymentMapper = $unzerPaymentMapper;
        $this->unzerPaymentUpdater = $unzerPaymentUpdater;
        $this->unzerCredentialsResolver = $unzerCredentialsResolver;
        $this->unzerOmsStateResolver = $unzerOmsStateResolver;
    }

    /**
     * @param \Generated\Shared\Transfer\UnzerNotificationTransfer $unzerNotificationTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerNotificationTransfer
     */
    public function processNotification(UnzerNotificationTransfer $unzerNotificationTransfer): UnzerNotificationTransfer
    {
        if (!$this->unzerConfig->isNotificationTypeEnabled($unzerNotificationTransfer->getEventOrFail())) {
            $unzerNotificationTransfer->setIsProcessed(true);

            return $unzerNotificationTransfer;
        }

        //If payment not found - let Unzer try later
        $paymentUnzerTransfer = $this->unzerReader
            ->findPaymentUnzerByPaymentIdAndPublicKey(
                $unzerNotificationTransfer->getPaymentIdOrFail(),
                $unzerNotificationTransfer->getPublicKeyOrFail(),
            );

        if ($paymentUnzerTransfer === null) {
            $unzerNotificationTransfer->setIsProcessed(false);

            return $unzerNotificationTransfer;
        }

        $unzerPaymentTransfer = $this->prepareUnzerPaymentTransfer($paymentUnzerTransfer);

        if ($unzerPaymentTransfer->getErrors()->count() !== 0) {
            $unzerNotificationTransfer->setIsProcessed(false);

            return $unzerNotificationTransfer;
        }

        $orderItemStatus = $this->unzerOmsStateResolver->getUnzerPaymentOmsStatus($unzerPaymentTransfer);

        $this->unzerPaymentUpdater->updateUnzerPaymentDetails($unzerPaymentTransfer, $orderItemStatus);

        return $unzerNotificationTransfer->setIsProcessed(true);
    }

    /**
     * @param \Generated\Shared\Transfer\PaymentUnzerTransfer $paymentUnzerTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerPaymentTransfer
     */
    protected function prepareUnzerPaymentTransfer(PaymentUnzerTransfer $paymentUnzerTransfer): UnzerPaymentTransfer
    {
        $unzerPaymentTransfer = $this->unzerPaymentMapper
            ->mapPaymentUnzerTransferToUnzerPaymentTransfer(
                $paymentUnzerTransfer,
                new UnzerPaymentTransfer(),
            );

        $unzerPaymentTransfer = $this->setUnzerKeypair($unzerPaymentTransfer, $paymentUnzerTransfer->getKeypairIdOrFail());

        return $this->unzerPaymentAdapter->getPaymentInfo($unzerPaymentTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\UnzerPaymentTransfer $unzerPaymentTransfer
     * @param string $keypairId
     *
     * @return \Generated\Shared\Transfer\UnzerPaymentTransfer
     */
    protected function setUnzerKeypair(UnzerPaymentTransfer $unzerPaymentTransfer, string $keypairId): UnzerPaymentTransfer
    {
        $unzerCredentialsCriteriaTransfer = (new UnzerCredentialsCriteriaTransfer())
            ->setUnzerCredentialsConditions(
                (new UnzerCredentialsConditionsTransfer())->addKeypairId($keypairId),
            );

        $unzerCredentialsTransfer = $this->unzerCredentialsResolver->resolveUnzerCredentialsByCriteriaTransfer($unzerCredentialsCriteriaTransfer);
        $unzerPaymentTransfer->setUnzerKeypair($unzerCredentialsTransfer->getUnzerKeypairOrFail());

        return $unzerPaymentTransfer;
    }
}
