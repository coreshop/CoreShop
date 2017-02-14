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

namespace CoreShop\Plugin;

use CoreShop\Model\AbstractModel;
use CoreShop\Model\Configuration;
use CoreShop\Version;
use Pimcore\File;
use Pimcore\Model\Document;
use Pimcore\Model\Object;
use Pimcore\Model\Object\Folder;
use Pimcore\Model\Translation\Admin;
use Pimcore\Model\User;
use Pimcore\Model\Staticroute;
use Pimcore\Model\Tool\Setup;

use Pimcore\Tool;

/**
 * Class Install
 * @package CoreShop\Plugin
 */
class Install
{
    /**
     * Admin User.
     *
     * @var User
     */
    protected $_user;

    /**
     * Install CoreShop
     *
     * @return bool
     */
    public function fullInstall()
    {
        \Pimcore::getEventManager()->trigger('coreshop.install.pre', null, ['installer' => $this]);

        //install Data
        $this->installObjectData('threadStates', 'Messaging\\Thread\\');
        $this->installObjectData('threadContacts', 'Messaging\\');
        $this->installDocuments('documents');
        $this->installMessagingMails();
        $this->installMessagingContacts();
        $this->installMailRules();

        $this->createFieldCollection('CoreShopOrderTax');
        $this->createFieldCollection('CoreShopPriceRuleItem');

        // create object classes
        $manufacturer = $this->createClass('CoreShopManufacturer');
        $categoryClass = $this->createClass('CoreShopCategory');
        $productClass = $this->createClass('CoreShopProduct');
        $cartClass = $this->createClass('CoreShopCart');
        $cartItemClass = $this->createClass('CoreShopCartItem');
        $userClass = $this->createClass('CoreShopUser');
        $customerGroupClass = $this->createClass('CoreShopCustomerGroup');
        $userAddressClass = $this->createClass('CoreShopUserAddress');

        $orderItemClass = $this->createClass('CoreShopOrderItem');
        $paymentClass = $this->createClass('CoreShopPayment');
        $orderClass = $this->createClass('CoreShopOrder');

        $invoiceItemClass = $this->createClass('CoreShopOrderInvoiceItem');
        $invoiceClass = $this->createClass('CoreShopOrderInvoice');

        $shipmentItemClass = $this->createClass('CoreShopOrderShipmentItem');
        $shipmentClass = $this->createClass('CoreShopOrderShipment');

        // create root object folder with subfolders
        $coreShopFolder = $this->createFolders();
        // create custom view for blog objects
        $this->createCustomView($coreShopFolder, [
            $productClass->getId(),
            $categoryClass->getId(),
            $cartClass->getId(),
            $cartItemClass->getId(),
            $userClass->getId(),
            $userAddressClass->getId(),
            $customerGroupClass->getId(),
            $orderItemClass->getId(),
            $orderClass->getId(),
            $paymentClass->getId(),
            $customerGroupClass->getId(),
            $invoiceClass->getId(),
            $invoiceItemClass->getId(),
            $shipmentClass->getId(),
            $shipmentItemClass->getId(),
            $manufacturer->getId()
        ]);
        // create static routes
        $this->createStaticRoutes();

        //install workflow configuration
        $this->installWorkflow();

        $this->installAdminTranslations(PIMCORE_PLUGINS_PATH.'/CoreShop/install/translations/admin.csv');

        $this->createImageThumbnails();

        \Pimcore::getEventManager()->trigger('coreshop.install.post', null, ['installer' => $this]);

        $this->setConfigInstalled();

        return true;
    }

    /**
     * executes some install SQL.
     *
     * @param string $fileName
     */
    public function executeSQL($fileName)
    {
        $file = PIMCORE_PLUGINS_PATH."/CoreShop/install/sql/$fileName.sql";

        $setup = new Setup();
        $setup->insertDump($file);
    }

    /**
     * creates a mew Class if it doesn't exists.
     *
     * @param $className
     * @param bool $updateClass should class be updated if it already exists
     *
     * @return mixed|Object\ClassDefinition
     */
    public function createClass($className, $updateClass = false)
    {
        $class = Object\ClassDefinition::getByName($className);

        if (!$class || $updateClass) {
            $jsonFile = PIMCORE_PLUGINS_PATH."/CoreShop/install/class-$className.json";
            $json = file_get_contents($jsonFile);

            $result = \Pimcore::getEventManager()->trigger("coreshop.install.class.getClass.$className", $this, ['className' => $className, 'json' => $json], function($v) {
                return $v instanceof Object\ClassDefinition;
            });

            if ($result->stopped()) {
                return $result->last();
            }

            if (!$class) {
                $class = Object\ClassDefinition::create();
            }

            $class->setName($className);
            $class->setUserOwner($this->_getUserId());

            $result = \Pimcore::getEventManager()->trigger('coreshop.install.class.preCreate', $this, ['className' => $className, 'json' => $json], function($v) {
                return !preg_match('/[^,:{}\\[\\]0-9.\\-+Eaeflnr-u \\n\\r\\t]/', preg_replace('/"(\\.|[^"\\\\])*"/', '', $v));
            });

            if ($result->stopped()) {
                $resultJson = $result->last();

                if ($resultJson) {
                    $json = $resultJson;
                }
            }

            Object\ClassDefinition\Service::importClassDefinitionFromJson($class, $json, true);

            //resave related object bricks
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
     * Removes a class definition.
     *
     * @param string $name
     */
    public function removeClass($name)
    {
        $class = Object\ClassDefinition::getByName($name);
        if ($class) {
            $class->delete();
        }
    }

    /**
     * Creates a new ObjectBrick.
     *
     * @param $name
     * @param null $jsonPath
     *
     * @return mixed|Object\Objectbrick\Definition
     */
    public function createObjectBrick($name, $jsonPath = null)
    {
        try {
            $objectBrick = Object\Objectbrick\Definition::getByKey($name);
        } catch (\Exception $e) {
            $objectBrick = new Object\Objectbrick\Definition();
            $objectBrick->setKey($name);
        }

        if ($jsonPath == null) {
            $jsonPath = PIMCORE_PLUGINS_PATH."/CoreShop/install/fieldcollection-$name.json";
        }

        $json = file_get_contents($jsonPath);

        $result = \Pimcore::getEventManager()->trigger('coreshop.install.objectbrick.preCreate', $this, ['objectbrickName' => $name, 'json' => $json], function($v) {
            return !preg_match('/[^,:{}\\[\\]0-9.\\-+Eaeflnr-u \\n\\r\\t]/', preg_replace('/"(\\.|[^"\\\\])*"/', '', $v));
        });

        if ($result->stopped()) {
            $resultJson = $result->last();

            if ($resultJson) {
                $json = $resultJson;
            }
        }

        Object\ClassDefinition\Service::importObjectBrickFromJson($objectBrick, $json, true);

        return $objectBrick;
    }

    /**
     * Removes an ObjectBrick.
     *
     * @param $name
     *
     * @return bool
     */
    public function removeObjectBrick($name)
    {
        try {
            $brick = Object\Objectbrick\Definition::getByKey($name);

            if ($brick) {
                $brick->delete();
            }
        } catch (\Exception $e) {
            return false;
        }

        return true;
    }

    /**
     * Creates a FieldCollection.
     *
     * @param string $name
     * @param null $jsonPath
     *
     * @return mixed|null|Object\Fieldcollection\Definition
     */
    public function createFieldCollection($name, $jsonPath = null)
    {
        try {
            $fieldCollection = Object\Fieldcollection\Definition::getByKey($name);
        } catch (\Exception $e) {
            $fieldCollection = new Object\Fieldcollection\Definition();
            $fieldCollection->setKey($name);
        }

        if ($jsonPath == null) {
            $jsonPath = PIMCORE_PLUGINS_PATH."/CoreShop/install/fieldcollection-$name.json";
        }

        $json = file_get_contents($jsonPath);

        $result = \Pimcore::getEventManager()->trigger('coreshop.install.fieldcollection.preCreate', $this, ['fieldcollectionName' => $name, 'json' => $json], function($v) {
            return !preg_match('/[^,:{}\\[\\]0-9.\\-+Eaeflnr-u \\n\\r\\t]/', preg_replace('/"(\\.|[^"\\\\])*"/', '', $v));
        });

        if ($result->stopped()) {
            $resultJson = $result->last();

            if ($resultJson) {
                $json = $resultJson;
            }
        }

        Object\ClassDefinition\Service::importFieldCollectionFromJson($fieldCollection, $json, true);

        return $fieldCollection;
    }

    /**
     * Removes a FieldCollection.
     *
     * @param string $name
     *
     * @return bool
     */
    public function removeFieldcollection($name)
    {
        try {
            $fc = Object\Fieldcollection\Definition::getByKey($name);

            if ($fc) {
                $fc->delete();
            }
        } catch (\Exception $e) {
            return false;
        }

        return true;
    }

    /**
     * Create needed CoreShop Folders.
     *
     * @return Object\AbstractObject|Folder
     */
    public function createFolders()
    {
        $root = Folder::getByPath('/coreshop');
        $products = Folder::getByPath('/coreshop/products');
        $categories = Folder::getByPath('/coreshop/categories');
        $cart = Folder::getByPath('/coreshop/carts');

        if (!$root instanceof Folder) {
            $root = Folder::create([
                'o_parentId' => 1,
                'o_creationDate' => time(),
                'o_userOwner' => $this->_getUserId(),
                'o_userModification' => $this->_getUserId(),
                'o_key' => 'coreshop',
                'o_published' => true,
            ]);
        }

        if (!$products instanceof Folder) {
            Folder::create([
                'o_parentId' => $root->getId(),
                'o_creationDate' => time(),
                'o_userOwner' => $this->_getUserId(),
                'o_userModification' => $this->_getUserId(),
                'o_key' => 'products',
                'o_published' => true,
            ]);
        }

        if (!$categories instanceof Folder) {
            Folder::create([
                'o_parentId' => $root->getId(),
                'o_creationDate' => time(),
                'o_userOwner' => $this->_getUserId(),
                'o_userModification' => $this->_getUserId(),
                'o_key' => 'categories',
                'o_published' => true,
            ]);
        }

        if (!$cart instanceof Folder) {
            Folder::create([
                'o_parentId' => $root->getId(),
                'o_creationDate' => time(),
                'o_userOwner' => $this->_getUserId(),
                'o_userModification' => $this->_getUserId(),
                'o_key' => 'carts',
                'o_published' => true,
            ]);
        }

        return $root;
    }

    /**
     * Remove CoreShop Folders.
     */
    public function removeFolders()
    {
        $blogFolder = Folder::getByPath('/coreshop');
        if ($blogFolder) {
            $blogFolder->delete();
        }
    }

    /**
     * Creates CustomView for CoreShop if it doesn't exist.
     *
     * @param $rootFolder
     * @param array $classIds
     *
     * @return bool
     *
     * @throws \Zend_Config_Exception
     */
    public function createCustomView($rootFolder, array $classIds)
    {
        $storedViews = Tool::getCustomViewConfig();

        if (empty($storedViews)) {
            $customViewId = 1;
            $storedViews = [];
        } else {
            $last = end($storedViews);
            $customViewId = $last['id'] + 1;
        }

        $alreadyDefined = false;

        // does custom view already exists?
        if (!empty($storedViews)) {
            foreach ($storedViews as $view) {
                if ($view['name'] == 'CoreShop') {
                    $alreadyDefined = true;
                    break;
                }
            }
        }

        if ($alreadyDefined === true) {
            return false;
        }

        $view = [
            'name' => 'CoreShop',
            'condition' => '',
            'icon' => '/pimcore/static/img/icon/cart.png',
            'id' => $customViewId,
            'rootfolder' => $rootFolder->getFullPath(),
            'showroot' => false,
            'classes' => implode(',', $classIds),
        ];

        $storedViews[] = $view;

        $customViews = ['views' => $storedViews];

        $configFile = \Pimcore\Config::locateConfigFile('customviews.php');
        File::put($configFile, to_php_data_file_format($customViews));

        return true;
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
        Admin::importTranslationsFromFile($csv, true, Tool\Admin::getLanguages());

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
    public function installDocuments($xml)
    {
        $dataPath = PIMCORE_PLUGINS_PATH.'/CoreShop/install/data/documents';
        $file = $dataPath."/$xml.xml";

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

    /**
     * Installs the Messaging Mails.
     */
    public function installMessagingMails()
    {
        $this->installDocuments('messaging');

        $validLanguages = explode(',', \Pimcore\Config::getSystemConfig()->general->validLanguages);

        foreach ($validLanguages as $language) {
            $locale = new \Zend_Locale($language);
            $language = $locale->getLanguage();

            $custEmail = Document::getByPath('/'.$language.'/shop/email/message-customer');
            $custReplyEmail = Document::getByPath('/'.$language.'/shop/email/message-customer-reply');
            $contEmail = Document::getByPath('/'.$language.'/shop/email/message-contact');

            if ($custEmail instanceof Document) {
                Configuration::set('SYSTEM.MESSAGING.MAIL.CUSTOMER.'.strtoupper($language), $custEmail->getId());
            }

            if ($custReplyEmail instanceof Document) {
                Configuration::set('SYSTEM.MESSAGING.MAIL.CUSTOMER.RE.'.strtoupper($language), $custReplyEmail->getId());
            }

            if ($contEmail instanceof Document) {
                Configuration::set('SYSTEM.MESSAGING.MAIL.CONTACT.'.strtoupper($language), $contEmail->getId());
            }
        }
    }

    /**
     * Install Default Messaging Contacts
     */
    public function installMessagingContacts()
    {
        Configuration::set('SYSTEM.MESSAGING.CONTACT.SALES', 1);
        Configuration::set('SYSTEM.MESSAGING.CONTACT.TECHNOLOGY', 2);
    }

    /**
     * Install Workflow Data
     */
    public function installWorkflow()
    {
        //install workflow data!!
        if(\Pimcore\Version::getRevision() > 4030) {
            $object = \CoreShop\Model\Order\Workflow::getWorkflowObject();
            $object->save();

            return $object->getId();
        }
        else
        {
            /** @noinspection PhpDeprecationInspection */
            $workflowConfig = \CoreShop\Model\Order\Workflow::getWorkflowConfig();
            $systemWorkflowConfig = \Pimcore\WorkflowManagement\Workflow\Config::getWorkflowManagementConfig(true);

            $configFile = PIMCORE_CONFIGURATION_DIRECTORY . '/workflowmanagement.php';

            //no workflow file. create it!
            if ($systemWorkflowConfig === null) {
                //set defaults
                $workflowConfig['id'] = 1;

                $workflowCompleteData = [
                    'workflows' => [$workflowConfig]
                ];

                \Pimcore\File::putPhpFile($configFile, to_php_data_file_format($workflowCompleteData));
            } else {
                $hasCoreShopWorkflow = false;
                $lastId = 1;

                if (isset($systemWorkflowConfig['workflows']) && is_array($systemWorkflowConfig['workflows'])) {
                    foreach ($systemWorkflowConfig['workflows'] as $workflow) {
                        if ($workflow['name'] === 'OrderState') {
                            $hasCoreShopWorkflow = true;
                            break;
                        }
                        $lastId = (int)$workflow['id'];
                    }

                    if ($hasCoreShopWorkflow === false) {
                        //set defaults
                        $workflowConfig['id'] = $lastId + 1;
                        $systemWorkflowConfig['workflows'] = array_merge($systemWorkflowConfig['workflows'], [$workflowConfig]);
                        \Pimcore\File::putPhpFile($configFile, to_php_data_file_format($systemWorkflowConfig));
                    }
                }
            }

            return $workflowConfig['id'];
        }
    }

    /**
     * Install default Mail Rules
     *
     * @return boolean
     */
    public function installMailRules()
    {
        $file = PIMCORE_PLUGINS_PATH .'/CoreShop/install/data/rules/mailRules.xml';

        if (!file_exists($file)) {
            return false;
        }

        $config = new \Zend_Config_Xml($file);
        $config = $config->toArray();

        $objects = $config['objects'];

        if (!is_array($objects['MailRule'])) {
            return false;
        }

        foreach ($objects['MailRule'] as $class => $rule) {
            $existingRule = \CoreShop\Model\Mail\Rule::getByField('name', $rule['name']);

            if ($existingRule instanceof \CoreShop\Model\Mail\Rule) {
                continue;
            }

            $ruleObj = \CoreShop\Model\Mail\Rule::create();
            $ruleObj->setName($rule['name']);
            $ruleObj->setSort(1);
            $ruleObj->setMailType($rule['mailType']);

            $conditions = isset($rule['conditions']['condition']) ? $rule['conditions']['condition'] : [];
            $actions = isset($rule['actions']['action']) ? $rule['actions']['action'] : [];

            $_conditions = $conditions;
            $_actions = $actions;

            foreach ($conditions as $condition => $conditionInfo) {
                if (!is_numeric($condition)) {
                    $_conditions = [$conditions];
                    break;
                }
            }

            foreach ($actions as $action => $actionInfo) {
                if (!is_numeric($action)) {
                    $_actions = [$actions];
                    break;
                }
            }

            $data = array_merge($_conditions, $_actions);

            $objConditions = [];
            $objActions = [];

            foreach ($data as $objectInfo) {
                $class = $objectInfo['class'];
                $params = $objectInfo['params'];

                $class = '\\CoreShop\\Model\\' . $class;
                $obj = new $class();

                if (is_array($params)) {
                    foreach ($params as $method => $value) {
                        $setter = 'set' . ucfirst($method);
                        if (method_exists($obj, $setter)) {

                            //get linked mails
                            if ($method === 'mails') {
                                $_val = [];
                                foreach ($value as $lang => $path) {
                                    $document = Document::getByPath('/' . $path);
                                    if ($document instanceof \Pimcore\Model\Document) {
                                        $_val[ $lang ] = $document->getId();
                                    }
                                }

                                $value = $_val;
                                unset($_val);
                            }

                            if (is_string($value) && strpos($value, '|') !== false) {
                                $value = array_filter(explode('|', $value));
                            }

                            $obj->$setter($value);
                        }
                    }
                }

                if (is_subclass_of($obj, '\CoreShop\Model\Mail\Rule\Condition\AbstractCondition')) {
                    $objConditions[] = $obj;
                } elseif (is_subclass_of($obj, '\CoreShop\Model\Mail\Rule\Action\AbstractAction')) {
                    $objActions[] = $obj;
                }
            }

            $ruleObj->setConditions($objConditions);
            $ruleObj->setActions($objActions);
            $ruleObj->save();
        }
        
        return true;
    }

    /**
     * Removes CoreShop CustomView.
     *
     * @throws \Zend_Config_Exception
     */
    public function removeCustomView()
    {
        $customViews = Tool::getCustomViewConfig();
        if ($customViews) {
            foreach ($customViews as $key => $view) {
                if ($view['name'] == 'CoreShop') {
                    unset($customViews[$key]);
                    break;
                }
            }
            $writer = new \Zend_Config_Writer_Xml([
                'config' => new \Zend_Config(['views' => ['view' => $customViews]]),
                'filename' => PIMCORE_CONFIGURATION_DIRECTORY.'/customviews.xml',
            ]);
            $writer->write();
        }
    }

    /**
     * set isInstalled true in CoreShop Config.
     *
     * @throws \Zend_Config_Exception
     */
    public function setConfigInstalled()
    {
        Configuration::set('SYSTEM.ISINSTALLED', true);
    }

    /**
     * Creates CoreShop Static Routes.
     *
     * @param string $path
     */
    public function createStaticRoutes($path = null)
    {
        if (is_null($path)) {
            $path = PIMCORE_PLUGINS_PATH.'/CoreShop/install/staticroutes.xml';
        }

        $conf = new \Zend_Config_Xml($path);
        $conf = $conf->toArray();

        $routes = $conf['routes']['route'];

        if (count($routes) == count($routes, 1)) {
            $routes = [$routes];
        }

        foreach ($routes as $def) {
            if (!Staticroute::getByName($def['name'])) {
                $route = Staticroute::create();
                $route->setName($def['name']);
                $route->setPattern($def['pattern']);
                $route->setReverse($def['reverse']);
                $route->setModule($def['module']);
                $route->setController($def['controller']);
                $route->setAction($def['action']);
                $route->setVariables($def['variables']);
                $route->setPriority($def['priority']);
                $route->save();
            }
        }
    }

    /**
     * Remove CoreShop Static Routes.
     *
     * @param null $path
     */
    public function removeStaticRoutes($path = null)
    {
        if (is_null($path)) {
            $path = PIMCORE_PLUGINS_PATH.'/CoreShop/install/staticroutes.xml';
        }

        $conf = new \Zend_Config_Xml($path);
        $conf = $conf->toArray();

        $routes = $conf['routes']['route'];

        if (count($routes) == count($routes, 1)) {
            $routes = [$routes];
        }

        foreach ($routes as $def) {
            $route = Staticroute::getByName($def['name']);

            if ($route) {
                $route->delete();
            }
        }
    }

    /**
     * Create CoreShop Config.
     */
    public function createConfig()
    {
        Configuration::set('SYSTEM.BASE.CURRENCY', 1); //Euro
        Configuration::set('SYSTEM.BASE.COUNTRY', 2); //Austria
        Configuration::set('SYSTEM.BASE.CATALOGMODE', false);
        Configuration::set('SYSTEM.BASE.BUILD', Version::getBuildNumber());
        Configuration::set('SYSTEM.BASE.VERSION', (string) Version::getVersion());
        Configuration::set('SYSTEM.PRODUCT.DEFAULTIMAGE', null);
        Configuration::set('SYSTEM.CATEGORY.DEFAULTIMAGE', null);
        Configuration::set('SYSTEM.ORDER.PREFIX', 'O');
        Configuration::set('SYSTEM.ORDER.SUFFIX', '');
        Configuration::set('SYSTEM.INVOICE.CREATE', true);
        Configuration::set('SYSTEM.INVOICE.PREFIX', 'OI');
        Configuration::set('SYSTEM.INVOICE.SUFFIX', '');
        Configuration::set('SYSTEM.INVOICE.WKHTML', '-T 40mm -B 15mm -L 10mm -R 10mm --header-spacing 5 --footer-spacing 5');
        Configuration::set('SYSTEM.SHIPMENT.PREFIX', 'OS');
        Configuration::set('SYSTEM.SHIPMENT.SUFFIX', '');
        Configuration::set('SYSTEM.SHIPMENT.WKHTML', '-T 40mm -B 15mm -L 10mm -R 10mm --header-spacing 5 --footer-spacing 5');
        Configuration::set('SYSTEM.MAIL.ORDER.NOTIFICATION', '');
        Configuration::set('SYSTEM.MAIL.ORDER.NOTIFICATION', true);
        Configuration::set('SYSTEM.MESSAGING.THREAD.STATE.NEW', 1);
        Configuration::set('SYSTEM.ISINSTALLED', false);
        Configuration::set("SYSTEM.MAIL.CONFIRMATION", "/shop/email/order-confirmation");
        Configuration::set("SYSTEM.BASE.PRICES.GROSS", false);
        Configuration::set("SYSTEM.SHIPPING.CARRIER_SORT", "grade");
        Configuration::set("SYSTEM.BASE.TAX.ENABLED", true);
        Configuration::set("SYSTEM.LOG.USAGESTATISTICS", true);
        Configuration::set("SYSTEM.VISITORS.TRACK", false);
        Configuration::set("SYSTEM.CATEGORY.LIST.MODE", "list");
        Configuration::set("SYSTEM.CATEGORY.GRID.PER_PAGE", [5, 10, 15, 20, 25]);
        Configuration::set("SYSTEM.CATEGORY.GRID.PER_PAGE_DEFAULT", 10);
        Configuration::set("SYSTEM.CATEGORY.LIST.PER_PAGE", [12, 24, 36]);
        Configuration::set("SYSTEM.CATEGORY.LIST.PER_PAGE_DEFAULT", 12);
        Configuration::set("SYSTEM.CATEGORY.VARIANT_MODE", "hide");
    }

    /**
     * Remove CoreShop Config.
     */
    public function removeConfig()
    {
        $configFile = \Pimcore\Config::locateConfigFile('coreshop_configurations');

        if (is_file($configFile.'.php')) {
            rename($configFile.'.php', $configFile.'.BACKUP');
        }
    }

    /**
     * Creates CoreShop Image Thumbnails.
     */
    public function createImageThumbnails()
    {
        $images = file_get_contents(PIMCORE_PLUGINS_PATH.'/CoreShop/install/thumbnails/images.json');
        $images = \Zend_Json::decode($images);

        foreach ($images as $name => $values) {
            $thumbnail = \Pimcore\Model\Asset\Image\Thumbnail\Config::getByName($name);

            if (!$thumbnail) {
                $thumbnail = new \Pimcore\Model\Asset\Image\Thumbnail\Config();
            }

            $thumbnail->setName($name);
            $thumbnail->setValues($values);
            $thumbnail->save();
        }
    }

    /**
     * Removes CoreShop Image Thumbnails.
     */
    public function removeImageThumbnails()
    {
        if (\Pimcore\Version::getRevision() >= 3608) {
            $definitions = new \Pimcore\Model\Asset\Image\Thumbnail\Config\Listing();
            $definitions = $definitions->getThumbnails();

            foreach ($definitions as $definition) {
                if (strpos($definition->getName(), 'coreshop') === 0) {
                    $definition->delete();
                }
            }
        } else {
            foreach (glob(PIMCORE_WEBSITE_PATH.'/var/config/imagepipelines/coreshop_*.xml') as $filename) {
                unlink($filename);
            }
        }
    }

    /**
     * @return \Int User Id
     */
    protected function _getUserId()
    {
        $userId = 0;
        $user = Tool\Admin::getCurrentUser();
        if ($user) {
            $userId = $user->getId();
        }

        return $userId;
    }
}
