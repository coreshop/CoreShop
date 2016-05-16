<?php
/**
 * CoreShop.
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

use Pimcore\Console\AbstractCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class BuildCommand extends AbstractCommand
{
    /**
     * Configure Command.
     */
    protected function configure()
    {
        $this
            ->setName('coreshop:build')
            ->setDescription('Build a CoreShop version')
            ->addOption(
                'build', 'b',
                InputOption::VALUE_NONE,
                'make the new build'
            )->addOption(
                'dry-run', 'd',
                InputOption::VALUE_NONE,
                'Dry-run'
            )->addOption(
                'no-commit', 'c',
                InputOption::VALUE_NONE,
                'Make no commit on git'
            );
    }

    /**
     * Execute Command.
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        //include(__DIR__ . "/../config/startup.php");
        $dryRun = $input->getOption('dry-run');

        chdir(CORESHOP_PATH);

        if ($dryRun) {
            $this->output->writeln('<info>---------- DRY-RUN ----------</info>');
        }

        if (!defined('CORESHOP_CHANGED_FILES')) {
            define('CORESHOP_CHANGED_FILES', CORESHOP_BUILD_DIRECTORY.'/changedFiles.txt');
        }
        if (!defined('CORESHOP_DELETED_FILES')) {
            define('CORESHOP_DELETED_FILES', CORESHOP_BUILD_DIRECTORY.'/deletedFiles.txt');
        }

        if ($input->getOption('build')) {
            $version = \CoreShop\Version::getVersion();

            $buildNumber = \CoreShop\Version::getBuildNumber();
            $buildNumber = $buildNumber + 1;

            $timestamp = time();
            $gitRevision = $this->getGitRevision();

            $changedFiles = $this->getChangedFiles();
            $deletedFiles = $this->getDeletedFiles();

            if (count($changedFiles) > 0 || count($deletedFiles) > 0) {
                if (count($changedFiles) > 0) {
                    $buildFolder = CORESHOP_BUILD_DIRECTORY.'/'.$buildNumber;
                    $buildFolderScripts = $buildFolder.'/scripts';
                    $buildFolderFiles = $buildFolder.'/files';

                    if (!$dryRun) {
                        if (!is_dir($buildFolder)) {
                            mkdir($buildFolder);
                        }

                        if (!is_dir($buildFolderScripts)) {
                            mkdir($buildFolderScripts);
                        }

                        if (!is_dir($buildFolderFiles)) {
                            mkdir($buildFolderFiles);
                        }
                    }

                    //Copy all Files into files folder
                    foreach ($changedFiles as $file) {
                        $file = str_replace("\n", '', $file);
                        $sourceFile = CORESHOP_PATH.'/'.$file;
                        $destinationFile = $buildFolderFiles.'/'.$file.'.build';
                        $pathInfo = pathinfo($destinationFile);

                        if (!$dryRun) {
                            if (!is_dir($pathInfo['dirname'])) {
                                mkdir($pathInfo['dirname'], 0755, true);
                            }

                            if (file_exists($destinationFile)) {
                                unlink($destinationFile);
                            }

                            copy($sourceFile, $destinationFile);
                        }

                        $this->output->writeln("copy file <comment>$file</comment> to <comment>$destinationFile</comment>");
                    }
                }

                if (count($deletedFiles) > 0) {
                    foreach ($deletedFiles as $file) {
                        $file = str_replace("\n", '', $file);

                        $this->output->writeln("file has been deleted <comment>$file</comment>");
                    }
                }

                if (!$dryRun) {
                    rename(CORESHOP_CHANGED_FILES, CORESHOP_BUILD_DIRECTORY.'/'.$buildNumber.'/changedFiles.txt');
                    rename(CORESHOP_DELETED_FILES, CORESHOP_BUILD_DIRECTORY.'/'.$buildNumber.'/deletedFiles.txt');
                }

                $this->updateVersionFile($dryRun, $buildNumber, $version, $timestamp, $gitRevision);
                $this->writeBuildToBuildFile($dryRun, $buildNumber, $version, $timestamp, $gitRevision);
                $this->gitAddAndCommit($dryRun, $buildNumber, $input->getOption('no-commit'));
                $this->uploadToUpdateServer($dryRun);
            } else {
                //delete "changedfiles" when no files has been changed
                unlink(CORESHOP_CHANGED_FILES);

                $this->output->writeln('<info>no files changed, no build will be created</info>');
            }

            if ($dryRun) {
                unlink(CORESHOP_CHANGED_FILES);
                unlink(CORESHOP_DELETED_FILES);
            }
        }
    }

    /**
     * Update Plugin XML File.
     *
     * @param $dryRun
     * @param $buildNumber
     * @param $version
     * @param $timestamp
     * @param $gitRevision
     *
     * @throws \Exception
     * @throws \Zend_Config_Exception
     */
    private function updateVersionFile($dryRun, $buildNumber, $version, $timestamp, $gitRevision)
    {
        if (!$dryRun) {
            $config = \Pimcore\ExtensionManager::getPluginConfig('CoreShop');

            $config['plugin']['pluginRevision'] = $buildNumber;
            $config['plugin']['pluginVersion'] = $version;
            $config['plugin']['pluginBuildTimestamp'] = $timestamp;
            $config['plugin']['pluginGitRevision'] = $gitRevision;

            $config = new \Zend_Config($config, true);
            $writer = new \Zend_Config_Writer_Xml(array(
                'config' => $config,
                'filename' => CORESHOP_PLUGIN_CONFIG,
            ));
            $writer->write();

            //Copy new plugin.xml to BuildFolder
            copy(CORESHOP_PLUGIN_CONFIG, CORESHOP_BUILD_DIRECTORY.'/'.$buildNumber.'/files/plugin.xml.build');
        }

        $this->output->writeln('wrote new plugin.xml');
        $this->output->writeln("Build: <info>$buildNumber</info>");
        $this->output->writeln("Version: <info>$version</info>");
        $this->output->writeln("Timestamp: <info>$timestamp</info>");
        $this->output->writeln("Git Revision: <info>$gitRevision</info>");
    }

    /**
     * Return current GIT-Revision.
     *
     * @return string
     */
    private function getGitRevision()
    {
        return str_replace("\n", '', \Pimcore\Tool\Console::exec('git rev-parse HEAD'));
    }

    /**
     * get all changed files.
     *
     * @return array
     */
    private function getChangedFiles()
    {
        $gitDirectory = CORESHOP_PATH;
        $gitRevision = \CoreShop\Version::getGitRevision();

        if (!$gitRevision) {
            $gitRevision = '383e78d';
        }

        \Pimcore\Tool\Console::exec("git diff-tree -r --no-commit-id --name-only --diff-filter=ACMRT $gitRevision HEAD $gitDirectory | sed '/^build/ d'", CORESHOP_CHANGED_FILES);

        return file(CORESHOP_CHANGED_FILES);
    }

    /**
     * get all deleted files.
     *
     * @return array
     */
    private function getDeletedFiles()
    {
        $gitDirectory = CORESHOP_PATH;
        $gitRevision = \CoreShop\Version::getGitRevision();

        if (!$gitRevision) {
            $gitRevision = '383e78d';
        }

        \Pimcore\Tool\Console::exec("git diff-tree -r --no-commit-id --name-only --diff-filter=D $gitRevision HEAD $gitDirectory | sed '/^build/ d'", CORESHOP_DELETED_FILES);

        return file(CORESHOP_DELETED_FILES);
    }

    /**
     * add files to git and make a commit.
     *
     * @param $dryRun
     * @param $buildNumber
     * @param $disableCommit
     */
    private function gitAddAndCommit($dryRun, $buildNumber, $disableCommit = false)
    {
        if (!$dryRun) {
            \Pimcore\Tool\Console::exec('git add plugin.xml');

            if (!$disableCommit) {
                \Pimcore\Tool\Console::exec("git commit -m \"Build Version $buildNumber\"");
            }
        }

        $this->output->writeln('made git add');

        if (!$disableCommit) {
            $this->output->writeln('made git commit');
        }
    }

    /**
     * Upload files to update server.
     *
     * @param $dryRun
     */
    private function uploadToUpdateServer($dryRun)
    {
        if (!$dryRun) {
            chdir(CORESHOP_PATH);
            echo shell_exec('build/copy-to-update-server.sh');
        }
    }

    /**
     * write build to build file.
     *
     * @param $dryRun
     * @param $buildNumber
     * @param $version
     * @param $timestamp
     * @param $gitRevision
     */
    private function writeBuildToBuildFile($dryRun, $buildNumber, $version, $timestamp, $gitRevision)
    {
        if (!$dryRun) {
            $buildsFile = file_get_contents(CORESHOP_BUILD_DIRECTORY.'/builds.json');

            try {
                $json = \Zend_Json::decode($buildsFile);
            } catch (\Exception $ex) {
                $json = array(
                    'builds' => array(),
                );
            }

            $json['builds'][] = array(
                'number' => $buildNumber,
                'version' => $version,
                'timestamp' => $timestamp,
                'gitRevision' => $gitRevision,
            );

            $buildsFile = \Zend_Json::encode($json);

            file_put_contents(CORESHOP_BUILD_DIRECTORY.'/builds.json', $buildsFile);
        }

        $this->output->writeln('wrote build to builds.json');
    }
}
