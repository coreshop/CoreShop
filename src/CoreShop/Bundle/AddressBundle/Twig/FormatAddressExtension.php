<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2019 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\AddressBundle\Twig;

use CoreShop\Bundle\AddressBundle\Templating\Helper\FormatAddressHelperInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

final class FormatAddressExtension extends AbstractExtension
{
    /**
     * @var FormatAddressHelperInterface
     */
    private $helper;

    /**
     * @param FormatAddressHelperInterface $helper
     */
    public function __construct(FormatAddressHelperInterface $helper)
    {
        $this->helper = $helper;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return [
            new TwigFilter('coreshop_format_address', [$this->helper, 'formatAddress'], ['is_safe' => ['html']]),
        ];
    }
}
