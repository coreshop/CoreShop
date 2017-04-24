<?php

namespace Pimcore\Bundle\AdminBundle\Pimcore;

use Pimcore\File;
use Pimcore\Model\Document;
use Pimcore\Model\Object;
use Pimcore\Model\Object\Folder;
use Pimcore\Model\Translation\Admin;
use Pimcore\Model\User;
use Pimcore\Model\Staticroute;
use Pimcore\Model\Tool\Setup;
use Pimcore\Tool;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;

class InstallHelper
{
    /**
     * Runs CoreShop Install Command
     */
    public static function runCoreShopInstallCommand()
    {
        static::runCommand('coreshop:install', ['--no-interaction' => true]);
    }

    /**
     * @param string $command
     * @param array $params
     */
    protected static function runCommand($command, $params = []) {
        $kernel = \Pimcore::getKernel();
        $application = new Application($kernel);
        $application->setAutoExit(false);
        $options = ['command' => $command];
        $options = array_merge($options, $params);
        $application->run(new ArrayInput($options));
    }

    /**
     * @param $jsonFile
     * @param $className
     * @param bool $updateClass
     * @return Object\ClassDefinition
     */
    public static function createClass($jsonFile, $className, $updateClass = false)
    {
        $class = Object\ClassDefinition::getByName($className);

        if (!$class || $updateClass) {
            $json = file_get_contents($jsonFile);

            if (!$class) {
                $class = Object\ClassDefinition::create();
            }

            $class->setName($className);
            $class->setUserOwner(0);

            Object\ClassDefinition\Service::importClassDefinitionFromJson($class, $json, true);

            /**
             * Fixes Object Brick Stuff
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
     * Creates a new ObjectBrick.
     *
     * @param $name
     * @param null $jsonPath
     *
     * @return mixed|Object\Objectbrick\Definition
     */
    public static function createObjectBrick($name, $jsonPath)
    {
        try {
            $objectBrick = Object\Objectbrick\Definition::getByKey($name);
        } catch (\Exception $e) {
            $objectBrick = new Object\Objectbrick\Definition();
            $objectBrick->setKey($name);
        }

        $json = file_get_contents($jsonPath);

        Object\ClassDefinition\Service::importObjectBrickFromJson($objectBrick, $json, true);

        return $objectBrick;
    }

    /**
     * @param $name
     * @param null $jsonPath
     * @return mixed|null|Object\Fieldcollection\Definition
     */
    public static function createFieldCollection($name, $jsonPath )
    {
        try {
            $fieldCollection = Object\Fieldcollection\Definition::getByKey($name);
        } catch (\Exception $e) {
            $fieldCollection = new Object\Fieldcollection\Definition();
            $fieldCollection->setKey($name);
        }

        $json = file_get_contents($jsonPath);

        Object\ClassDefinition\Service::importFieldCollectionFromJson($fieldCollection, $json, true);

        return $fieldCollection;
    }

    /**
     * Install Admin TranslationsFile.
     *
     * @param string $csv string Path to CSV File
     *
     * @return bool
     */
    public function installAdminTranslations($csv)
    {
        Admin::importTranslationsFromFile($csv, true);

        return true;
    }

    /**
     * installs some data based from an XML File.
     *
     * @param string $xml
     * @param $namespace
     */
    public function installObjectData($xml, $namespace = '')
    {
        $file = PIMCORE_PLUGINS_PATH."/CoreShop/install/data/objects/$xml.xml";

        if (file_exists($file)) {
            $config = new \Zend_Config_Xml($file);
            $config = $config->toArray();
            $coreShopNamespace = '\\CoreShop\\Model\\'.$namespace;

            foreach ($config['objects'] as $class => $amounts) {
                $class = $coreShopNamespace.$class;

                foreach ($amounts as $values) {
                    if (Tool::classExists($class) && is_a($class, AbstractModel::class)) {
                        $object = $class::create();

                        foreach ($values as $key => $value) {
                            //Localized Value
                            $setter = 'set' . ucfirst($key);

                            if (is_array($value)) {
                                foreach ($value as $lang => $val) {
                                    $object->$setter($val, $lang);
                                }
                            } else {
                                $object->$setter($value);
                            }
                        }

                        $object->save();
                    }
                }
            }
        }
    }

    /**
     * Creates some Documents with Data based from XML file.
     *
     * @param string $xml
     *
     * @throws \Exception
     */
    public static function installDocuments($file)
    {
        if (file_exists($file)) {
            $config = new \Zend_Config_Xml($file);
            $config = $config->toArray();

            if (array_key_exists('documents', $config)) {
                $validLanguages = explode(',', \Pimcore\Config::getSystemConfig()->general->validLanguages);
                $languagesDone = [];

                foreach ($validLanguages as $language) {
                    $locale = new \Zend_Locale($language);
                    $language = $locale->getLanguage();
                    $languageDocument = Document::getByPath('/'.$language);

                    if (!$languageDocument instanceof Document) {
                        $languageDocument = new Document\Page();
                        $languageDocument->setParent(Document::getById(1));
                        $languageDocument->setKey($language);
                        $languageDocument->save();
                    }

                    foreach ($config['documents'] as $value) {
                        foreach ($value as $doc) {
                            $document = Document::getByPath('/'.$language.'/'.$doc['path'].'/'.$doc['key']);

                            if (!$document) {
                                $class = 'Pimcore\\Model\\Document\\'.ucfirst($doc['type']);

                                if (Tool::classExists($class)) {
                                    $document = new $class();
                                    $document->setParent(Document::getByPath('/'.$language.'/'.$doc['path']));
                                    $document->setKey($doc['key']);
                                    $document->setProperty('language', $language, 'text', true);

                                    if ($document instanceof Document\PageSnippet) {
                                        if (array_key_exists('action', $doc)) {
                                            $document->setAction($doc['action']);
                                        }

                                        if (array_key_exists('controller', $doc)) {
                                            $document->setController($doc['controller']);
                                        }

                                        if (array_key_exists('module', $doc)) {
                                            $document->setModule($doc['module']);
                                        }
                                    }

                                    $document->setProperty('language', 'text', $language);
                                    $document->save();

                                    if (array_key_exists('content', $doc)) {
                                        foreach ($doc['content'] as $fieldLanguage => $fields) {
                                            if ($fieldLanguage !== $language) {
                                                continue;
                                            }

                                            foreach ($fields['field'] as $field) {
                                                $key = $field['key'];
                                                $type = $field['type'];
                                                $content = null;

                                                if (array_key_exists('file', $field)) {
                                                    $file = $dataPath.'/'.$field['file'];

                                                    if (file_exists($file)) {
                                                        $content = file_get_contents($file);
                                                    }
                                                }

                                                if (array_key_exists('value', $field)) {
                                                    $content = $field['value'];
                                                }

                                                if ($content) {
                                                    if ($type === 'objectProperty') {
                                                        $document->setValue($key, $content);
                                                    } else {
                                                        $document->setRawElement($key, $type, $content);
                                                    }
                                                }
                                            }
                                        }

                                        $document->save();
                                    }
                                }
                            }

                            //Link translations
                            foreach ($languagesDone as $doneLanguage) {
                                $translatedDocument = Document::getByPath('/'.$doneLanguage.'/'.$doc['path'].'/'.$doc['key']);

                                if ($translatedDocument) {
                                    $service = new \Pimcore\Model\Document\Service();

                                    $service->addTranslation($document, $translatedDocument, $doneLanguage);
                                }
                            }
                        }
                    }

                    $languagesDone[] = $language;
                }
            }
        }
    }

}