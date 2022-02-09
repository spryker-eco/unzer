<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
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
