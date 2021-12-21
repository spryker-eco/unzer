<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Dependency;

interface UnzerToUtilTextServiceInterface
{
    /**
     * @param string $prefix
     * @param bool $moreEntropy
     *
     * @return string
     */
    public function generateUniqueId(string $prefix = '', bool $moreEntropy = false): string;
}
