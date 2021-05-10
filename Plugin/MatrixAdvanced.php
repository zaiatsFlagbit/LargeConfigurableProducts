<?php
/**
 * Copyright ©  Flagbit GmbH & Co. KG All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Flagbit\LargeConfigurableProducts\Plugin;

class MatrixAdvanced
{
    /**
     * Avoid redundant heavy load computations
     * Detach jsonEncode($productMatrix) from indexed file
     * Resulting in > 10 MB json on client server and
     * Heavy response body transmitted via Network
     * @return array
     */
    public function aroundGetVariations(): array
    {
        return [];
    }

}

