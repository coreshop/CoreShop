<?php

namespace CoreShop\Bundle\ResourceBundle\Installer;

use Symfony\Component\Console\Output\OutputInterface;

interface ResourceInstallerInterface
{
    /**
     * @param OutputInterface $output
     */
    public function installResources(OutputInterface $output);
}