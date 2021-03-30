<?php declare(strict_types=1);

namespace ProductPromotionExam\Core\Content\PromotionExam;

use Shopware\Core\Content\Product\ProductDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\DateTimeField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\FkField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\FloatField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IdField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ManyToOneAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\StringField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;

class PromotionExamDefinition extends EntityDefinition {
    public function getEntityName(): string
    {
        return 'promotion_exam';
    }

    public function getCollectionClass(): string
    {
        return PromotionExamCollection::class;
    }

    public function getEntityClass(): string
    {
        return PromotionExamEntity::class;
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new IdField('id', 'id'))->addFlags(new Required(), new PrimaryKey()),
            (new StringField('name', 'name'))->addFlags(new Required()),
            (new FloatField('discount_rate', 'discountRate'))->addFlags(new Required()),
            new DateTimeField('start_date', 'startDate'),
            new DateTimeField('expired_date', 'expiredDate'),

            (new FkField('product_id', 'productId', ProductDefinition::class)),
            new ManyToOneAssociationField('product', 'product_id', ProductDefinition::class)
        ]);
    }

}
