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
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    https://www.coreshop.com/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Bundle\FrontendBundle\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

final class TypeCastExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('coreshop_int', function ($value) {
                return (int) $value;
            }),
            new TwigFilter('coreshop_float', function ($value) {
                return (float) $value;
            }),
            new TwigFilter('coreshop_string', function ($value) {
                return (string) $value;
            }),
            new TwigFilter('coreshop_bool', function ($value) {
                return (bool) $value;
            }),
            new TwigFilter('coreshop_array', function (object $value) {
                return (array) $value;
            }),
            new TwigFilter('coreshop_object', function (array $value) {
                return (object) $value;
            }),
        ];
    }
}
