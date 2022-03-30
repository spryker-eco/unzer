<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Yves\Unzer\Form;

use Generated\Shared\Transfer\PaymentTransfer;
use SprykerEco\Shared\Unzer\UnzerConfig;

class MarketplaceBankTransferSubForm extends BankTransferSubForm
{
    /**
     * @var string
     */
    public const TEMPLATE_VIEW_PATH = 'views/marketplace-bank-transfer/marketplace-bank-transfer';

    /**
     * @return string
     */
    public function getName(): string
    {
        return PaymentTransfer::UNZER_MARKETPLACE_BANK_TRANSFER;
    }

    /**
     * @return string
     */
    public function getPropertyPath(): string
    {
        return PaymentTransfer::UNZER_MARKETPLACE_BANK_TRANSFER;
    }

    /**
     * @return string
     */
    public function getTemplatePath(): string
    {
        return UnzerConfig::PAYMENT_PROVIDER_NAME . DIRECTORY_SEPARATOR . static::TEMPLATE_VIEW_PATH;
    }
}
