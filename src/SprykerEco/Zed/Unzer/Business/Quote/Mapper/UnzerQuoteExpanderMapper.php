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
        $unzerCustomerTransfer
            ->setId($quoteTransfer->getCustomerReference() . uniqid('', true))
            ->setLastname($quoteTransfer->getCustomer()->getLastName())
            ->setFirstname($quoteTransfer->getCustomer()->getFirstName())
            ->setSalutation($quoteTransfer->getCustomer()->getSalutation())
            ->setCompany($quoteTransfer->getCustomer()->getCompany())
            ->setBirthDate($quoteTransfer->getCustomer()->getDateOfBirth())
            ->setEmail($quoteTransfer->getCustomer()->getEmail())
            ->setPhone($quoteTransfer->getShippingAddress()->getPhone())
            ->setMobile($quoteTransfer->getCustomer()->getPhone())
            ->setShippingAddress(
                $this->mapAddressTransferToUnzerAddressTransfer($quoteTransfer->getShippingAddress(), new UnzerAddressTransfer())
            )
            ->setBillingAddress(
                $this->mapAddressTransferToUnzerAddressTransfer($quoteTransfer->getBillingAddress(), new UnzerAddressTransfer())
            );

        return $unzerCustomerTransfer;
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
        $unzerAddressTransfer->setCountry($addressTransfer->getIso2Code());
        $unzerAddressTransfer->setState($addressTransfer->getState());
        $unzerAddressTransfer->setCity($addressTransfer->getCity());
        $unzerAddressTransfer->setName($addressTransfer->getFirstName() . ' ' . $addressTransfer->getLastName());
        $unzerAddressTransfer->setZip($addressTransfer->getZipCode());
        $unzerAddressTransfer->setStreet($addressTransfer->getAddress1());

        return $unzerAddressTransfer;
    }
}
