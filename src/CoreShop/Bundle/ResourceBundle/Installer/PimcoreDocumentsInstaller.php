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

namespace CoreShop\Bundle\ResourceBundle\Installer;

use CoreShop\Bundle\ResourceBundle\Installer\Configuration\DocumentConfiguration;
use Pimcore\Model\Document;
use Pimcore\Model\Element\Service;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Yaml\Yaml;

final class PimcoreDocumentsInstaller implements ResourceInstallerInterface
{
    /**
     * @var KernelInterface
     */
    private $kernel;

    /**<
     * @param KernelInterface $kernel
     */
    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    /**
     * {@inheritdoc}
     */
    public function installResources(OutputInterface $output, $applicationName = null, $options = [])
    {
        $parameter = $applicationName ? sprintf('%s.pimcore.admin.install.documents', $applicationName) : 'coreshop.all.pimcore.admin.install.documents';

        if ($this->kernel->getContainer()->hasParameter($parameter)) {
            $documentFilesToInstall = $this->kernel->getContainer()->getParameter($parameter);
            $docsToInstall = [];

            $progress = new ProgressBar($output);
            $progress->setBarCharacter('<info>░</info>');
            $progress->setEmptyBarCharacter(' ');
            $progress->setProgressCharacter('<comment>░</comment>');
            $progress->setFormat(' %current%/%max% [%bar%] %percent:3s%% %message%');

            $processor = new Processor();
            $configurationDefinition = new DocumentConfiguration();

            foreach ($documentFilesToInstall as $file) {
                $file = $this->kernel->locateResource($file);

                if (file_exists($file)) {
                    $documents = Yaml::parse(file_get_contents($file));
                    $documents = $processor->processConfiguration($configurationDefinition, ['documents' => $documents]);
                    $documents = $documents['documents'];

                    foreach ($documents as $docData) {
                        $docsToInstall[] = $docData;
                    }
                }
            }

            $progress->start(count($docsToInstall));
            $validLanguages = explode(',', \Pimcore\Config::getSystemConfig()->general->validLanguages);
            $languagesDone = [];

            foreach ($docsToInstall as $docData) {
                $progress->setMessage(sprintf('<error>Install Document %s/%s</error>', $docData['path'], $docData['key']));

                foreach ($validLanguages as $language) {
                    $languageDocument = Document::getByPath('/'.$language);

                    if (!$languageDocument instanceof Document) {
                        $languageDocument = new Document\Page();
                        $languageDocument->setParent(Document::getById(1));
                        $languageDocument->setProperty('language', 'text', $language);
                        $languageDocument->setKey(Service::getValidKey($language, 'document'));
                        $languageDocument->save();
                    }

                    $doc = $this->installDocument($language, $docData);

                    if ($doc instanceof Document) {
                        //Link translations
                        foreach ($languagesDone as $doneLanguage) {
                            $translatedDocument = Document::getByPath('/'.$doneLanguage.'/'.$docData['path'].'/'.$docData['key']);

                            if ($translatedDocument) {
                                $service = new Document\Service();
                                $service->addTranslation($doc, $translatedDocument, $doneLanguage);
                            }
                        }
                    }
                }

                $progress->advance();
            }

            $progress->finish();
        }
    }

    /**
     * @param $language
     * @param $properties
     *
     * @return Document
     */
    private function installDocument($language, $properties)
    {
        $path = '/'.$language.'/'.$properties['path'].'/'.$properties['key'];

        if (!Document\Service::pathExists($path)) {
            $class = 'Pimcore\\Model\\Document\\'.ucfirst($properties['type']);

            if (\Pimcore\Tool::classExists($class)) {
                /** @var Document $document */
                $document = new $class();
                $document->setParent(Document::getByPath('/'.$language.'/'.$properties['path']));

                $document->setKey(Service::getValidKey($properties['key'], 'document'));
                $document->setProperty('language', $language, 'text', true);

                if (isset($properties['name'])) {
                    $document->setName($properties['name']);
                }
                if (isset($properties['title'])) {
                    $document->setTitle($properties['title']);
                }
                if (isset($properties['module'])) {
                    $document->setModule($properties['module']);
                }
                if (isset($properties['controller'])) {
                    $document->setController($properties['controller']);
                }
                if (isset($properties['action'])) {
                    $document->setAction($properties['action']);
                }
                if (isset($properties['template'])) {
                    $document->setTemplate($properties['template']);
                }

                if (array_key_exists('content', $properties)) {
                    foreach ($properties['content'] as $fieldLanguage => $fields) {
                        if ($fieldLanguage !== $language) {
                            continue;
                        }

                        foreach ($fields as $key => $field) {
                            $type = $field['type'];
                            $content = null;

                            if (array_key_exists('value', $field)) {
                                $content = $field['value'];
                            }

                            if (!empty($content)) {
                                if ('objectProperty' === $type) {
                                    $document->setValue($key, $content);
                                } else {
                                    $document->setRawElement($key, $type, $content);
                                }
                            }
                        }
                    }
                }

                $document->setPublished(true);
                $document->save();

                return $document;
            }
        }
    }
}
