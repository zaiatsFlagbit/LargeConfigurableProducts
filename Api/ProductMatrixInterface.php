<?php
/**
 * Copyright ©  Flagbit GmbH & Co. KG All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Flagbit\LargeConfigurableProducts\Api;

interface ProductMatrixInterface
{
    /**
     * @param string $sku
     * @param int $offset
     * @param int $limit
     * @return array
     */
    public function getProductMatrixPaginated($sku, $offset, $limit): array;

}
