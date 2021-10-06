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

class UnzerQuoteExpanderMapper implements UnzerQuoteExpanderMapperInterface
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
            ->setId($quoteTransfer->getCustomerReference() . uniqid('', true))
            ->setLastname($quoteTransfer->getCustomer()->getLastName())
            ->setFirstname($quoteTransfer->getCustomer()->getFirstName())
            ->setSalutation($quoteTransfer->getCustomer()->getSalutation())
            ->setCompany($quoteTransfer->getCustomer()->getCompany())
            ->setBirthDate($quoteTransfer->getCustomer()->getDateOfBirth())
            ->setEmail($quoteTransfer->getCustomer()->getEmail())
            ->setPhone($shippingAddress->getPhone())
            ->setMobile($quoteTransfer->getCustomer()->getPhone())
            ->setShippingAddress(
                $this->mapAddressTransferToUnzerAddressTransfer($shippingAddress, new UnzerAddressTransfer())
            )
            ->setBillingAddress(
                $this->mapAddressTransferToUnzerAddressTransfer($quoteTransfer->getBillingAddress(), new UnzerAddressTransfer())
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
        return $unzerAddressTransfer->setCountry($addressTransfer->getIso2Code())
            ->setState($addressTransfer->getState())
            ->setCity($addressTransfer->getCity())
            ->setName($addressTransfer->getFirstName() . ' ' . $addressTransfer->getLastName())
            ->setZip($addressTransfer->getZipCode())
            ->setStreet($addressTransfer->getAddress1());
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\AddressTransfer|null
     */
    protected function getShippingAddressFromQuote(QuoteTransfer $quoteTransfer): ?AddressTransfer
    {
        if ($quoteTransfer->getShippingAddress()) {
            return $quoteTransfer->getShippingAddress();
        }

        foreach ($quoteTransfer->getItems() as $itemTransfer) {
            return $itemTransfer->getShipment()->getShippingAddress();
        }

        return null;
    }
}
