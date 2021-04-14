<?php declare(strict_types=1);

namespace ProductPromotionExam\Core\Checkout\PromotionExam\Cart;

use ProductPromotionExam\Core\Content\PromotionExam\PromotionExamEntity;
use Shopware\Core\Checkout\Cart\Cart;
use Shopware\Core\Checkout\Cart\CartBehavior;
use Shopware\Core\Checkout\Cart\CartProcessorInterface;
use Shopware\Core\Checkout\Cart\LineItem\CartDataCollection;
use Shopware\Core\Checkout\Cart\LineItem\LineItem;
use Shopware\Core\Checkout\Cart\Price\PercentagePriceCalculator;
use Shopware\Core\Checkout\Cart\Price\Struct\CalculatedPrice;
use Shopware\Core\Checkout\Cart\Price\Struct\PercentagePriceDefinition;
use Shopware\Core\Checkout\Cart\Price\Struct\PriceCollection;
use Shopware\Core\System\SalesChannel\SalesChannelContext;

class PromotionExamCartProcessor implements CartProcessorInterface {

    /**
     * @var PercentagePriceCalculator
     */
    private $calculator;

    public function __construct(PercentagePriceCalculator $calculator)
    {
        $this->calculator = $calculator;
    }

    public function process(CartDataCollection $data, Cart $original, Cart $toCalculate, SalesChannelContext $context, CartBehavior $behavior): void
    {
        // TODO print out $original and $toCalculate to see the different between them
//        dd(['original' => $original, 'toCalculate' => $toCalculate]);

        $productLineItems = $original->getLineItems()->filterType(LineItem::PRODUCT_LINE_ITEM_TYPE);
        /**
         * @var $pli LineItem
         */
        foreach ($productLineItems as $pli) {
            $promoLineItems = $pli->getChildren()->filterType(PromotionExamCartCollector::PROMO_EXAM);
            foreach ($promoLineItems as $promoLineItem) {
                $this->calculatePromoPrice($data, $promoLineItem, $pli->getPrice(), $context);
            }
            $pli->setPrice((new PriceCollection([$pli->getPrice(), $promoLineItems->getPrices()->sum()]))->sum());
        }
    }

    private function calculatePromoPrice(CartDataCollection $data, LineItem $promoLineItem, CalculatedPrice $productPrice, SalesChannelContext $context)
    {
        /**
         * @var $promoExam PromotionExamEntity
         */
        $promoExam = $data->get(PromotionExamCartCollector::PROMO_EXAM.$promoLineItem->getReferencedId());
        $priceDefinition = new PercentagePriceDefinition($promoExam->getDiscountRate() * -1, 2);
        $promoLineItem->setPriceDefinition($priceDefinition);
        $promoLineItem->setPrice($this->calculator->calculate($priceDefinition->getPercentage(), new PriceCollection([$productPrice]), $context));

    }

}
