<?php
/**
 * Copyright ©  Flagbit GmbH & Co. KG All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Flagbit\LargeConfigurableProducts\Controller;

use Flagbit\LargeConfigurableProducts\Api\ProductMatrixInterface;
use Flagbit\LargeConfigurableProducts\Ui\DataProvider\Modifier\Data\AssociatedProducts;
use Magento\Catalog\Model\Product\Attribute\Backend\Sku;

class ProductMatrix implements ProductMatrixInterface
{
    /**
     * @var AssociatedProducts
     */
    private $associatedProducts;

    /**
     * ProductMatrix constructor
     * @param AssociatedProducts $associatedProducts
     */
    public function __construct(
        AssociatedProducts $associatedProducts
    ) {
        $this->associatedProducts = $associatedProducts;
    }

    /**
     * @param string $sku
     * @param int $offset
     * @param int $length
     * @return array
     */
    public function getProductMatrixPaginated($sku, $offset, $length): array
    {
        $this->_runSetters($sku, $offset, $length);
        try {
            $response = $this->associatedProducts->getProductMatrix();
        } catch (\Exception $e) {
            $response = ['error' => $e->getMessage()];
        }

        return $response;
    }

    /**
     * @param Sku $sku
     * @param int $offset
     * @param int $length
     * @return void
     */
    private function _runSetters($sku, $offset, $length): void
    {
        $this->associatedProducts->setProductSku($sku);
        $this->associatedProducts->setPaginationOffset($offset);
        $this->associatedProducts->setPaginationLength($length);
    }
}
