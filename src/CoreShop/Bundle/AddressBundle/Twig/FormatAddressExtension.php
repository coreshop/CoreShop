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

namespace CoreShop\Bundle\AddressBundle\Twig;

use CoreShop\Bundle\AddressBundle\Templating\Helper\FormatAddressHelperInterface;

final class FormatAddressExtension extends \Twig_Extension
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
            new \Twig_Filter('coreshop_format_address', [$this->helper, 'formatAddress'], ['is_safe' => ['html']]),
        ];
    }
}
