<?php
/**
 * CoreShop
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015 Dominik Pfaffenbauer (http://dominik.pfaffenbauer.at)
 * @license    http://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Console\Command;

use CoreShop\IndexService;
use CoreShop\Model\Product;
use Pimcore\Console\AbstractCommand;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class IndexCommand extends AbstractCommand
{
    /**
     * configure command
     */
    protected function configure()
    {
        $this
            ->setName('coreshop:index')
            ->setDescription('Reindex all Products');
    }

    /**
     * Execute command
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $allProducts = Product::getList();
        $allProducts->setObjectTypes(array(Product::OBJECT_TYPE_OBJECT, Product::OBJECT_TYPE_VARIANT));
        $allProducts = $allProducts->load();

        $steps = count($allProducts);

        $this->output->writeln("<info>Found ".count($allProducts)." Products to index</info>");

        $progress = new ProgressBar($output, $steps);
        $progress->start();

        foreach ($allProducts as $product) {
            IndexService::getIndexService()->updateIndex($product);
            $progress->advance();
        }
        $this->output->writeln("");
        $this->output->writeln("<info>Done</info>");
    }
}
