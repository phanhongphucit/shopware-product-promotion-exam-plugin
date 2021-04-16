<?php declare(strict_types=1);

namespace ProductPromotionExam\Core\Checkout\PromotionExam\Cart;

use ProductPromotionExam\Core\Content\PromotionExam\PromotionExamCollection;
use ProductPromotionExam\Core\Content\PromotionExam\PromotionExamEntity;
use Shopware\Core\Checkout\Cart\Cart;
use Shopware\Core\Checkout\Cart\CartBehavior;
use Shopware\Core\Checkout\Cart\CartDataCollectorInterface;
use Shopware\Core\Checkout\Cart\LineItem\CartDataCollection;
use Shopware\Core\Checkout\Cart\LineItem\LineItem;
use Shopware\Core\Checkout\Promotion\PromotionEntity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsAnyFilter;
use Shopware\Core\System\SalesChannel\SalesChannelContext;

class PromotionExamCartCollector implements CartDataCollectorInterface {

    public const PROMO_EXAM = 'promo_exam';

    /**
     * @var EntityRepositoryInterface
     */
    private $promoRepository;

    public function __construct(EntityRepositoryInterface $promoRepository)
    {
        $this->promoRepository = $promoRepository;
    }

    public function collect(CartDataCollection $data, Cart $original, SalesChannelContext $context, CartBehavior $behavior): void
    {
        $productLineItems = $original->getLineItems()->filterType(LineItem::PRODUCT_LINE_ITEM_TYPE);
        $productIds = array_map(function($lineItem) {return $lineItem->getReferencedId();}, $productLineItems->getElements());

        $promoExams = $this->fetchPromoExamsByProductIds($productIds, $context);

        // Store promotions to data
        foreach ($promoExams as $promo) {
            $data->set(self::PROMO_EXAM.$promo->getId(), $promo);
        }

        // Add promotionLineItems to productLineItem
        $productPromos = $this->getMapProductId2PromoExams($promoExams, $context);
        foreach ($productLineItems as $pli) {
            $this->addPromoLineItems($pli, $productPromos[$pli->getReferencedId()], $data);
        }
    }

    private function getMapProductId2PromoExams(PromotionExamCollection $promoExams, SalesChannelContext $context): array {
        $result = [];
        foreach ($promoExams->getIterator() as $promo) {
            $result[$promo->getProductId()][] = $promo;
        }
        return $result;
    }

    /**
     * @param array $productIds
     * @param SalesChannelContext $context
     * @return PromotionExamCollection
     */
    private function fetchPromoExamsByProductIds(array $productIds, SalesChannelContext $context): PromotionExamCollection
    {
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsAnyFilter('productId', $productIds));
        return $this->promoRepository->search($criteria, $context->getContext())->getEntities();
    }

    /**
     * @param LineItem $productLineItem
     * @param LineItem $promoLineItem
     * @param PromotionEntity $pe
     */
    private function enrichPromotion(LineItem $productLineItem, LineItem $promoLineItem, PromotionExamEntity $pe)
    {
        $promoLineItem->setLabel($pe->getName().' (-'.$pe->getDiscountRate().'%)');
        $promoLineItem->setGood(false);
        $promoLineItem->setStackable(true);

    }

    /**
     * @param LineItem $productLineItem
     * @param array $promoExams
     */
    private function addPromoLineItems(LineItem $productLineItem, array $promoExams, CartDataCollection $data)
    {
        foreach ($promoExams as $pe) {
            $data->get(self::PROMO_EXAM.$pe->getId());

            $promoLineItemId = $pe->getId() . self::PROMO_EXAM;
            $promoLineItem = $productLineItem->getChildren()->get($promoLineItemId);
            if(!$promoLineItem) {
                $promoLineItem = new LineItem($promoLineItemId, self::PROMO_EXAM, $pe->getId(), $productLineItem->getQuantity());
                $productLineItem->addChild($promoLineItem);
            } else {
                $promoLineItem->setQuantity($productLineItem->getQuantity());
            }
            $this->enrichPromotion($productLineItem, $promoLineItem, $pe);
        }
    }


}
