<?php

namespace CoreShop\Bundle\ResourceBundle\Installer;

use CoreShop\Component\Registry\PrioritizedServiceRegistryInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CompositeResourceInstaller implements ResourceInstallerInterface
{
    /**
     * @var PrioritizedServiceRegistryInterface
     */
    protected $serviceRegistry;

    /**
     * @param PrioritizedServiceRegistryInterface $serviceRegistry
     */
    public function __construct(PrioritizedServiceRegistryInterface $serviceRegistry)
    {
        $this->serviceRegistry = $serviceRegistry;
    }

    /**
     * {@inheritdoc}
     */
    public function installResources(OutputInterface $output, $applicationName = null)
    {
        foreach ($this->serviceRegistry->all() as $installer) {
            if ($installer instanceof ResourceInstallerInterface) {
                $installer->installResources($output, $applicationName);
            }
        }
    }
}