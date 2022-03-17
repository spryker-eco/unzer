<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Business\Quote\Mapper;

use Generated\Shared\Transfer\AddressTransfer;
use Generated\Shared\Transfer\CustomerTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\UnzerAddressTransfer;
use Generated\Shared\Transfer\UnzerCustomerTransfer;
use SprykerEco\Shared\Unzer\UnzerConfig;

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
        $customerTransfer = $quoteTransfer->getCustomerOrFail();

        return $unzerCustomerTransfer
            ->setId($quoteTransfer->getCustomerReferenceOrFail() . uniqid('', true))
            ->setLastname($customerTransfer->getLastNameOrFail())
            ->setFirstname($customerTransfer->getFirstNameOrFail())
            ->setSalutation($this->mapSalutationToUnzerSalutation($customerTransfer))
            ->setCompany((string)$customerTransfer->getCompany())
            ->setBirthDate($customerTransfer->getDateOfBirth())
            ->setEmail((string)$customerTransfer->getEmail())
            ->setPhone((string)$shippingAddress->getPhone())
            ->setMobile((string)$customerTransfer->getPhone())
            ->setShippingAddress(
                $this->mapAddressTransferToUnzerAddressTransfer($shippingAddress, new UnzerAddressTransfer()),
            )
            ->setBillingAddress(
                $this->mapAddressTransferToUnzerAddressTransfer($quoteTransfer->getBillingAddressOrFail(), new UnzerAddressTransfer()),
            );
    }

    /**
     * @param \Generated\Shared\Transfer\CustomerTransfer $customerTransfer
     *
     * @return string
     */
    protected function mapSalutationToUnzerSalutation(CustomerTransfer $customerTransfer): string
    {
        if ($customerTransfer->getSalutation() && array_key_exists($customerTransfer->getSalutation(), UnzerConfig::SALUTATION_MAP)) {
            return UnzerConfig::SALUTATION_MAP[$customerTransfer->getSalutation()];
        }

        return UnzerConfig::SALUTATION_DEFAULT;
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

        return $unzerAddressTransfer->setCountry((string)$addressTransfer->getIso2Code())
            ->setState((string)$addressTransfer->getState())
            ->setCity((string)$addressTransfer->getCity())
            ->setName($name)
            ->setZip((string)$addressTransfer->getZipCode())
            ->setStreet((string)$addressTransfer->getAddress1());
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
