<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
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
