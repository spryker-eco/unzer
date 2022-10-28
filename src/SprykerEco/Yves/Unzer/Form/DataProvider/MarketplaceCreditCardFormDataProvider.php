<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Yves\Unzer\Form\DataProvider;

use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\UnzerMarketplacePaymentCredentialsResolverCriteriaTransfer;
use Generated\Shared\Transfer\UnzerPaymentResourceTransfer;
use Generated\Shared\Transfer\UnzerPaymentTransfer;
use Spryker\Shared\Kernel\Transfer\AbstractTransfer;
use SprykerEco\Client\Unzer\UnzerClientInterface;
use SprykerEco\Shared\Unzer\UnzerConfig;
use SprykerEco\Shared\Unzer\UnzerConstants;
use SprykerEco\Yves\Unzer\Dependency\Client\UnzerToQuoteClientInterface;

class MarketplaceCreditCardFormDataProvider extends AbstractFormDataProvider
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
    protected UnzerToQuoteClientInterface $quoteClient;

    /**
     * @var \SprykerEco\Client\Unzer\UnzerClientInterface
     */
    protected UnzerClientInterface $unzerClient;

    /**
     * @param \SprykerEco\Yves\Unzer\Dependency\Client\UnzerToQuoteClientInterface $quoteClient
     * @param \SprykerEco\Client\Unzer\UnzerClientInterface $unzerClient
     */
    public function __construct(
        UnzerToQuoteClientInterface $quoteClient,
        UnzerClientInterface $unzerClient
    ) {
        $this->quoteClient = $quoteClient;
        $this->unzerClient = $unzerClient;
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return array<string, mixed>
     */
    public function getOptions(AbstractTransfer $quoteTransfer): array
    {
        if ($quoteTransfer->getUnzerCredentialsOrFail()->getTypeOrFail() === UnzerConstants::UNZER_CREDENTIALS_TYPE_STANDARD) {
            return [];
        }

        $unzerMarketplacePaymentCredentialsResolverCriteriaTransfer = (new UnzerMarketplacePaymentCredentialsResolverCriteriaTransfer())
            ->setQuote($quoteTransfer)
            ->setPaymentMethodKey(UnzerConfig::PAYMENT_METHOD_KEY_MARKETPLACE_CREDIT_CARD);

        return [
            static::OPTION_PUBLIC_KEY => $this->unzerClient
                ->resolveMarketplacePaymentUnzerCredentials($unzerMarketplacePaymentCredentialsResolverCriteriaTransfer)
                ->getUnzerKeypairOrFail()
                ->getPublicKey(),
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
        $quoteTransfer->getPaymentOrFail()->setUnzerMarketplaceCreditCard(
            (new UnzerPaymentTransfer())->setPaymentResource(
                (new UnzerPaymentResourceTransfer()),
            ),
        );

        $this->quoteClient->setQuote($quoteTransfer);

        return $quoteTransfer;
    }
}
