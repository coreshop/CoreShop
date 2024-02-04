<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under two different licenses:
 *  - GNU General Public License version 3 (GPLv3)
 *  - CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Bundle\ClassDefinitionPatchBundle\Console;

use SebastianBergmann\Diff\Differ;
use SebastianBergmann\Diff\Output\UnifiedDiffOutputBuilder;

final class ConsoleDiffer
{
    public function __construct(
        private ColorConsoleDiffFormatter $colorConsoleDiffFormatter,
    ) {
    }

    public function diff(string $old, string $new): string
    {
        $differ = new Differ(new UnifiedDiffOutputBuilder());
        $diff = $differ->diff($old, $new);

        return $this->colorConsoleDiffFormatter->format($diff);
    }
}
