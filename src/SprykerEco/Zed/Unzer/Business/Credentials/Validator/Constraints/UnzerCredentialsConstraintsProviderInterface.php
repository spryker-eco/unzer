<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Business\Credentials\Validator\Constraints;

use Symfony\Component\Validator\Constraints\Collection;

interface UnzerCredentialsConstraintsProviderInterface
{
    /**
     * @param int $unzerCredentialsType
     *
     * @return \Symfony\Component\Validator\Constraints\Collection
     */
    public function getConstraintsCollectionByConfigType(int $unzerCredentialsType): Collection;
}
