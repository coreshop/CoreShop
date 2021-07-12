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

declare(strict_types=1);

namespace CoreShop\Bundle\CoreBundle\Twig;

use CoreShop\Component\Core\Taxation\TaxationDisplayProviderInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class TaxationDisplayProviderExtension extends AbstractExtension
{
    private TaxationDisplayProviderInterface $taxationDisplayProvider;

    public function __construct(TaxationDisplayProviderInterface $taxationDisplayProvider)
    {
        $this->taxationDisplayProvider = $taxationDisplayProvider;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('coreshop_display_with_tax', [$this, 'getDisplayWithTax']),
        ];
    }

    public function getDisplayWithTax(array $context): bool
    {
        return $this->taxationDisplayProvider->displayWithTax($context);
    }
}
