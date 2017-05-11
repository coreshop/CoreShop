<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
*/

namespace CoreShop\Bundle\CurrencyBundle\Twig;

use CoreShop\Bundle\CurrencyBundle\Templating\Helper\ConvertMoneyHelperInterface;

final class ConvertMoneyExtension extends \Twig_Extension
{
    /**
     * @var ConvertMoneyHelperInterface
     */
    private $helper;

    /**
     * @param ConvertMoneyHelperInterface $helper
     */
    public function __construct(ConvertMoneyHelperInterface $helper)
    {
        $this->helper = $helper;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('coreshop_convert_money', [$this->helper, 'convertAmount']),
        ];
    }
}
