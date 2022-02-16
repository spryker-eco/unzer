<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Yves\Unzer\Controller;

use Generated\Shared\Transfer\OrderTransfer;
use Generated\Shared\Transfer\UnzerNotificationTransfer;
use Spryker\Yves\Kernel\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @method \SprykerEco\Yves\Unzer\UnzerFactory getFactory()
 */
class CallbackController extends AbstractController
{
    /**
     * @uses \SprykerEco\Zed\Unzer\UnzerConstants::UNZER_PAYMENT_STATUS_CANCELED
     *
     * @var int
     */
    protected const UNZER_PAYMENT_STATUS_CANCELED = 2;

    /**
     * @uses \SprykerShop\Yves\CheckoutPage\Plugin\Router\CheckoutPageRouteProviderPlugin::ROUTE_NAME_CHECKOUT_ERROR
     *
     * @var string
     */
    protected const ROUTE_NAME_CHECKOUT_ERROR = 'checkout-error';

    /**
     * @uses \SprykerShop\Yves\CheckoutPage\Plugin\Router\CheckoutPageRouteProviderPlugin::ROUTE_NAME_CHECKOUT_SUCCESS
     *
     * @var string
     */
    public const ROUTE_NAME_CHECKOUT_SUCCESS = 'checkout-success';

    /**
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function paymentResultAction(): RedirectResponse
    {
        $quoteTransfer = $this->getFactory()->getQuoteClient()->getQuote();
        $orderTransfer = (new OrderTransfer())->setOrderReference($quoteTransfer->getOrderReferenceOrFail());

        $unzerPaymentTransfer = $this->getFactory()->getUnzerClient()->findUpdatedUnzerPaymentForOrderAction($orderTransfer);

        if ($unzerPaymentTransfer->getStateId() === static::UNZER_PAYMENT_STATUS_CANCELED) {
            return $this->redirectResponseInternal(static::ROUTE_NAME_CHECKOUT_ERROR);
        }

        return $this->redirectResponseInternal(static::ROUTE_NAME_CHECKOUT_SUCCESS);
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function notificationAction(Request $request): JsonResponse
    {
        $requestData = $this->getFactory()->getUtilEncodingService()->decodeJson($request->getContent(), true);
        $unzerNotificationTransfer = (new UnzerNotificationTransfer())->fromArray($requestData, true);

        $unzerNotificationTransfer = $this->getFactory()->getUnzerClient()->processNotification($unzerNotificationTransfer);

        $responseCode = $unzerNotificationTransfer->getIsProcessedOrFail() ? Response::HTTP_OK : Response::HTTP_BAD_REQUEST;

        return $this->jsonResponse(null, $responseCode);
    }
}
