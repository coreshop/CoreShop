<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under two different licenses:
 *  - GNU General Public License version 3 (GPLv3)
 *  - CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    https://www.coreshop.com/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Bundle\CurrencyBundle\Twig;

use CoreShop\Component\Currency\Display\DisplayFractionProviderInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class DisplayFractionExtension extends AbstractExtension
{
    public function __construct(
        private DisplayFractionProviderInterface $displayFractionProvider,
    ) {
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
