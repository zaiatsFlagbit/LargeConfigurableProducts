<?php
/**
 * Copyright ©  Flagbit GmbH & Co. KG All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Flagbit\LargeConfigurableProducts\Ui\DataProvider\Modifier;

use Flagbit\LargeConfigurableProducts\Ui\DataProvider\Modifier\Data\AssociatedProducts;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\Locator\LocatorInterface;
use Magento\Catalog\Ui\AllowedProductTypes;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable as ConfigurableType;
use Magento\ConfigurableProduct\Ui\DataProvider\Product\Form\Modifier\Composite;
use Magento\Framework\ObjectManagerInterface;
use Magento\Ui\DataProvider\Modifier\ModifierInterface;

/**
 * Data provider for Configurable products
 */
class ModifierAdvanced extends Composite
{
    /**
     * @var LocatorInterface
     */
    private $locator;

    /**
     * @var AssociatedProducts
     */
    private $associatedProducts;

    /**
     * @var AllowedProductTypes
     */
    protected $allowedProductTypes;

    /**
     * @var ModifierInterface[]
     */
    private $modifiersObjects = [];


    public function __construct(
        LocatorInterface $locator,
        ObjectManagerInterface $objectManager,
        AssociatedProducts $associatedProducts,
        AllowedProductTypes $allowedProductTypes,
        array $modifiers = []
    ) {
        parent::__construct(
            $locator,
            $objectManager,
            $associatedProducts,
            $allowedProductTypes,
            $modifiers
        );
        $this->locator = $locator;
        $this->associatedProducts = $associatedProducts;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyData(array $data)
    {
        /** @var ProductInterface $model */
        $model = $this->locator->getProduct();
        $productTypeId = $model->getTypeId();
        if ($this->allowedProductTypes->isAllowedProductType($this->locator->getProduct())) {
            $productId = $model->getId();
            $data[$productId]['affect_configurable_product_attributes'] = '1';

            if ($productTypeId === ConfigurableType::TYPE_CODE) {
                $data[$productId]['configurable-matrix'] = $this->associatedProducts->getProductMatrix();
                $data[$productId]['assoc_prod_total'] = $this->associatedProducts->getAssociatedProductsTotal();
                $data[$productId]['assoc_prod_ids'] = $this->associatedProducts->getAssociatedProductsIDs();
                $data[$productId]['attributes'] = $this->associatedProducts->getProductAttributesIds();
                $data[$productId]['attribute_codes'] = $this->associatedProducts->getProductAttributesCodes();
                $data[$productId]['product']['configurable_attributes_data'] =
                    $this->associatedProducts->getConfigurableAttributesData();
            }
        }

        foreach ($this->modifiersObjects as $modifier) {
            $data = $modifier->modifyData($data);
        }

        return $data;
    }
}
