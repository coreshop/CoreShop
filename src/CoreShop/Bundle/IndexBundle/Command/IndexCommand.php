<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2019 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\IndexBundle\Command;

use CoreShop\Component\Index\Model\IndexInterface;
use CoreShop\Component\Index\Service\IndexUpdaterServiceInterface;
use CoreShop\Component\Pimcore\BatchProcessing\BatchListing;
use CoreShop\Component\Resource\Repository\RepositoryInterface;
use Pimcore\Model\DataObject\AbstractObject;
use Pimcore\Model\DataObject\Listing;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

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
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * @param RepositoryInterface          $indexRepository
     * @param IndexUpdaterServiceInterface $indexUpdater
     * @param EventDispatcherInterface     $eventDispatcher
     */
    public function __construct(
        RepositoryInterface $indexRepository,
        IndexUpdaterServiceInterface $indexUpdater,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->indexRepository = $indexRepository;
        $this->indexUpdater = $indexUpdater;
        $this->eventDispatcher = $eventDispatcher;

        parent::__construct();
    }

    /**
     * configure command.
     */
    protected function configure()
    {
        $this
            ->setName('coreshop:index')
            ->setDescription('Reindex all Objects')
            ->addArgument(
                'indices',
                InputArgument::IS_ARRAY | InputArgument::OPTIONAL,
                'IDs or names of Indices which are re-indexed',
                null
            );
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
        $indices = $classesToUpdate = [];
        $indexIds = $input->getArgument('indices');

        if (null === $indexIds) {
            $indices = $this->indexRepository->findAll();
        } else {
            foreach ($indexIds as $id) {
                if (is_numeric($id)) {
                    $index = $this->indexRepository->find($id);
                } else {
                    $index = $this->indexRepository->findOneBy(['name' => $id]);
                }

                if (null === $index) {
                    continue;
                }

                $indices[] = $index;
            }
        }

        if (empty($indices)) {
            if (null === $indexIds) {
                $output->writeln('<info>No Indices available, you have to first create an Index.</info>');
                $this->dispatchInfo('status', 'No Indices available, you have to first create an Index.');
            } else {
                $output->writeln(
                    sprintf('<info>No Indices found for %s</info>', implode(', ', $indexIds))
                );
                $this->dispatchInfo(
                    'status',
                    sprintf('No Indices found for %s', implode(', ', $indexIds))
                );
            }

            return 0;
        }

        /**
         * @var IndexInterface $index
         */
        foreach ($indices as $index) {
            if (!in_array($index->getClass(), $classesToUpdate)) {
                $classesToUpdate[] = $index->getClass();
            }
        }

        $this->dispatchInfo('classes', 'Classes: ' . implode(', ', $classesToUpdate));

        $batchLists = [];
        $total = 0;

        foreach ($classesToUpdate as $class) {
            $class = ucfirst($class);

            /**
             * @var Listing $list
             */
            $list = '\Pimcore\Model\DataObject\\' . $class . '\Listing';
            $list = new $list();

            $list->setObjectTypes([AbstractObject::OBJECT_TYPE_OBJECT, AbstractObject::OBJECT_TYPE_VARIANT]);
            $perLoop = 10;

            $batchList = new BatchListing($list, $perLoop);

            $batchLists[$class] = $batchList;

            $total += $batchList->count();
        }

        $this->dispatchInfo('start', $total);

        /**
         * @var BatchListing $batchList
         */
        foreach ($batchLists as $class => $batchList) {
            $total = $batchList->count();

            if (0 === $total) {
                $output->writeln(sprintf('<info>No Object found for class %s</info>', $class));
                $this->dispatchInfo('status', sprintf('No Object found for class %s', $class));

                continue;
            }

            $this->dispatchInfo('status', sprintf('Processing %s Objects of class "%s"', $total, $class));

            $output->writeln(sprintf('<info>Processing %s Objects of class "%s"</info>', $total, $class));
            $progress = new ProgressBar($output, $total);
            $progress->setFormat(
                '%current%/%max% [%bar%] %percent:3s%% (%elapsed:6s%/%estimated:-6s%) %memory:6s%: %message%'
            );
            $progress->start();

            foreach ($batchList as $object) {
                $progress->setMessage(sprintf('Index %s (%s)', $object->getFullPath(), $object->getId()));
                $progress->advance();

                $this->dispatchInfo('progress', sprintf('Index %s (%s)', $object->getFullPath(), $object->getId()));

                $this->indexUpdater->updateIndices($object);
            }

            $progress->finish();
            $output->writeln('');

            $this->dispatchInfo('status', sprintf('Processed %s Objects of class "%s"', $total, $class));
        }

        $output->writeln('');
        $output->writeln('<info>Done</info>');

        $this->dispatchInfo('finished', sprintf('Finished Indexing Classes %s', implode(', ', $classesToUpdate)));

        return 0;
    }

    /**
     * @param string $type
     * @param string $info
     */
    private function dispatchInfo(string $type, string $info)
    {
        $this->eventDispatcher->dispatch(sprintf('coreshop.index.%s', $type), new GenericEvent($info));
    }
}
