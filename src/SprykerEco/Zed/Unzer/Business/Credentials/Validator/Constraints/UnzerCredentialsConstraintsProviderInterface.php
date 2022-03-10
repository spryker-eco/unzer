<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Business\Credentials\Validator\Constraints;

interface UnzerCredentialsConstraintsProviderInterface
{
    /**
     * @param int $unzerCredentialsType
     *
     * @return array
     */
    public function getConstraintsCollectionByConfigType(int $unzerCredentialsType): array;
}
