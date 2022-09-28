<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

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
     * @var \SprykerEco\Zed\Unzer\Business\Reader\UnzerReaderInterface
     */
    protected $unzerReader;

    /**
     * @var \SprykerEco\Zed\Unzer\Business\Payment\Mapper\UnzerPaymentMapperInterface
     */
    protected $unzerPaymentMapper;

    /**
     * @var \SprykerEco\Zed\Unzer\Business\ApiAdapter\UnzerPaymentAdapterInterface
     */
    protected $unzerPaymentAdapter;

    /**
     * @var \SprykerEco\Zed\Unzer\Business\Credentials\UnzerCredentialsResolverInterface
     */
    protected $unzerCredentialsResolver;

    /**
     * @param \SprykerEco\Zed\Unzer\Business\Reader\UnzerReaderInterface $unzerReader
     * @param \SprykerEco\Zed\Unzer\Business\Payment\Mapper\UnzerPaymentMapperInterface $unzerPaymentMapper
     * @param \SprykerEco\Zed\Unzer\Business\ApiAdapter\UnzerPaymentAdapterInterface $unzerPaymentAdapter
     * @param \SprykerEco\Zed\Unzer\Business\Credentials\UnzerCredentialsResolverInterface $unzerCredentialsResolver
     */
    public function __construct(
        UnzerReaderInterface $unzerReader,
        UnzerPaymentMapperInterface $unzerPaymentMapper,
        UnzerPaymentAdapterInterface $unzerPaymentAdapter,
        UnzerCredentialsResolverInterface $unzerCredentialsResolver
    ) {
        $this->unzerReader = $unzerReader;
        $this->unzerPaymentMapper = $unzerPaymentMapper;
        $this->unzerPaymentAdapter = $unzerPaymentAdapter;
        $this->unzerCredentialsResolver = $unzerCredentialsResolver;
    }

    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerPaymentTransfer|null
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
        $unzerPaymentTransfer = $this->unzerPaymentAdapter->getPaymentInfo($unzerPaymentTransfer);

        if ($unzerPaymentTransfer->getErrors()->count() !== 0) {
            return null;
        }

        return $unzerPaymentTransfer;
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
