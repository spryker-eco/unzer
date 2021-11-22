<?php

namespace SprykerEco\Zed\Unzer\Business\Payment\KeypairResolver;

use Generated\Shared\Transfer\StoreTransfer;
use Generated\Shared\Transfer\UnzerKeypairTransfer;
use SprykerEco\Zed\Unzer\Business\Exception\UnzerException;
use SprykerEco\Zed\Unzer\Business\Reader\UnzerReaderInterface;

class UnzerKeypairResolver implements UnzerKeypairResolverInterface
{
    /**
     * @var UnzerReaderInterface
     */
    protected $unzerReader;

    /**
     * @param UnzerReaderInterface $unzerReader
     */
    public function __construct(UnzerReaderInterface $unzerReader)
    {
        $this->unzerReader = $unzerReader;
    }

    /**
     * @param string $merchantReference
     * @param StoreTransfer $storeTransfer
     *
     * @return UnzerKeypairTransfer
     * @throws UnzerException
     */
    public function getUnzerKeypairByMerchantReferenceAndStore(string $merchantReference, StoreTransfer $storeTransfer): UnzerKeypairTransfer
    {
        $unzerKeypairTransfer = $this->unzerReader->getUnzerKeypairByMerchantReferenceAndStoreId($merchantReference, $storeTransfer);
        if ($unzerKeypairTransfer === null) {
            throw new UnzerException(
                sprintf(
                    'Unzer Vault key not found for merchant reference %s and store %s',
                    $merchantReference,
                    $storeTransfer->getName()
                )
            );
        }

        return $unzerKeypairTransfer;
    }

    /**
     * @param string $unzerPrimaryKeypairId
     *
     * @return UnzerKeypairTransfer
     *
     * @throws UnzerException
     */
    public function getUnzerKeypairByKeypairId(string $unzerPrimaryKeypairId): UnzerKeypairTransfer
    {
        $unzerKeypairTransfer = $this->unzerReader->getUnzerKeypairByKeypairId($unzerPrimaryKeypairId);
        if ($unzerKeypairTransfer === null) {
            throw new UnzerException( sprintf('Unzer Vault key not found by the key %s',$unzerPrimaryKeypairId));
        }

        return $unzerKeypairTransfer;
    }
}
