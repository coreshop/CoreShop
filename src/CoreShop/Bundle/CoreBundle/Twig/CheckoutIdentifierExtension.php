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

namespace CoreShop\Bundle\CoreBundle\Twig;

use CoreShop\Bundle\CoreBundle\Templating\Helper\CheckoutIdentifierHelperInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class CheckoutIdentifierExtension extends AbstractExtension
{
    /**
     * @var CheckoutIdentifierHelperInterface
     */
    private $helper;

    /**
     * @param CheckoutIdentifierHelperInterface $helper
     */
    public function __construct(CheckoutIdentifierHelperInterface $helper)
    {
        $this->helper = $helper;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('coreshop_checkout_steps', [$this->helper, 'getSteps']),
            new TwigFunction('coreshop_checkout_steps_*', [$this->helper, 'getStep']),
        ];
    }
}
