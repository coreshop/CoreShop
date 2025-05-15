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

namespace CoreShop\Bundle\AddressBundle\Twig;

use CoreShop\Component\Address\Formatter\AddressFormatterInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

final class FormatAddressExtension extends AbstractExtension
{
    public function __construct(
        private AddressFormatterInterface $addressFormatter,
    ) {
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('coreshop_format_address', [$this->addressFormatter, 'formatAddress'], ['is_safe' => ['html']]),
        ];
    }
}
