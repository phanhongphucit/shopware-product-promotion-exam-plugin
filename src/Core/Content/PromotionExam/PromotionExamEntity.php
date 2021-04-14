<?php declare(strict_types=1);


namespace ProductPromotionExam\Core\Content\PromotionExam;

use Shopware\Core\Content\Product\ProductEntity;
use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityIdTrait;

class PromotionExamEntity extends Entity
{
    use EntityIdTrait;

    /**
     * @var string
     */
    protected $name;
    /**
     * @var float
     */
    protected $discountRate;
    /**
     * @var \DateTimeInterface|null
     */
    protected $startDate;
    /**
     * @var \DateTimeInterface|null
     */
    protected $expiredDate;

    /**
     * @var ProductEntity|null
     */
    protected $product;

    /**
     * @var string
     */
    protected $productId;

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return PromotionEntity
     */
    public function setName(string $name): PromotionEntity
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return float
     */
    public function getDiscountRate(): float
    {
        return $this->discountRate;
    }

    /**
     * @param float $discountRate
     * @return PromotionExamEntity
     */
    public function setDiscountRate(float $discountRate): PromotionExamEntity
    {
        $this->discountRate = $discountRate;
        return $this;
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->startDate;
    }

    /**
     * @param \DateTimeInterface|null $startDate
     * @return PromotionEntity
     */
    public function setStartDate(?\DateTimeInterface $startDate): PromotionEntity
    {
        $this->startDate = $startDate;
        return $this;
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function getExpiredDate(): ?\DateTimeInterface
    {
        return $this->expiredDate;
    }

    /**
     * @param \DateTimeInterface|null $expiredDate
     * @return PromotionEntity
     */
    public function setExpiredDate(?\DateTimeInterface $expiredDate): PromotionEntity
    {
        $this->expiredDate = $expiredDate;
        return $this;
    }

    /**
     * @return ProductEntity|null
     */
    public function getProduct(): ?ProductEntity
    {
        return $this->product;
    }

    /**
     * @param ProductEntity|null $product
     * @return PromotionExamEntity
     */
    public function setProduct(?ProductEntity $product): PromotionExamEntity
    {
        $this->product = $product;
        return $this;
    }

    /**
     * @return string
     */
    public function getProductId(): string
    {
        return $this->productId;
    }

    /**
     * @param string $productId
     * @return PromotionExamEntity
     */
    public function setProductId(string $productId): PromotionExamEntity
    {
        $this->productId = $productId;
        return $this;
    }

}
