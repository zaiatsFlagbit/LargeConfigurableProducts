<?php
/**
 * Copyright ©  Flagbit GmbH & Co. KG All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Flagbit\LargeConfigurableProducts\Ui\DataProvider\Modifier\Data;

use Flagbit\LargeConfigurableProducts\Api\Data\AssociationProductsPaginationInterface;
use Flagbit\LargeConfigurableProducts\Config;
use Magento\Catalog\Model\Product\Attribute\Backend\Sku;
use Magento\ConfigurableProduct\Ui\DataProvider\Product\Form\Modifier\Data\AssociatedProducts as AssociatedProductsBase;

class AssociatedProducts extends AssociatedProductsBase implements AssociationProductsPaginationInterface
{
    /**
     * @var int $offset
     */
    private $offset;

    /**
     * @var int $length
     */
    private $length;

    /**
     * @var int $variationsTotal
     */
    private $variationTotal;

    /**
     * @return  int $variationsTotal
     */
    public function getVariationsTotal(): int
    {
        return $this->variationTotal;
    }

    /**
     * @return int
     */
    public function getOffset(): int
    {
        return $this->offset ?? Config::INITIAL_OFFSET;
    }

    /**
     * @return int
     */
    public function getSize(): int
    {
        return $this->length ?? Config::INITIAL_SIZE;
    }

    /**
     * @return int
     */
    public function getRequestVariationCount(): int
    {
        return $this->getSize() + $this->getOffset();
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
    public function setPaginationOffset($offset = Config::INITIAL_OFFSET): void
    {
        $this->offset = $offset;
    }

    /**
     * @param int $length
     * @return void
     * @TODO create an input for admin user, for configuration speed/quantity
     *
     */
    public function setPaginationLength($length = Config::INITIAL_SIZE): void
    {
        $this->length = $length;
    }

    /**
     * Retrieve all possible attribute values combinations
     *
     * @return array
     */
    protected function getVariations(): array
    {
        $variations = $this->variationMatrix->getVariations($this->getAttributes());
        $this->variationTotal = count($variations);
        if ($this->getRequestVariationCount() < $this->variationTotal) {
            return array_slice($variations, $this->getOffset(), $this->getSize());
        }
        return $variations;
    }
}
