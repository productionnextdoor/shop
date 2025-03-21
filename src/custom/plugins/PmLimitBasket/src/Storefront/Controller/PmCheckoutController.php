<?php declare(strict_types=1);

namespace PmLimitBasket\Storefront\Controller;

use Shopware\Core\Checkout\Cart\SalesChannel\CartService;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Storefront\Controller\StorefrontController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(defaults: ['_routeScope' => ['storefront']])]
class PmCheckoutController extends StorefrontController
{
    public function __construct(
        private readonly CartService $cartService
    ) {}

    #[Route(
        '/widgets/pmcheckout/limited',
        name: 'frontend.pm.checkout.limited',
        defaults: ['XmlHttpRequest' => true],
        options: ['seo' => 'false'],
        methods: ['GET']
    )]
    public function limited(SalesChannelContext $context): Response
    {
        return $this->json(['limited' => $this->isLimited($context)]);
    }

    private function isLimited(SalesChannelContext $context): bool
    {
        $cart = $this->cartService->getCart($context->getToken(), $context);
        $limeItemsCount = $cart->getLineItems()->count();
        if ($limeItemsCount !== 1) {
            return $limeItemsCount > 1;
        }

        $lineItemCount = $cart->getLineItems()->first()?->getQuantity() ?? 0;
        return $lineItemCount >= 1;
    }
}
