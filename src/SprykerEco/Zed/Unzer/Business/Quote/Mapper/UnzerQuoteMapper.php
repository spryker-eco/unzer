<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Business\Quote\Mapper;

use Generated\Shared\Transfer\AddressTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\UnzerAddressTransfer;
use Generated\Shared\Transfer\UnzerCustomerTransfer;

class UnzerQuoteMapper implements UnzerQuoteMapperInterface
{
    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Generated\Shared\Transfer\UnzerCustomerTransfer $unzerCustomerTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerCustomerTransfer
     */
    public function mapQuoteTransferToUnzerCustomerTransfer(
        QuoteTransfer $quoteTransfer,
        UnzerCustomerTransfer $unzerCustomerTransfer
    ): UnzerCustomerTransfer {
        $shippingAddress = $this->getShippingAddressFromQuote($quoteTransfer);

        return $unzerCustomerTransfer
            ->setId($quoteTransfer->getCustomerReferenceOrFail() . uniqid('', true))
            ->setLastname($quoteTransfer->getCustomerOrFail()->getLastName())
            ->setFirstname($quoteTransfer->getCustomerOrFail()->getFirstName())
            ->setSalutation($quoteTransfer->getCustomerOrFail()->getSalutation())
            ->setCompany($quoteTransfer->getCustomerOrFail()->getCompany())
            ->setBirthDate($quoteTransfer->getCustomerOrFail()->getDateOfBirth())
            ->setEmail($quoteTransfer->getCustomerOrFail()->getEmail())
            ->setPhone($shippingAddress->getPhone())
            ->setMobile($quoteTransfer->getCustomerOrFail()->getPhone())
            ->setShippingAddress(
                $this->mapAddressTransferToUnzerAddressTransfer($shippingAddress, new UnzerAddressTransfer()),
            )
            ->setBillingAddress(
                $this->mapAddressTransferToUnzerAddressTransfer($quoteTransfer->getBillingAddressOrFail(), new UnzerAddressTransfer()),
            );
    }

    /**
     * @param \Generated\Shared\Transfer\AddressTransfer $addressTransfer
     * @param \Generated\Shared\Transfer\UnzerAddressTransfer $unzerAddressTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerAddressTransfer
     */
    protected function mapAddressTransferToUnzerAddressTransfer(
        AddressTransfer $addressTransfer,
        UnzerAddressTransfer $unzerAddressTransfer
    ): UnzerAddressTransfer {
        $name = trim(sprintf('%s %s', (string)$addressTransfer->getFirstName(), (string)$addressTransfer->getLastName()));
        return $unzerAddressTransfer->setCountry($addressTransfer->getIso2Code())
            ->setState($addressTransfer->getState())
            ->setCity($addressTransfer->getCity())
            ->setName($name)
            ->setZip($addressTransfer->getZipCode())
            ->setStreet($addressTransfer->getAddress1());
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\AddressTransfer
     */
    protected function getShippingAddressFromQuote(QuoteTransfer $quoteTransfer): AddressTransfer
    {
        foreach ($quoteTransfer->getItems() as $itemTransfer) {
            if ($itemTransfer->getShipment() && $itemTransfer->getShipmentOrFail()->getShippingAddress()) {
                return $itemTransfer->getShipmentOrFail()->getShippingAddressOrFail();
            }
        }

        return $quoteTransfer->getShippingAddressOrFail();
    }
}
