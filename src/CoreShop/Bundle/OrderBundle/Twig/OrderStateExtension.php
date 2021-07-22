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

namespace CoreShop\Bundle\OrderBundle\Twig;

use CoreShop\Bundle\OrderBundle\Templating\Helper\OrderStateHelperInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

final class OrderStateExtension extends AbstractExtension
{
    /**
     * @var OrderStateHelperInterface
     */
    private $helper;

    /**
     * @param OrderStateHelperInterface $helper
     */
    public function __construct(OrderStateHelperInterface $helper)
    {
        $this->helper = $helper;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return [
            new TwigFilter('coreshop_order_state', [$this->helper, 'getOrderState']),
        ];
    }
}
