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

namespace CoreShop\Bundle\FrontendBundle\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class TestHtmlAttributeExtension extends AbstractExtension
{
    /**
     * @var string
     */
    private $env;

    public function __construct(string $env)
    {
        $this->env = $env;
    }

    /**
     * @return TwigFunction[]
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'coreshop_test_html_attribute',
                function (string $name, ?string $value = null): string {
                    if (strpos($this->env, 'test') === 0) {
                        return sprintf('data-test-%s="%s"', $name, (string) $value);
                    }

                    return '';
                },
                ['is_safe' => ['html']]
            ),
        ];
    }
}
