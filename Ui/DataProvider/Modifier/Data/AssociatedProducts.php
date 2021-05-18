<?php
/**
 * Copyright ©  Flagbit GmbH & Co. KG All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Flagbit\LargeConfigurableProducts\Ui\DataProvider\Modifier\Data;

use Flagbit\LargeConfigurableProducts\Api\Data\AssociationProductsPaginationInterface;
use Flagbit\LargeConfigurableProducts\Config;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Attribute\Backend\Sku;
use Magento\ConfigurableProduct\Ui\DataProvider\Product\Form\Modifier\Data\AssociatedProducts as AssociatedProductsBase;
use Magento\Framework\Exception\NoSuchEntityException;

class AssociatedProducts extends AssociatedProductsBase implements AssociationProductsPaginationInterface
{
    /**
     * @var int $offset
     */
    private $offset;

    /**
     * @var array $assoc_prod_ids
     */
    private $associatedProductsIDs;

    /**
     * @var int $limit
     */
    private $limit;

    /**
     * @var int $variationsTotal
     */
    private $associatedProductsTotal;

    /**
     * @return  int $associatedProductsTotal
     */
    public function getAssociatedProductsTotal(): int
    {
        return $this->associatedProductsTotal;
    }

    /**
     * @return  array $associatedProductsIDs
     */
    public function getAssociatedProductsIDs(): array
    {
        return $this->associatedProductsIDs;
    }

    /**
     * @return int
     */
    public function getOffset(): int
    {
        return $this->offset ?? Config::INITAL_OFFSET;
    }

    /**
     * @return int
     */
    public function getSize(): int
    {
        return $this->limit ?? Config::INITAL_SIZE;
    }

    /**
     * @param Sku $sku
     * @return void
     */
    public function setProductSku($sku): void
    {
        $this->locator->setSku($sku);
    }

    /**
     * @param int $offset
     * @return void
     */
    public function setPaginationOffset($offset = Config::INITAL_OFFSET): void
    {
        $this->offset = $offset;
    }

    /**
     * @param int $limit
     * @return void
     * @TODO create an input for admin user, for configuration speed/quantity
     *
     */
    public function setPaginationLimit($limit = Config::INITAL_SIZE): void
    {
        $this->limit = $limit;
    }

    /**
     * Retrieve actual list of associated products (i.e. if product contains variations matrix form data
     * - previously saved in database relations are not considered)
     *
     * @return Product[]
     */
    protected function _getAssociatedProducts(): array
    {
        $products = [];
        $configurable = $this->locator->getProduct();
        $ids = $this->locator->getProduct()->getAssociatedProductIds();
        if ($ids === null) {
            // form data overrides any relations stored in database
            $products = $configurable
                ->getTypeInstance()
                ->getUsedProducts($configurable);
            foreach ($products as $child) {
                $ids[] = $child->getEntityId();
            }
        }
        foreach ($ids as $productId) {
            try {
                $products[] = $this->productRepository->getById($productId);
            } catch (NoSuchEntityException $e) {
                continue;
            }
        }

        $this->associatedProductsTotal = count($products);
        $this->associatedProductsIDs = $ids;
        return array_slice($products, $this->getOffset(), $this->getSize());
    }
}
