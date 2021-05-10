<?php
/**
 * Copyright ©  Flagbit GmbH & Co. KG All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Flagbit\LargeConfigurableProducts\Api\Data;


use Magento\Catalog\Model\Product\Attribute\Backend\Sku;

interface AssociationProductsPaginationInterface
{
    /**
     * @return int
     */
    public function getVariationsTotal(): int;


    /**
     * @param Sku $sku
     * @return void
     */
    public function setProductSku($sku): void;

    /**
     * @param int $offset
     * @return void
     */
    public function setPaginationOffset($offset): void;


    /**
     * @param int $length
     * @return void
     *
     */
    public function setPaginationLength($length): void;

}
