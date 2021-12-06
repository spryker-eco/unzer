<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Unzer\Business\Config;

use Generated\Shared\Transfer\UnzerConfigResponseTransfer;
use Generated\Shared\Transfer\UnzerConfigTransfer;

interface UnzerConfigUpdaterInterface
{
    /**
     * @param \Generated\Shared\Transfer\UnzerConfigTransfer $unzerConfigTransfer
     *
     * @return \Generated\Shared\Transfer\UnzerConfigResponseTransfer
     */
    public function updateUnzerConfig(UnzerConfigTransfer $unzerConfigTransfer): UnzerConfigResponseTransfer;
}
