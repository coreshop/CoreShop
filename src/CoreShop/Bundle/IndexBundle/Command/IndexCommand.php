<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\IndexBundle\Command;

use CoreShop\Component\Index\Model\IndexInterface;
use CoreShop\Component\Index\Service\IndexUpdaterServiceInterface;
use CoreShop\Component\Resource\Repository\RepositoryInterface;
use Pimcore\Model\DataObject\AbstractObject;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class IndexCommand extends Command
{
    /**
     * @var RepositoryInterface
     */
    protected $indexRepository;

    /**
     * @var IndexUpdaterServiceInterface
     */
    protected $indexUpdater;

    /**
     * @param RepositoryInterface $indexRepository
     * @param IndexUpdaterServiceInterface $indexUpdater
     */
    public function __construct(RepositoryInterface $indexRepository, IndexUpdaterServiceInterface $indexUpdater)
    {
        $this->indexRepository = $indexRepository;
        $this->indexUpdater = $indexUpdater;

        parent::__construct();
    }

    /**
     * configure command.
     */
    protected function configure()
    {
        $this
            ->setName('coreshop:index')
            ->setDescription('Reindex all Objects');
    }

    /**
     * Execute command.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $indices = $this->indexRepository->findAll();
        $classesToUpdate = [];

        /**
         * @var $index IndexInterface
         */
        foreach ($indices as $index) {
            if (!in_array($index->getClass(), $classesToUpdate)) {
                $classesToUpdate[] = $index->getClass();
            }
        }

        $classProgress = new ProgressBar($output, count($classesToUpdate));
        $classProgress->setProgressCharacter('#');

        foreach ($classesToUpdate as $class) {
            $list = '\Pimcore\Model\DataObject\\'.$class.'\Listing';
            $list = new $list();

            $list->setObjectTypes([AbstractObject::OBJECT_TYPE_OBJECT, AbstractObject::OBJECT_TYPE_VARIANT]);
            $list = $list->load();

            $steps = count($list);

            $output->writeln(sprintf('<info>Found %s Objects ("%s") to index</info>', $steps, $class));

            $progress = new ProgressBar($output, $steps);
            $progress->start();

            foreach ($list as $object) {
                $this->indexUpdater->updateIndices($object);

                $progress->advance();
            }

            $progress->finish();
            $classProgress->advance();
        }

        $output->writeln('');
        $output->writeln('<info>Done</info>');

        return 0;
    }
}
