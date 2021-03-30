<?php declare(strict_types=1);

namespace ProductPromotionExam\Core\Content\PromotionExam;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

class PromotionExamCollection extends EntityCollection {
    protected function getExpectedClass(): string
    {
        return PromotionExamEntity::class;
    }

}
