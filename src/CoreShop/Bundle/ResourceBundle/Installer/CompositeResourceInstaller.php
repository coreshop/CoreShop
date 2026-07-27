<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 *
 */

namespace CoreShop\Bundle\ResourceBundle\Installer;

use CoreShop\Component\Registry\PrioritizedServiceRegistryInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CompositeResourceInstaller implements ResourceInstallerInterface
{
    public function __construct(
        protected PrioritizedServiceRegistryInterface $serviceRegistry,
    ) {
    }

    public function installResources(OutputInterface $output, ?string $applicationName = null, array $options = []): void
    {
        foreach ($this->serviceRegistry->all() as $installer) {
            if ($installer instanceof ResourceInstallerInterface) {
                $installer->installResources($output, $applicationName, $options);
            }
        }
    }
}
