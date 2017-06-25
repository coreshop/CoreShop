<?php

namespace CoreShop\Bundle\ResourceBundle\Installer;

use Composer\Autoload\ClassLoader;
use CoreShop\Bundle\ResourceBundle\CoreShopResourceBundle;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Pimcore\Model\Object;

final class PimcoreClassInstaller implements ResourceInstallerInterface
{
    /**
     * @var KernelInterface
     */
    protected $kernel;

    /**
     * @param KernelInterface $kernel
     */
    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    /**
     * {@inheritdoc}
     */
    public function installResources(OutputInterface $output, $applicationName = null)
    {
        $parameter = $applicationName ? sprintf('%s.pimcore.classes', $applicationName) : 'coreshop.pimcore';

        if ($this->kernel->getContainer()->hasParameter($parameter)) {
            $pimcoreClasses = $this->kernel->getContainer()->getParameter($parameter);
            $fieldCollections = [];
            $bricks = [];
            $classes = [];

            $classLoader = new ClassLoader();
            $classLoader->register(true);

            foreach ($pimcoreClasses as $pimcoreModel) {
                $modelName = explode('\\', $pimcoreModel['classes']['model']);
                $modelName = $modelName[count($modelName) - 1];

                if (array_key_exists("install_file", $pimcoreModel['classes'])) {
                    $type = $pimcoreModel['classes']['type'];

                    try {
                        $file = $this->kernel->locateResource($pimcoreModel['classes']['install_file']);

                        if ($type === CoreShopResourceBundle::PIMCORE_MODEL_TYPE_OBJECT) {
                            //$this->createClass($file, $modelName, true);
                            $classes[] = [
                                'model' => $modelName,
                                'file' => $file
                            ];
                        } else if ($type === CoreShopResourceBundle::PIMCORE_MODEL_TYPE_FIELD_COLLECTION) {
                            $fieldCollections[] = [
                                'model' => $modelName,
                                'file' => $file
                            ];
                        } else if ($type === CoreShopResourceBundle::PIMCORE_MODEL_TYPE_BRICK) {
                            $bricks[] = [
                                'model' => $modelName,
                                'file' => $file
                            ];
                        }

                    } catch (\InvalidArgumentException $ex) {
                        //File not found, continue with next, maybe add some logging?
                    }
                }
            }

            $progress = new ProgressBar($output);
            $progress->setBarCharacter('<info>░</info>');
            $progress->setEmptyBarCharacter(' ');
            $progress->setProgressCharacter('<comment>░</comment>');
            $progress->setFormat(' %current%/%max% [%bar%] %percent:3s%% %message%');

            $progress->start(count($pimcoreClasses));

            foreach ($fieldCollections as $fc) {
                $progress->setMessage(sprintf('<error>Install Fieldcollection %s (%s)</error>', $fc['model'], $fc['file']));

                $this->createFieldCollection($fc['file'], $fc['model']);

                $progress->advance();
            }

            foreach ($classes as $class) {
                $progress->setMessage(sprintf('<error>Install Class %s (%s)</error>', $class['model'], $class['file']));

                $this->createClass($class['file'], $class['model']);

                $progress->advance();
            }

            foreach ($bricks as $brick) {
                $progress->setMessage(sprintf('<error>Install Brick %s (%s)</error>', $brick['model'], $brick['file']));

                $this->createBrick($brick['file'], $brick['model']);

                $progress->advance();
            }

            $classLoader->addPsr4("Pimcore\\Model\\Object\\", PIMCORE_CLASS_DIRECTORY . "/Object");

            $progress->finish();
        }
    }

    /**
     * @param $josnFile
     * @param $brickName
     */
    private function createBrick($josnFile, $brickName) {
        //TODO
    }

    /**
     * @param $jsonFile
     * @param $className
     * @param bool $updateClass
     *
     * @return Object\ClassDefinition
     */
    private function createClass($jsonFile, $className, $updateClass = false)
    {
        $tempClass = new Object\ClassDefinition();
        $id = $tempClass->getDao()->getIdByName($className);
        $class = null;

        if ($id) {
            $class = Object\ClassDefinition::getById($id);
        }

        if (!$class || $updateClass) {
            $json = file_get_contents($jsonFile);

            if (!$class) {
                $class = Object\ClassDefinition::create();
            }

            $class->setName($className);
            $class->setUserOwner(0);

            Object\ClassDefinition\Service::importClassDefinitionFromJson($class, $json, true);

            /**
             * Fixes Object Brick Stuff.
             */
            $list = new Object\Objectbrick\Definition\Listing();
            $list = $list->load();

            if (!empty($list)) {
                foreach ($list as $brickDefinition) {
                    $clsDefs = $brickDefinition->getClassDefinitions();
                    if (!empty($clsDefs)) {
                        foreach ($clsDefs as $cd) {
                            if ($cd['classname'] == $class->getId()) {
                                $brickDefinition->save();
                            }
                        }
                    }
                }
            }

            return $class;
        }

        return $class;
    }

    /**
     * @param $name
     * @param null $jsonFile
     *
     * @return mixed|null|Object\Fieldcollection\Definition
     */
    private function createFieldCollection($jsonFile, $name)
    {
        try {
            $fieldCollection = Object\Fieldcollection\Definition::getByKey($name);
        } catch (\Exception $e) {
            $fieldCollection = new Object\Fieldcollection\Definition();
            $fieldCollection->setKey($name);
        }

        $json = file_get_contents($jsonFile);

        Object\ClassDefinition\Service::importFieldCollectionFromJson($fieldCollection, $json, true);

        return $fieldCollection;
    }
}