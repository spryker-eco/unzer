<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Business\Payment\KeypairResolver;

use Generated\Shared\Transfer\StoreTransfer;
use Generated\Shared\Transfer\UnzerCredentialsConditionsTransfer;
use Generated\Shared\Transfer\UnzerCredentialsCriteriaTransfer;
use Generated\Shared\Transfer\UnzerKeypairTransfer;
use SprykerEco\Zed\Unzer\Business\Exception\UnzerException;
use SprykerEco\Zed\Unzer\Business\Reader\UnzerReaderInterface;

class UnzerKeypairResolver implements UnzerKeypairResolverInterface
{
    /**
     * @var \SprykerEco\Zed\Unzer\Business\Reader\UnzerReaderInterface
     */
    protected $unzerReader;

    /**
     * @param \SprykerEco\Zed\Unzer\Business\Reader\UnzerReaderInterface $unzerReader
     */
    public function __construct(UnzerReaderInterface $unzerReader)
    {
        $this->unzerReader = $unzerReader;
    }

    /**
     * @param string $merchantReference
     * @param \Generated\Shared\Transfer\StoreTransfer $storeTransfer
     *
     * @throws \SprykerEco\Zed\Unzer\Business\Exception\UnzerException
     *
     * @return \Generated\Shared\Transfer\UnzerKeypairTransfer
     */
    public function getUnzerKeypairByMerchantReferenceAndStore(string $merchantReference, StoreTransfer $storeTransfer): UnzerKeypairTransfer
    {
        $unzerCredentialsCriteriaTransfer = (new UnzerCredentialsCriteriaTransfer())
            ->setUnzerCredentialsConditions(
                (new UnzerCredentialsConditionsTransfer())
                    ->addMerchantReference($merchantReference)
                    ->addStoreName($storeTransfer->getName()),
            );

        $unzerCredentialsTransfer = $this->unzerReader->getUnzerCredentialsByCriteria($unzerCredentialsCriteriaTransfer);
        if ($unzerCredentialsTransfer === null || $unzerCredentialsTransfer->getUnzerKeypair() === null) {
            throw new UnzerException(
                sprintf(
                    'UnzerKeypair not found for merchant reference %s and store %s',
                    $merchantReference,
                    $storeTransfer->getName(),
                ),
            );
        }

        return $unzerCredentialsTransfer->getUnzerKeypair();
    }

    /**
     * @param string $unzerKeypairId
     *
     * @throws \SprykerEco\Zed\Unzer\Business\Exception\UnzerException
     *
     * @return \Generated\Shared\Transfer\UnzerKeypairTransfer
     */
    public function getUnzerKeypairByKeypairId(string $unzerKeypairId): UnzerKeypairTransfer
    {
        $unzerCredentialsCriteriaTransfer = (new UnzerCredentialsCriteriaTransfer())
            ->setUnzerCredentialsConditions((new UnzerCredentialsConditionsTransfer())->addKeypairId($unzerKeypairId));

        $unzerCredentialsTransfer = $this->unzerReader->getUnzerCredentialsByCriteria($unzerCredentialsCriteriaTransfer);
        if ($unzerCredentialsTransfer === null || $unzerCredentialsTransfer->getUnzerKeypair() === null) {
            throw new UnzerException(sprintf('UnzerKeypair not found by the key %s', $unzerKeypairId));
        }

        return $unzerCredentialsTransfer->getUnzerKeypair();
    }
}
