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

namespace CoreShop\Bundle\CurrencyBundle\Templating\Helper;

use Symfony\Component\Intl\Intl;
use Symfony\Component\Templating\Helper\Helper;

class CurrencyHelper extends Helper implements CurrencyHelperInterface
{
    /**
     * {@inheritdoc}
     */
    public function convertCurrencyCodeToSymbol($code)
    {
        return Intl::getCurrencyBundle()->getCurrencySymbol($code);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'coreshop_currency';
    }
}
