<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Business\Notification\Processor;

use Generated\Shared\Transfer\OrderTransfer;
use Generated\Shared\Transfer\UnzerCredentialsConditionsTransfer;
use Generated\Shared\Transfer\UnzerCredentialsCriteriaTransfer;
use Generated\Shared\Transfer\UnzerNotificationTransfer;
use Generated\Shared\Transfer\UnzerPaymentTransfer;
use SprykerEco\Zed\Unzer\Business\ApiAdapter\UnzerPaymentAdapterInterface;
use SprykerEco\Zed\Unzer\Business\Credentials\UnzerCredentialsResolverInterface;
use SprykerEco\Zed\Unzer\Business\Payment\Mapper\UnzerPaymentMapperInterface;
use SprykerEco\Zed\Unzer\Business\Payment\Saver\UnzerPaymentSaverInterface;
use SprykerEco\Zed\Unzer\Business\Reader\UnzerReaderInterface;
use SprykerEco\Zed\Unzer\UnzerConfig;

class UnzerNotificationProcessor implements UnzerNotificationProcessorInterface
{
    /**
     * @var \SprykerEco\Zed\Unzer\Business\ApiAdapter\UnzerPaymentAdapterInterface
     */
    protected $unzerPaymentAdapter;

    /**
     * @var \SprykerEco\Zed\Unzer\UnzerConfig
     */
    protected $unzerConfig;

    /**
     * @var \SprykerEco\Zed\Unzer\Business\Reader\UnzerReaderInterface
     */
    protected $unzerReader;

    /**
     * @var \SprykerEco\Zed\Unzer\Business\Payment\Mapper\UnzerPaymentMapperInterface
     */
    protected $unzerPaymentMapper;

    /**
     * @var \SprykerEco\Zed\Unzer\Business\Payment\Saver\UnzerPaymentSaverInterface
     */
    protected $unzerPaymentSaver;

    /**
     * @var \SprykerEco\Zed\Unzer\Business\Credentials\UnzerCredentialsResolverInterface
     */
    protected $unzerCredentialsResolver;

    /**
     * @param \SprykerEco\Zed\Unzer\Business\ApiAdapter\UnzerPaymentAdapterInterface $unzerPaymentAdapter
     * @param \SprykerEco\Zed\Unzer\UnzerConfig $unzerConfig
     * @param \SprykerEco\Zed\Unzer\Business\Reader\UnzerReaderInterface $unzerReader
     * @param \SprykerEco\Zed\Unzer\Business\Payment\Mapper\UnzerPaymentMapperInterface $unzerPaymentMapper
     * @param \SprykerEco\Zed\Unzer\Business\Payment\Saver\UnzerPaymentSaverInterface $unzerPaymentSaver
     * @param \SprykerEco\Zed\Unzer\Business\Credentials\UnzerCredentialsResolverInterface $unzerCredentialsResolver
     */
    public function __construct(
        UnzerPaymentAdapterInterface $unzerPaymentAdapter,
        UnzerConfig $unzerConfig,
        UnzerReaderInterface $unzerReader,
        UnzerPaymentMapperInterface $unzerPaymentMapper,
        UnzerPaymentSaverInterface $unzerPaymentSaver,
        UnzerCredentialsResolverInterface $unzerCredentialsResolver
    ) {
        $this->unzerPaymentAdapter = $unzerPaymentAdapter;
        $this->unzerConfig = $unzerConfig;
        $this->unzerReader = $unzerReader;
        $this->unzerPaymentMapper = $unzerPaymentMapper;
        $this->unzerPaymentSaver = $unzerPaymentSaver;
        $this->unzerCredentialsResolver = $unzerCredentialsResolver;
    }

    /**
     * @param \Generated\Shared\Transfer\UnzerNotificationTransfer $unzerNotificationTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerNotificationTransfer
     */
    public function processNotification(UnzerNotificationTransfer $unzerNotificationTransfer): UnzerNotificationTransfer
    {
        if (!$this->unzerConfig->isNotificationTypeEnabled($unzerNotificationTransfer->getEvent())) {
            $unzerNotificationTransfer->setIsProcessed(true);

            return $unzerNotificationTransfer;
        }

        //If payment not found - let Unzer try later
        $paymentUnzerTransfer = $this->unzerReader
            ->findPaymentUnzerByPaymentIdAndPublicKey(
                $unzerNotificationTransfer->getPaymentId(),
                $unzerNotificationTransfer->getPublicKey(),
            );

        if ($paymentUnzerTransfer === null) {
            $unzerNotificationTransfer->setIsProcessed(false);

            return $unzerNotificationTransfer;
        }

        $unzerPaymentTransfer = $this->unzerPaymentMapper->mapPaymentUnzerTransferToUnzerPaymentTransfer(
            $paymentUnzerTransfer,
            new UnzerPaymentTransfer(),
        );

        $unzerPaymentTransfer = $this->setUnzerKeypair($unzerPaymentTransfer, $paymentUnzerTransfer->getKeypairIdOrFail());
        $unzerPaymentTransfer = $this->unzerPaymentAdapter->getPaymentInfo($unzerPaymentTransfer);

        $orderItemStatus = $this->unzerConfig->mapUnzerEventToOmsStatus(
            $unzerNotificationTransfer->getEvent(),
        );

        $this->unzerPaymentSaver->saveUnzerPaymentDetails($unzerPaymentTransfer, $orderItemStatus);
        $unzerNotificationTransfer->setIsProcessed(true);

        return $unzerNotificationTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerPaymentTransfer|null
     */
    public function findUpdatedUnzerPaymentForOrder(OrderTransfer $orderTransfer): ?UnzerPaymentTransfer
    {
        $paymentUnzerTransfer = $this->unzerReader->getPaymentUnzerByOrderReference($orderTransfer->getOrderReferenceOrFail());
        if (!$paymentUnzerTransfer->getKeypairId()) {
            return null;
        }

        $unzerCredentialsConditionsTransfer = (new UnzerCredentialsConditionsTransfer())->addKeypairId($paymentUnzerTransfer->getKeypairIdOrFail());
        $unzerCredentialsCriteriaTransfer = (new UnzerCredentialsCriteriaTransfer())->setUnzerCredentialsConditions($unzerCredentialsConditionsTransfer);

        $unzerCredentialsCollectionTransfer = $this->unzerReader->findUnzerCredentialsByCriteria($unzerCredentialsCriteriaTransfer);
        if (!$unzerCredentialsCollectionTransfer) {
            return null;
        }

        $unzerPaymentTransfer = $this->unzerPaymentMapper->mapPaymentUnzerTransferToUnzerPaymentTransfer(
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
