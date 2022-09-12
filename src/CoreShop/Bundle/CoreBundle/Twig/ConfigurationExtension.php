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
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Bundle\CoreBundle\Twig;

use CoreShop\Component\Core\Configuration\ConfigurationServiceInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class ConfigurationExtension extends AbstractExtension
{
    public function __construct(private ConfigurationServiceInterface $configurationService)
    {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('coreshop_configuration', [$this->configurationService, 'getForStore']),
        ];
    }
}
