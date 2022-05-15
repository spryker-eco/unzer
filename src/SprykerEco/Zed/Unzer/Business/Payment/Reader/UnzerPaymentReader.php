<?php

namespace SprykerEco\Zed\Unzer\Business\Payment\Reader;

use Generated\Shared\Transfer\OrderTransfer;
use Generated\Shared\Transfer\UnzerCredentialsConditionsTransfer;
use Generated\Shared\Transfer\UnzerCredentialsCriteriaTransfer;
use Generated\Shared\Transfer\UnzerPaymentTransfer;
use SprykerEco\Zed\Unzer\Business\ApiAdapter\UnzerPaymentAdapterInterface;
use SprykerEco\Zed\Unzer\Business\Credentials\UnzerCredentialsResolverInterface;
use SprykerEco\Zed\Unzer\Business\Payment\Mapper\UnzerPaymentMapperInterface;
use SprykerEco\Zed\Unzer\Business\Reader\UnzerReaderInterface;

class UnzerPaymentReader implements UnzerPaymentReaderInterface
{
    /**
     * @var UnzerReaderInterface
     */
    protected $unzerReader;

    /**
     * @var UnzerPaymentMapperInterface
     */
    protected $unzerPaymentMapper;

    /**
     * @var UnzerPaymentAdapterInterface
     */
    protected $unzerPaymentAdapter;

    /**
     * @var UnzerCredentialsResolverInterface
     */
    protected $unzerCredentialsResolver;

    /**
     * @param UnzerReaderInterface $unzerReader
     * @param UnzerPaymentMapperInterface $unzerPaymentMapper
     * @param UnzerPaymentAdapterInterface $unzerPaymentAdapter
     * @param UnzerCredentialsResolverInterface $unzerCredentialsResolver
     */
    public function __construct(
        UnzerReaderInterface $unzerReader,
        UnzerPaymentMapperInterface $unzerPaymentMapper,
        UnzerPaymentAdapterInterface $unzerPaymentAdapter,
        UnzerCredentialsResolverInterface $unzerCredentialsResolver
    )
    {
        $this->unzerReader = $unzerReader;
        $this->unzerPaymentMapper = $unzerPaymentMapper;
        $this->unzerPaymentAdapter = $unzerPaymentAdapter;
        $this->unzerCredentialsResolver = $unzerCredentialsResolver;
    }

    /**
     * @param OrderTransfer $orderTransfer
     *
     * @return UnzerPaymentTransfer|null
     */
    public function findUnzerPaymentForOrder(OrderTransfer $orderTransfer): ?UnzerPaymentTransfer
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
