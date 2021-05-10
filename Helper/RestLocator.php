<?php
/**
 * Copyright ©  Flagbit GmbH & Co. KG All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Flagbit\LargeConfigurableProducts\Helper;

use Flagbit\LargeConfigurableProducts\Api\Data\LocatorRestInterface;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product\Attribute\Backend\Sku;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class RestLocator
 */
class RestLocator implements LocatorRestInterface
{
    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var ProductInterface
     */
    private $product;

    /**
     * @var StoreInterface
     */
    private $store;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var Sku
     */
    protected $sku;

    /**
     * Constructor
     *
     * @param ProductRepositoryInterface $productRepository
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        ProductRepositoryInterface $productRepository,
        StoreManagerInterface $storeManager
    ) {
        $this->productRepository = $productRepository;
        $this->storeManager = $storeManager;
    }

    public function setSku($sku): void
    {
        $this->sku = $sku;
    }

    /**
     * {@inheritDoc}
     */
    public function getProduct(): ProductInterface
    {
        if ($this->product) {
            return $this->product;
        }

        // Retrieve product via repository
        if (null !== $this->sku) {
            return $this->productRepository->get($this->sku);
        }

        return $this->product;
    }

    /**
     * {@inheritDoc}
     */
    public function getStore()
    {
        if (!$this->store) {
            $this->store = $this->storeManager->getStore();
        }
        return $this->store;
    }

    /**
     * {@inheritDoc}
     */
    public function getWebsiteIds()
    {
        return $this->getProduct()->getWebsiteIds();
    }

    /**
     * {@inheritDoc}
     */
    public function getBaseCurrencyCode()
    {
        return $this->getStore()->getBaseCurrencyCode();
    }
}
