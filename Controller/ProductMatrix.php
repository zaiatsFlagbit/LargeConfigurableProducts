<?php
/**
 * Copyright ©  Flagbit GmbH & Co. KG All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Flagbit\LargeConfigurableProducts\Controller;

use Flagbit\LargeConfigurableProducts\Api\ProductMatrixInterface;
use Flagbit\LargeConfigurableProducts\Helper\RestLocator;
use Flagbit\LargeConfigurableProducts\Ui\DataProvider\Modifier\Data\AssociatedProducts;
use Magento\Catalog\Model\Product\Attribute\Backend\Sku;
use Magento\Framework\App\Area;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\App\Emulation;

class ProductMatrix implements ProductMatrixInterface
{
    /**
     * @var AssociatedProducts
     */
    private $associatedProducts;

    /**
     * @var Emulation
     */
    protected $emulation;

    /**
     * @var RestLocator
     */
    protected $locator;

    /**
     * ProductMatrix constructor
     * @param AssociatedProducts $associatedProducts
     * @param Emulation $emulation
     * @param RestLocator $locator
     */
    public function __construct(
        AssociatedProducts $associatedProducts,
        Emulation $emulation,
        RestLocator $locator
    ) {
        $this->associatedProducts = $associatedProducts;
        $this->emulation = $emulation;
        $this->locator = $locator;
    }

    /**
     * @return int
     */
    public function getStoreId(): ?int
    {
        try {
            $store = $this->locator->getStore();
            if (isset($store)) {
                return (int) $store->getId();
            }
            throw new Exception("The storeId does not exist");
        } catch (NoSuchEntityException $e) {
            throw new Exception($e);
        }
    }

    /**
     * @param Sku $sku
     * @param int $offset
     * @param int $limit
     * @return array
     */
    public function getProductMatrixPaginated($sku, $offset, $limit): array
    {
        $this->_runSetters($sku, $offset, $limit);

        // store adminarea emulation
        $this->emulation->startEnvironmentEmulation($this->getStoreId(), Area::AREA_ADMINHTML, true);
        try {
            $response = $this->associatedProducts->getProductMatrix();
        } catch (\Exception $e) {
            $response = ['error' => $e->getMessage()];
        }
        $this->emulation->stopEnvironmentEmulation();
        return $response;
    }

    /**
     * @param Sku $sku
     * @param int $offset
     * @param int $limit
     * @return void
     */
    private function _runSetters($sku, $offset, $limit): void
    {
        $this->associatedProducts->setProductSku($sku);
        $this->associatedProducts->setPaginationOffset($offset);
        $this->associatedProducts->setPaginationLimit($limit);
    }
}
