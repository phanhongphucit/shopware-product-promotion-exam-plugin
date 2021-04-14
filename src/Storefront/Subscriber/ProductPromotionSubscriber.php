<?php declare(strict_types=1);

namespace ProductPromotionExam\Storefront\Subscriber;

use Doctrine\DBAL\Connection;
use mysql_xdevapi\Exception;
use Shopware\Core\Content\Cms\SalesChannel\Struct\TextStruct;
use Shopware\Core\Content\Product\Events\ProductListingResultEvent;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\Struct\ArrayStruct;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Shopware\Storefront\Page\Product\ProductPageLoadedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ProductPromotionSubscriber implements EventSubscriberInterface
{

    /**
     * @var SystemConfigService
     */
    private $systemConfigService;

    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var EntityRepositoryInterface
     */
    private $promotionExamRepository;

    public function __construct(Connection $connection, SystemConfigService $systemConfigService, EntityRepositoryInterface $promotionExamRepository)
    {
        $this->connection = $connection;
        $this->systemConfigService = $systemConfigService;
        $this->promotionExamRepository = $promotionExamRepository;
    }

    public static function getSubscribedEvents()
    {
        return [
            ProductListingResultEvent::class => 'onProductListingResultEvent',
            ProductPageLoadedEvent::class => 'onProductPageLoadedEvent'
        ];
    }

    /**
     * @param ProductListingResultEvent $event
     */
    public function onProductListingResultEvent(ProductListingResultEvent $event) {
        if (!$this->systemConfigService->get('ProductPromotionExam.config.showPromotion')) {
            return;
        }

        $event->getResult()->addExtension('promotion_exam', new ArrayStruct($this->getPromotedProductIds($event->getContext())));
    }

    private function getPromotedProductIds(Context $context): array
    {
        $query = $this->connection->createQueryBuilder();
        $query->select('DISTINCT LOWER(HEX(product_id))');
        $query->from('promotion_exam');
        return $query->execute()->fetchAll(\PDO::FETCH_COLUMN);
    }

    /**
     * @param ProductPageLoadedEvent $event
     */
    public function onProductPageLoadedEvent(ProductPageLoadedEvent $event) {
        if (!$this->systemConfigService->get('ProductPromotionExam.config.showPromotion')) {
            return;
        }

        $productId = $event->getPage()->getProduct()->getId();
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('productId', $productId));
        $promoList = $this->promotionExamRepository->search($criteria, $event->getContext())->getEntities();
        $event->getPage()->addExtension('promo_list', $promoList);
    }
}
