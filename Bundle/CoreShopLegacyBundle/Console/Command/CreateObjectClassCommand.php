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
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\CoreShopLegacyBundle\Console\Command;

use CoreShop\Bundle\CoreShopLegacyBundle\Exception;
use Pimcore\API\Plugin\Broker;
use Pimcore\Cache;
use Pimcore\Console\AbstractCommand;
use Pimcore\Db;
use Pimcore\ExtensionManager;
use Pimcore\File;
use Pimcore\Model\Object\ClassDefinition;
use Pimcore\Model\Object\Objectbrick;
use Pimcore\Model\Object\Fieldcollection;
use Pimcore\Tool;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

/**
 * Class CreateObjectClassCommand
 * @package CoreShop\Bundle\CoreShopLegacyBundle\Console\Command
 */
class CreateObjectClassCommand extends AbstractCommand
{
    /**
     * configure command.
     */
    protected function configure()
    {
        $availableClasses = array_keys(\CoreShop\Bundle\CoreShopLegacyBundle\CoreShop::getPimcoreClasses());

        $this
            ->setName('coreshop:create-object-class')
            ->setDescription('Creates a Custom Object class for CoreShop Classes')
            ->addOption(
                'classType', 'c',
                InputOption::VALUE_REQUIRED,
                'Class you wish to create a Custom Class of. Available options: ' . implode(", ", $availableClasses)
            )
            ->addOption(
                'prefix', 'p',
                InputOption::VALUE_REQUIRED,
                'Prefix for your Class'
            )
            ->addOption(
                'plugin', 'pl',
                InputOption::VALUE_REQUIRED,
                'Plugin to create Class In',
                'website'
            )
        ;
    }

    /**
     * Execute command.
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @throws Exception
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        Cache::disable();

        $this->disableLogging();

        $helper = $this->getHelper('question');

        $classType = $input->getOption("classType");
        $prefix = $input->getOption("prefix");
        $pluginName = ucfirst($input->getOption("plugin"));
        $isWebsite = strtolower($pluginName) === "website";

        $availableClasses = \CoreShop\Bundle\CoreShopLegacyBundle\CoreShop::getPimcoreClasses();

        if (!array_key_exists($classType, $availableClasses)) {
            throw new Exception("Class Type $classType not found");
        }

        $oldClassNameInfo = $availableClasses[$classType];
        $fullOldClassName = $oldClassNameInfo['pimcoreClass'];
        $oldClassNameArray = explode('\\', $fullOldClassName);
        $oldClassName = end($oldClassNameArray);
        $fullNewClassName = str_replace("CoreShop", $prefix, $oldClassNameInfo['pimcoreClass']);
        $newClassNameArray = explode('\\', $fullNewClassName);
        $newClassName = end($newClassNameArray);
        $coreShopClassName = $oldClassNameInfo['coreShopClass'];

        $baseNamespace = str_replace("CoreShop\Bundle\CoreShopLegacyBundle\\Model\\", "", $coreShopClassName);
        $newParentClass = $pluginName . "\\Model\\" . $baseNamespace;

        $extendClass = $oldClassNameInfo['coreShopClass'];
        $classNameArray = explode('\\', $extendClass);
        $className = end($classNameArray);

        $namespacePath = explode("\\", $newParentClass);
        array_pop($namespacePath);
        $namespace = implode("\\", $namespacePath);


        if (!$isWebsite) {
            if (!Broker::getInstance()->hasPlugin($pluginName . '\\Plugin')) {
                $question = new ConfirmationQuestion("Plugin with name $pluginName not found, should I create it? (y/n)", false);
                if (!$helper->ask($input, $output, $question)) {
                    $this->output->writeln("<error>Aborting due to not creating the plugin!</error>");
                    return 1;
                }

                $this->createPlugin($pluginName);
            }
        }

        $fileName = str_replace("\\", "/", $baseNamespace);

        if ($isWebsite) {
            $pathForFile = PIMCORE_WEBSITE_PATH . "/models/Website/Model/" . $fileName . ".php";
        } else {
            $pathForFile = PIMCORE_PLUGINS_PATH . "/" . $pluginName . "/lib/" . $pluginName . "/Model/" . $fileName . ".php";
        }

        $question = new ConfirmationQuestion("<info>You are going to create a new PHP File $pathForFile and a new Object-Class ($newClassName) for ($oldClassName) Are you sure? (y/n)</info>", true);

        if (!$helper->ask($input, $output, $question)) {
            return 1;
        }

        $this->createPhpClass($namespace, $coreShopClassName, $className, $newClassName, $pathForFile);
        $this->createClassDefinition($oldClassName, '\\' . $namespace . '\\' . $className, $newClassName);

        $question = new ConfirmationQuestion("Do you want to migrate the existing data from $oldClassName to $newClassName? (y/n)", true);

        if ($helper->ask($input, $output, $question)) {
            $this->migrateDataFrom($oldClassName, $newClassName);
        }

        $diPath = \Pimcore\Config::locateConfigFile("di.php");

        $this->output->writeln("<comment>PLEASE ADD THIS LINE TO YOUR di.php ($diPath): </comment>");
        $this->output->writeln("<comment>'$coreShopClassName' => DI\Object('$namespace\\$className')</comment>");

        Cache::clearAll();

        $this->output->writeln('');
        $this->output->writeln('<info>Done</info>');

        return 0;
    }

    /**
     * @param string $name
     * @return bool
     */
    protected function createPlugin($name)
    {
        $examplePluginPath = realpath(PIMCORE_PATH . "/modules/extensionmanager/example-plugin");
        $pluginDestinationPath = realpath(PIMCORE_PLUGINS_PATH) . DIRECTORY_SEPARATOR . $name;

        if (preg_match("/^[a-zA-Z0-9_]+$/", $name, $matches) && !is_dir($pluginDestinationPath)) {
            $pluginExampleFiles = rscandir($examplePluginPath);
            foreach ($pluginExampleFiles as $pluginExampleFile) {
                if (!is_file($pluginExampleFile)) {
                    continue;
                }
                $newPath = $pluginDestinationPath . str_replace($examplePluginPath . DIRECTORY_SEPARATOR . 'Example', '', $pluginExampleFile);
                $newPath = str_replace(DIRECTORY_SEPARATOR . "Example" . DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR . $name . DIRECTORY_SEPARATOR, $newPath);

                $content = file_get_contents($pluginExampleFile);

                // do some modifications in the content of the file
                $content = str_replace("Example", $name, $content);
                $content = str_replace(".example", ".".strtolower($name), $content);
                $content = str_replace("examplePlugin", strtolower($name)."Plugin", $content);
                $content = str_replace("Example Plugin", $name . " Plugin", $content);

                if (!file_exists(dirname($newPath))) {
                    File::mkdir(dirname($newPath));
                }

                File::put($newPath, $content);
            }

            ExtensionManager::enable("plugin", $name);
        }

        return true;
    }

    /**
     * @param string $namespace
     * @param $coreShopClass
     * @param $className
     * @param $pimcoreClass
     * @param string $pathForFile
     */
    protected function createPhpClass($namespace, $coreShopClass, $className, $pimcoreClass, $pathForFile)
    {
        $cd = '<?php ';
        $cd .= "\n\n";
        $cd .= "namespace $namespace;";
        $cd .= "\n\n";
        $cd .= "class " . $className . " extends \\" . $coreShopClass . " {";
        $cd .= "\n\n";
        $cd .= "\t/**\n";
        $cd .= "\t * Pimcore Object Class.\n";
        $cd .= "\t *\n";
        $cd .= "\t * @var string\n";
        $cd .= "\t */\n";
        $cd .= "\tpublic static \$pimcoreClass = '\\Pimcore\\Model\\Object\\$pimcoreClass';\n";
        $cd .= "}";

        File::putPhpFile($pathForFile, $cd);
    }

    /**
     * @param $oldPimcoreClass
     * @param string $newParentClass
     * @param $newPimcoreClassName
     *
     * @throws Exception
     */
    protected function createClassDefinition($oldPimcoreClass, $newParentClass, $newPimcoreClassName)
    {
        $newClassDefinition = ClassDefinition::getByName($newPimcoreClassName);

        if ($newClassDefinition instanceof ClassDefinition) {
            $newClassDefinition->delete();
        }

        $classDefinition = ClassDefinition::getByName($oldPimcoreClass);

        //Somehow ::generateClassDefinitionJson destroys the field-definitions, this line repairs it. So we just remove it from \Zend_Registry
        \Zend_Registry::getInstance()->offsetUnset("class_" . $classDefinition->getId());

        if (!$classDefinition instanceof ClassDefinition) {
            throw new Exception("ClassDefinition for $oldPimcoreClass not found!");
        }

        $jsonDefinition = ClassDefinition\Service::generateClassDefinitionJson($classDefinition);
        $json = \Zend_Json::decode($jsonDefinition);
        $json['parentClass'] = $newParentClass;
        $json = \Zend_Json::encode($json);

        $class = ClassDefinition::create();
        $class->setName($newPimcoreClassName);
        $class->setUserOwner(0); //0 = SystemId

        ClassDefinition\Service::importClassDefinitionFromJson($class, $json, true);

        $list = new Objectbrick\Definition\Listing();
        $list = $list->load();

        if (is_array($list)) {
            foreach ($list as $brickDefinition) {
                if ($brickDefinition instanceof Objectbrick\Definition) {
                    $clsDef = $brickDefinition->getClassDefinitions();

                    if (is_array($clsDef)) {
                        $fieldName = null;

                        foreach ($clsDef as $cd) {
                            if ($cd['classname'] == $classDefinition->getId()) {
                                $fieldName = $cd['fieldname'];

                                break;
                            }
                        }

                        if ($fieldName) {
                            $clsDef[] = [
                                'classname' => $class->getId(),
                                'fieldname' => $fieldName
                            ];

                            $brickDefinition->setClassDefinitions($clsDef);
                            $brickDefinition->save();
                        }
                    }
                }
            }
        }

        foreach ($class->getFieldDefinitions() as $fd) {
            if ($fd instanceof ClassDefinition\Data\Fieldcollections) {
                foreach ($fd->getAllowedTypes() as $type) {
                    $definition = Fieldcollection\Definition::getByKey($type);

                    $definition->createUpdateTable($class);
                }
            }
        }
    }

    /**
     * @param $oldPimcoreClass
     * @param $newPimcoreClass
     * @throws Exception
     */
    protected function migrateDataFrom($oldPimcoreClass, $newPimcoreClass)
    {
        $oldClassDefinition = ClassDefinition::getByName($oldPimcoreClass);
        $newClassDefinition = ClassDefinition::getByName($newPimcoreClass);

        if (!$oldClassDefinition) {
            throw new Exception("Could not find the ClassDefinition for class $oldPimcoreClass");
        }

        if (!$newClassDefinition) {
            throw new Exception("Could not find the ClassDefinition for class $newPimcoreClass");
        }

        $oldClassId = $oldClassDefinition->getId();
        $newClassId = $newClassDefinition->getId();

        $db = Db::get();

        $tablesToMigrate = [
            "object_query_%s" => true,
            "object_store_%s" => false,
            "object_relations_%s" => false
        ];

        foreach ($oldClassDefinition->getFieldDefinitions() as $fd) {
            if ($fd instanceof ClassDefinition\Data\Objectbricks) {
                foreach ($fd->getAllowedTypes() as $type) {
                    $definition = Objectbrick\Definition::getByKey($type);

                    $tablesToMigrate["object_brick_query_" . $definition->getKey() . "_%s"] = false;
                    $tablesToMigrate["object_brick_store_" . $definition->getKey() . "_%s"] = false;
                }
            } elseif ($fd instanceof ClassDefinition\Data\Fieldcollections) {
                foreach ($fd->getAllowedTypes() as $type) {
                    $definition = Fieldcollection\Definition::getByKey($type);

                    if ($definition instanceof Fieldcollection\Definition) {
                        $tablesToMigrate["object_collection_" . $definition->getKey() . "_%s"] = false;

                        foreach ($definition->getFieldDefinitions() as $fieldDef) {
                            if ($fieldDef instanceof ClassDefinition\Data\Localizedfields) {
                                $tablesToMigrate["object_collection_" . $definition->getKey() . "_localized_%s"] = false;
                            }
                        }
                    }
                }
            } elseif ($fd instanceof ClassDefinition\Data\Localizedfields) {
                $tablesToMigrate["object_localized_data_%s"] = false;

                $validLanguages = Tool::getValidLanguages();

                foreach ($validLanguages as $lang) {
                    $tablesToMigrate["object_localized_query_%s_" . $lang] = false;
                }
            } elseif ($fd instanceof ClassDefinition\Data\Classificationstore) {
                $tablesToMigrate["object_classificationstore_data_%s"] = false;
                $tablesToMigrate["object_classificationstore_groups_%s"] = false;
            }
        }

        foreach ($tablesToMigrate as $tbl => $replaceClassNames) {
            $oldSqlTable = sprintf($tbl, $oldClassId);
            $newSqlTable = sprintf($tbl, $newClassId);

            if(!$this->tableExists($oldSqlTable)) {
                continue;
            }

            $columns = $this->getColumns($newSqlTable);

            foreach($columns as &$column) {
                $column = $db->quoteIdentifier($column);
            }

            $sql = "INSERT INTO $newSqlTable SELECT ".implode(",", $columns)." FROM $oldSqlTable";

            $db->query($sql);

            if ($replaceClassNames) {
                $sql = "UPDATE $newSqlTable SET oo_classId=?, oo_className=?";

                $db->query($sql, [$newClassDefinition->getId(), $newClassDefinition->getName()]);
            }
        }

        $db->query("UPDATE objects SET o_classId=?, o_className=? WHERE o_classId=?", [$newClassDefinition->getId(), $newClassDefinition->getName(), $oldClassDefinition->getId()]);
    }

    /**
     * @param string $table
     * @return array
     */
    protected function getColumns($table)
    {
        $db = Db::get();

        $data = $db->fetchAll("SHOW COLUMNS FROM " . $table);
        $columns = [];

        foreach ($data as $d) {
            $columns[] = $d["Field"];
        }

        return $columns;
    }

    /**
     * Check if table exists
     *
     * @param $table
     * @return bool
     */
    protected function tableExists($table) {
        $db = Db::get();

        $result = $db->fetchAll("SHOW TABLES LIKE '$table'");

        return count($result) > 0;
    }
}
