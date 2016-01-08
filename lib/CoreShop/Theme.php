<?php
/**
 * CoreShop
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

namespace CoreShop;

use CoreShop\Exception\UnsupportedException;
use Pimcore\Model\Document;
use Pimcore\Model\Object;

abstract class Theme {

    /**
     * @returns string
     * @throws UnsupportedException
     */
    public function getName() {
        throw new UnsupportedException("Please implement me");
    }

    public function getTemplatePath() {
        return $templatePath = CORESHOP_TEMPLATE_BASE_PATH . "/" . $this->getName();
    }

    public function getConfig() {
        $templatePath = $this->getTemplatePath();

        //Get Template Xml
        if(!file_exists("$templatePath/template.xml")) {
            throw new \Exception("Template " . $this->getName() . " not found");
        }

        $config = new \Zend_Config_Xml("$templatePath/template.xml");
        return $config->toArray();
    }

    /**
     * Installs a Theme
     *
     * @throws \Exception
     */
    public function installTheme()
    {
        $config = $this->getConfig();

        if(array_key_exists("installation", $config))
        {
            //Install CoreShop Documents
            if(array_key_exists("documents", $config["installation"]))
            {
                $validLanguages = explode(",", \Pimcore\Config::getSystemConfig()->general->validLanguages);

                foreach($validLanguages as $language)
                {
                    $languageDocument = Document::getByPath("/" . $language);

                    if(!$languageDocument instanceof Document) {
                        $languageDocument = new Document\Page();
                        $languageDocument->setParent(Document::getById(1));
                        $languageDocument->setKey($language);
                        $languageDocument->save();
                    }

                    foreach($config["installation"]["documents"] as $value)
                    {
                        foreach($value as $doc)
                        {
                            $document = Document::getByPath("/" . $language . "/" . $doc['path'] . "/" . $doc['key']);

                            if(!$document)
                            {
                                $class = "Pimcore\\Model\\Document\\" . ucfirst($doc['type']);

                                if(\Pimcore\Tool::classExists($class))
                                {
                                    $document = new $class();
                                    $document->setParent(Document::getByPath("/" . $language . "/" . $doc['path']));
                                    $document->setKey($doc['key']);

                                    if($document instanceof Document\PageSnippet) {
                                        if(array_key_exists("action", $doc))
                                            $document->setAction($doc['action']);

                                        if(array_key_exists("controller", $doc))
                                            $document->setController($doc['controller']);

                                        if(array_key_exists("module", $doc))
                                            $document->setModule($doc['module']);
                                    }

                                    $document->save();

                                    if(array_key_exists("content", $doc)) {
                                        foreach($doc['content'] as $fieldLanguage=>$fields) {
                                            if($fieldLanguage !== $language)
                                                continue;

                                            foreach($fields['field'] as $field) {
                                                $key = $field['key'];
                                                $type = $field['type'];
                                                $content = null;

                                                if(array_key_exists("file", $field)) {
                                                    $file = $this->getTemplatePath() . "/" . $field['file'];

                                                    if(file_exists($file))
                                                        $content = file_get_contents($file);
                                                }

                                                if(array_key_exists("value", $field)) {
                                                    $content = $field['value'];
                                                }

                                                if($content) {
                                                    $document->setRawElement($key, $type, $content);
                                                }
                                            }
                                        }

                                        $document->save();
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * Installs Theme Demo Data if available
     *
     * @return bool
     */
    public function installDemoData() {
        return true;
    }
}