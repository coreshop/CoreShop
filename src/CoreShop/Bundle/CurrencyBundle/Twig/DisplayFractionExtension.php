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

declare(strict_types=1);

namespace CoreShop\Bundle\CurrencyBundle\Twig;

use CoreShop\Component\Currency\Display\DisplayFractionProviderInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class DisplayFractionExtension extends AbstractExtension
{
    private DisplayFractionProviderInterface $displayFractionProvider;

    public function __construct(DisplayFractionProviderInterface $displayFractionProvider)
    {
        $this->displayFractionProvider = $displayFractionProvider;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('coreshop_currency_display_fraction', [$this, 'getDisplayFraction']),
        ];
    }

    public function getDisplayFraction(array $context): int
    {
        return $this->displayFractionProvider->getDisplayFraction($context);
    }
}
