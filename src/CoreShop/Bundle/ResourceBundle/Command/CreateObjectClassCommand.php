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

namespace CoreShop\Bundle\ResourceBundle\Command;

use CoreShop\Bundle\ResourceBundle\Generator\PimcoreResourceClassGenerator;
use CoreShop\Component\Pimcore\DataObject\Migrate;
use Pimcore\Cache;
use Sensio\Bundle\GeneratorBundle\Command\GeneratorCommand;
use Sensio\Bundle\GeneratorBundle\Command\Validators;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Yaml\Yaml;

final class CreateObjectClassCommand extends GeneratorCommand
{
    /**
     * @var KernelInterface
     */
    protected $kernel;

    /**
     * @var array
     */
    protected $classes;

    /**
     * @param KernelInterface $kernel
     * @param array           $classes
     */
    public function __construct(KernelInterface $kernel, array $classes)
    {
        $this->kernel = $kernel;
        $this->classes = $classes;

        parent::__construct();
    }

    /**
     * configure command.
     */
    protected function configure()
    {
        $this
            ->setName('coreshop:generate:class')
            ->setDescription('Generates a Custom Object Class for Resource Classes')
            ->addOption(
                'classType',
                'c',
                InputOption::VALUE_REQUIRED,
                'Class you wish to create a Custom Class of.'
            )
            ->addOption(
                'prefix',
                'p',
                InputOption::VALUE_REQUIRED,
                'Prefix for your Class'
            )
            ->addOption(
                'bundle',
                'b',
                InputOption::VALUE_REQUIRED,
                'Bundle to create Class In',
                'AppBundle'
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function getSkeletonDirs(BundleInterface $bundle = null)
    {
        $dirs = parent::getSkeletonDirs($bundle);

        array_unshift($dirs, __DIR__ . '/../Resources/skeleton');

        return $dirs;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        Cache::disable();

        /*
         *
         * coreshop.cart' => array(
                    'driver' => 'pimcore',
                    'path' => 'coreshop/carts',
                    'classes' => array(
                        'install_file' => '@CoreShopCoreBundle/Resources/install/pimcore/classes/CoreShopOrderBundle/CoreShopCart.json',
                        'model' => 'Pimcore\\Model\\DataObject\\CoreShopCart',
                        'interface' => 'CoreShop\\Component\\Order\\Model\\CartInterface',
                        'factory' => 'CoreShop\\Component\\Resource\\Factory\\PimcoreFactory',
                        'repository' => 'CoreShop\\Bundle\\OrderBundle\\Pimcore\\Repository\\CartRepository',
                        'type' => 'object',
                    ),
                ),
         * */

        $helper = $this->getHelper('question');

        $classType = $input->getOption('classType');
        $prefix = $input->getOption('prefix');
        $pluginName = ucfirst($input->getOption('bundle'));
        $bundle = null;

        if (is_string($pluginName)) {
            $bundle = Validators::validateBundleName($pluginName);

            try {
                $bundle = $this->kernel->getBundle($bundle);
            } catch (\Exception $e) {
                $output->writeln(sprintf('<bg=red>Bundle "%s" does not exist.</>', $bundle));

                return 1;
            }
        }

        if (null === $bundle) {
            throw new \InvalidArgumentException('Could not determine the right bundle');
        }

        $availableClasses = $this->classes;

        if (!array_key_exists($classType, $availableClasses)) {
            throw new \Exception(sprintf('Class Type %s not found. Found these: ' . implode(', ', array_keys($availableClasses)), $classType));
        }

        list($applicationName, $shortClassType) = explode('.', $classType);

        $oldClassNameInfo = $availableClasses[$classType];
        $fullOldClassName = $oldClassNameInfo['classes']['model'];
        $oldPimcoreClassName = explode('\\', $fullOldClassName);
        $oldPimcoreClassName = end($oldPimcoreClassName);
        $parentClass = get_parent_class($fullOldClassName);
        $oldClassNameArray = explode('\\', $parentClass);
        $className = $prefix . end($oldClassNameArray);

        $newParentClass = $pluginName . '\\Model\\' . $className;

        $namespacePath = explode('\\', $newParentClass);
        array_pop($namespacePath);

        $pathForFile = $bundle->getPath() . '/Model/' . $className . '.php';

        $question = new ConfirmationQuestion("<info>You are going to create a new PHP File $pathForFile and a new Object-Class ($oldPimcoreClassName) for ($className) Are you sure? (y/n)</info>", true);

        if (!$helper->ask($input, $output, $question)) {
            return 1;
        }

        $this->getGenerator()->generateResourceClass($bundle, $className, $parentClass);
        Migrate::migrateClass($oldPimcoreClassName, $className, [
            'delete_existing_class' => true,
            'parentClass' => $parentClass,
        ]);

        $question = new ConfirmationQuestion("Do you want to migrate the existing data from $parentClass to $newParentClass? (y/n)", true);

        if ($helper->ask($input, $output, $question)) {
            Migrate::migrateData($oldPimcoreClassName, $className);
        }

        $configFile = sprintf('%s/Resources/config/coreshop.yml', $bundle->getPath());

        $configEntry = [
            $oldClassNameInfo['alias'] => [
                'pimcore' => [
                    $shortClassType => [
                        'classes' => [
                            'model' => 'Pimcore\Model\DataObject\\' . $className,
                        ],
                    ],
                ],
            ],
        ];

        if (file_exists($configFile)) {
            $yamlConfig = Yaml::parse(file_get_contents($configFile));
            $configEntry = array_merge_recursive($yamlConfig, $configEntry);
        }

        file_put_contents($configFile, Yaml::dump($configEntry, 10));

        Cache::clearAll();

        $output->writeln(sprintf('<info>All you need to do now, is to load the config file %s</info>', $configFile));

        $output->writeln('');
        $output->writeln('<info>Done</info>');

        return 0;
    }

    protected function createGenerator()
    {
        return new PimcoreResourceClassGenerator();
    }
}
