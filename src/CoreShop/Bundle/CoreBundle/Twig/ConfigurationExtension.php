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

use CoreShop\Bundle\CoreBundle\Templating\Helper\ConfigurationHelperInterface;
use CoreShop\Component\Core\Configuration\ConfigurationServiceInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class ConfigurationExtension extends AbstractExtension
{
    private $configurationService;

    public function __construct(ConfigurationServiceInterface $configurationService)
    {
        $this->configurationService = $configurationService;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('coreshop_configuration', [$this->configurationService, 'getForStore']),
        ];
    }
}
