<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Yves\Unzer\Form\DataProvider;

use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\UnzerPaymentResourceTransfer;
use Generated\Shared\Transfer\UnzerPaymentTransfer;
use Spryker\Shared\Kernel\Transfer\AbstractTransfer;
use SprykerEco\Shared\Unzer\UnzerConstants;
use SprykerEco\Yves\Unzer\Dependency\Client\UnzerToQuoteClientInterface;

class CreditCardFormDataProvider extends AbstractFormDataProvider
{
    /**
     * @var string
     *
     * @uses \SprykerEco\Yves\Unzer\Form\CreditCardSubForm::OPTION_PUBLIC_KEY
     */
    protected const OPTION_PUBLIC_KEY = 'public_key';

    /**
     * @var \SprykerEco\Yves\Unzer\Dependency\Client\UnzerToQuoteClientInterface
     */
    protected $quoteClient;

    /**
     * @param \SprykerEco\Yves\Unzer\Dependency\Client\UnzerToQuoteClientInterface $quoteClient
     */
    public function __construct(UnzerToQuoteClientInterface $quoteClient)
    {
        $this->quoteClient = $quoteClient;
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return array<string, mixed>
     */
    public function getOptions(AbstractTransfer $quoteTransfer): array
    {
        if ($quoteTransfer->getUnzerCredentialsOrFail()->getTypeOrFail() !== UnzerConstants::UNZER_CREDENTIALS_TYPE_STANDARD) {
            return [];
        }

        return [
            static::OPTION_PUBLIC_KEY => $quoteTransfer->getUnzerCredentialsOrFail()->getUnzerKeypairOrFail()->getPublicKeyOrFail(),
        ];
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\QuoteTransfer
     */
    public function getData(AbstractTransfer $quoteTransfer): QuoteTransfer
    {
        $quoteTransfer = $this->updateQuoteWithPaymentData($quoteTransfer);
        $quoteTransfer->getPaymentOrFail()->setUnzerCreditCard(
            (new UnzerPaymentTransfer())->setPaymentResource(
                (new UnzerPaymentResourceTransfer()),
            ),
        );

        $this->quoteClient->setQuote($quoteTransfer);

        return $quoteTransfer;
    }
}
