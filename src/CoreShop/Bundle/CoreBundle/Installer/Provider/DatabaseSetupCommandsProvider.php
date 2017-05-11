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

namespace CoreShop\Bundle\CoreBundle\Installer\Provider;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\DBAL\Schema\AbstractSchemaManager;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Style\SymfonyStyle;

final class DatabaseSetupCommandsProvider implements DatabaseSetupCommandsProviderInterface
{
    /**
     * @var Registry
     */
    private $doctrineRegistry;

    /**
     * @param Registry $doctrineRegistry
     */
    public function __construct(Registry $doctrineRegistry)
    {
        $this->doctrineRegistry = $doctrineRegistry;
    }

    /**
     * {@inheritdoc}
     */
    public function getCommands(InputInterface $input, OutputInterface $output, QuestionHelper $questionHelper)
    {
        return array_merge($this->getRequiredCommands($input, $output, $questionHelper), [
            'cache:clear' => ['--no-warmup' => true]
        ]);
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @param QuestionHelper  $questionHelper
     *
     * @return array
     */
    private function getRequiredCommands(InputInterface $input, OutputInterface $output, QuestionHelper $questionHelper)
    {
        if ($input->getOption('no-interaction')) {
            $commands['doctrine:schema:update'] = ['--force' => true];
        }

        return $this->setupDatabase($input, $output, $questionHelper);
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @param QuestionHelper  $questionHelper
     *
     * @return array
     */
    private function setupDatabase(InputInterface $input, OutputInterface $output, QuestionHelper $questionHelper)
    {
        $outputStyle = new SymfonyStyle($input, $output);

        if (!$this->isSchemaPresent()) {
            return ['doctrine:schema:create'];
        }

        $outputStyle->writeln('Seems like your database contains schema.');
        $outputStyle->writeln('<error>Warning! This action will erase your database.</error>');
        $question = new ConfirmationQuestion('Do you want to reset your CoreShop scheme it? (y/N) ', false);
        if ($questionHelper->ask($input, $output, $question)) {
            return [
                'doctrine:schema:drop' => ['--force' => true],
                'doctrine:schema:create',
            ];
        }

        return [];
    }

    /**
     * @return bool
     */
    private function isSchemaPresent()
    {
        return in_array('coreshop_store', $this->getSchemaManager()->listTableNames());
    }

    /**
     * @return AbstractSchemaManager
     */
    private function getSchemaManager()
    {
        return $this->doctrineRegistry->getManager()->getConnection()->getSchemaManager();
    }
}
