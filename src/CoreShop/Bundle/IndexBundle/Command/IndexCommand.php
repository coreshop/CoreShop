<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under two different licenses:
 *  - GNU General Public License version 3 (GPLv3)
 *  - CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Bundle\IndexBundle\Command;

use CoreShop\Component\Index\Model\IndexableInterface;
use CoreShop\Component\Index\Model\IndexInterface;
use CoreShop\Component\Index\Service\IndexUpdaterServiceInterface;
use CoreShop\Component\Pimcore\BatchProcessing\DataObjectBatchListing;
use CoreShop\Component\Resource\Repository\RepositoryInterface;
use Pimcore\Model\DataObject\AbstractObject;
use Pimcore\Model\DataObject\Listing;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

final class IndexCommand extends Command
{
    public function __construct(
        private RepositoryInterface $indexRepository,
        private IndexUpdaterServiceInterface $indexUpdater,
        private EventDispatcherInterface $eventDispatcher,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('coreshop:index')
            ->setDescription('Reindex all Objects')
            ->addOption(
                'unpublished',
                'u',
                InputOption::VALUE_NONE,
                'Include unpublished objects',
            )
            ->addArgument(
                'indices',
                InputArgument::IS_ARRAY | InputArgument::OPTIONAL,
                'IDs or names of Indices which are re-indexed',
                null,
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $indices = $classesToUpdate = [];
        $indexIds = $input->getArgument('indices');
        $includeUnpublished = $input->getOption('unpublished') === true;

        if (empty($indexIds)) {
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
                    sprintf('<info>No Indices found for %s</info>', implode(', ', $indexIds)),
                );
                $this->dispatchInfo(
                    'status',
                    sprintf('No Indices found for %s', implode(', ', $indexIds)),
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
             * @psalm-var class-string $list
             */
            $list = '\Pimcore\Model\DataObject\\' . $class . '\Listing';
            /**
             * @var Listing $list
             *
             * @psalm-suppress UndefinedClass
             */
            $list = new $list();

            $list->setObjectTypes([AbstractObject::OBJECT_TYPE_OBJECT, AbstractObject::OBJECT_TYPE_VARIANT]);
            $list->setUnpublished($includeUnpublished);

            $perLoop = 10;

            $batchList = new DataObjectBatchListing($list, $perLoop);

            $batchLists[$class] = $batchList;

            $total += $batchList->count();
        }

        $this->dispatchInfo('start', $total);

        /**
         * @var DataObjectBatchListing $batchList
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
                '%current%/%max% [%bar%] %percent:3s%% (%elapsed:6s%/%estimated:-6s%) %memory:6s%: %message%',
            );
            $progress->start();

            foreach ($batchList as $object) {
                $progress->setMessage(sprintf('Index %s (%s)', $object->getFullPath(), $object->getId()));
                $progress->advance();

                $this->dispatchInfo('progress', sprintf('Index %s (%s)', $object->getFullPath(), $object->getId()));

                if (!$object instanceof IndexableInterface) {
                    continue;
                }

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

    private function dispatchInfo(string $type, $info): void
    {
        $this->eventDispatcher->dispatch(new GenericEvent($info), sprintf('coreshop.index.%s', $type));
    }
}
