<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\CurrencyBundle\Twig;

use CoreShop\Bundle\CurrencyBundle\Templating\Helper\CurrencyHelperInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

final class CurrencyExtension extends AbstractExtension
{
    /**
     * @var CurrencyHelperInterface
     */
    private $helper;

    /**
     * @param CurrencyHelperInterface $helper
     */
    public function __construct(CurrencyHelperInterface $helper)
    {
        $this->helper = $helper;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return [
            new TwigFilter('coreshop_currency_symbol', [$this->helper, 'convertCurrencyCodeToSymbol']),
        ];
    }
}
