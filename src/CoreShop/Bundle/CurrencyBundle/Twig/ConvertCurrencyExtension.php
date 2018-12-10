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

namespace CoreShop\Bundle\CurrencyBundle\Twig;

use CoreShop\Bundle\CurrencyBundle\Templating\Helper\ConvertCurrencyHelperInterface;

final class ConvertCurrencyExtension extends \Twig_Extension
{
    /**
     * @var ConvertCurrencyHelperInterface
     */
    private $helper;

    /**
     * @param ConvertCurrencyHelperInterface $helper
     */
    public function __construct(ConvertCurrencyHelperInterface $helper)
    {
        $this->helper = $helper;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return [
            new \Twig_Filter('coreshop_convert_currency', [$this->helper, 'convertAmount']),
        ];
    }
}
