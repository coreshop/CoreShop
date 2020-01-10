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

namespace CoreShop\Bundle\MoneyBundle\Twig;

use CoreShop\Bundle\MoneyBundle\Templating\Helper\FormatMoneyHelperInterface;

final class FormatMoneyExtension extends \Twig_Extension
{
    /**
     * @var FormatMoneyHelperInterface
     */
    private $helper;

    /**
     * @param FormatMoneyHelperInterface $helper
     */
    public function __construct(FormatMoneyHelperInterface $helper)
    {
        $this->helper = $helper;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return [
            new \Twig_Filter('coreshop_format_money', [$this->helper, 'formatAmount']),
        ];
    }
}
