<?php
/**
 * Copyright ©  Flagbit GmbH & Co. KG All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Flagbit\LargeConfigurableProducts\Api\Data;

use Magento\Catalog\Model\Locator\LocatorInterface;
use Magento\Catalog\Model\Product\Attribute\Backend\Sku;

interface LocatorRestInterface extends LocatorInterface
{
    /**
     * @param Sku $sku
     * @return void
     */
    public function setSku($sku): void;

}
