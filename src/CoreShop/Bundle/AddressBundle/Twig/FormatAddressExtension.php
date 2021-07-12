<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2020 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Bundle\AddressBundle\Twig;

use CoreShop\Component\Address\Formatter\AddressFormatterInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

final class FormatAddressExtension extends AbstractExtension
{
    private AddressFormatterInterface $addressFormatter;

    public function __construct(AddressFormatterInterface $addressFormatter)
    {
        $this->addressFormatter = $addressFormatter;
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('coreshop_format_address', [$this->addressFormatter, 'formatAddress'], ['is_safe' => ['html']]),
        ];
    }
}
