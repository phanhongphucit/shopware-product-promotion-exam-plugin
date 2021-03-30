<?php declare(strict_types=1);

namespace ProductPromotionExam\Core\Api;

use Faker\Factory;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\Routing\Annotation\RouteScope;
use Shopware\Core\Framework\Uuid\Uuid;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @RouteScope(scopes={"api"})
 */
class DemoPromotionController extends AbstractController {

    /**
     * @var SystemConfigService
     */
    private $systemConfigService;

    /**
     * @var EntityRepositoryInterface
     */
    private $productRepository;

    /**
     * @var EntityRepositoryInterface
     */
    private $promotionExamRepository;

    public function __construct(SystemConfigService $systemConfigService, EntityRepositoryInterface $productRepository, EntityRepositoryInterface $promotionExamRepository)
    {
        $this->systemConfigService = $systemConfigService;
        $this->productRepository = $productRepository;
        $this->promotionExamRepository = $promotionExamRepository;
    }

    /**
     * @Route(path="/api/v{version}/_action/product-promotion-exam/generate", name="api.custom.product_promotion_exam.generate", methods={"POST"})
     * @param Context $context
     * @return Response
     */
    public function generate(Context $context): Response
    {
        $faker = Factory::create();

        $product_ids = $this->getActiveProductIds($context);
        $promoData = [];
        $max_promo = $this->systemConfigService->get('ProductPromotionExam.config.maxNumPromo');
        foreach ($product_ids as $p_id) {
            $num_promo = rand(1, $max_promo);
            for($i = 0; $i < $num_promo; $i++) {
                $promoData[] = [
                    "id" => Uuid::randomHex(),
                    "name" => 'promotion' . $faker->name,
                    "discountRate" => $faker->numberBetween(10, 40),
                    "startDate" => $faker->dateTimeBetween('+2 days', '+10 days'),
                    "expiredDate" => $faker->dateTimeBetween('+15 days', "+45 days"),
                    "productId" => $p_id
                ];
            }
        }
        $this->promotionExamRepository->create($promoData, $context);
        return new Response('', Response::HTTP_NO_CONTENT);
    }

    /**
     * @param Context $context
     * @return array
     */
    private function getActiveProductIds(Context $context): array
    {
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('active', true));
        $criteria->setLimit(10);
        return $this->productRepository->search($criteria, $context)->getIds();
    }

}
