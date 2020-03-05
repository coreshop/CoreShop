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

namespace CoreShop\Bundle\ResourceBundle\Installer;

use CoreShop\Component\Registry\PrioritizedServiceRegistryInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CompositeResourceInstaller implements ResourceInstallerInterface
{
    protected $serviceRegistry;

    public function __construct(PrioritizedServiceRegistryInterface $serviceRegistry)
    {
        $this->serviceRegistry = $serviceRegistry;
    }

    /**
     * {@inheritdoc}
     */
    public function installResources(OutputInterface $output, string $applicationName = null, array $options = []): void
    {
        foreach ($this->serviceRegistry->all() as $installer) {
            if ($installer instanceof ResourceInstallerInterface) {
                $installer->installResources($output, $applicationName);
            }
        }
    }
}
