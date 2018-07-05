<?php

namespace CoreShop\Bundle\ResourceBundle\Installer;

use Symfony\Component\Console\Output\OutputInterface;

interface ResourceInstallerInterface
{
    /**
     * @param OutputInterface $output
     * @param string          $applicationName
     * @param array           $options
     */
    public function installResources(OutputInterface $output, $applicationName = null, $options = []);
}
