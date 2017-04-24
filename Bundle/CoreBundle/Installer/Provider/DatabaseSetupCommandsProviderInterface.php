<?php

namespace CoreShop\Bundle\CoreBundle\Installer\Provider;

use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

interface DatabaseSetupCommandsProviderInterface
{
    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param QuestionHelper $questionHelper
     *
     * @return array
     */
    public function getCommands(InputInterface $input, OutputInterface $output, QuestionHelper $questionHelper);
}
