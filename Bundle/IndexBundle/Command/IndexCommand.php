<?php

namespace CoreShop\Bundle\IndexBundle\Command;

use Pimcore\Model\Object\AbstractObject;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class IndexCommand extends ContainerAwareCommand
{
    /**
     * configure command.
     */
    protected function configure()
    {
        $this
            ->setName('coreshop:index')
            ->setDescription('Reindex all Products');
    }

    /**
     * Execute command.
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $allProducts = $this->getContainer()->get('coreshop.repository.product')->getList();;
        $allProducts->setObjectTypes([AbstractObject::OBJECT_TYPE_OBJECT, AbstractObject::OBJECT_TYPE_VARIANT]);
        $allProducts = $allProducts->load();

        $steps = count($allProducts);

        $output->writeln('<info>Found '.count($allProducts).' Products to index</info>');

        $progress = new ProgressBar($output, $steps);
        $progress->start();

        foreach ($allProducts as $product) {
            $this->getContainer()->get('coreshop.index.updater')->updateIndices($product);

            $progress->advance();
        }

        $output->writeln('');
        $output->writeln('<info>Done</info>');

        return 0;
    }
}
