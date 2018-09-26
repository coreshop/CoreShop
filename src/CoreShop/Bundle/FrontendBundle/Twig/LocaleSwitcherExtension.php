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

namespace CoreShop\Bundle\FrontendBundle\Twig;

use CoreShop\Component\Core\Context\ShopperContextInterface;
use Pimcore\Model\Document;
use Pimcore\Model\Site;
use Pimcore\Tool;

final class LocaleSwitcherExtension extends \Twig_Extension
{
    /**
     * @var Document\Service
     */
    private $documentService;

    /**
     * @var ShopperContextInterface
     */
    private $shopperContext;

    /**
     * LanguageSwitcherExtension constructor.
     * @param Document\Service        $documentService
     * @param ShopperContextInterface $shopperContext
     */
    public function __construct(
        Document\Service $documentService,
        ShopperContextInterface $shopperContext
    ) {
        $this->documentService = $documentService;
        $this->shopperContext = $shopperContext;
    }


    /**
     * @inheritDoc
     */
    public function getFunctions()
    {
        return [
            new \Twig_Function('coreshop_locale_switcher', [$this, 'getLocalizedLinks']),
        ];
    }

    /**
     * @param Document $document
     * @return array
     */
    public function getLocalizedLinks(Document $document)
    {
        $translations = $this->documentService->getTranslations($document);
        $links = [];
        $basePath = '/';

        $store = $this->shopperContext->getStore();

        if ($store->getSiteId()) {
            try {
                $site = Site::getById($store->getSiteId());
                $basePath = $site->getRootDocument()->getRealFullPath().'/';
            } catch (\Exception $ex) {
                $basePath = '/';
            }
        }

        foreach (Tool::getValidLanguages() as $language) {
            $target = $basePath.$language;
            $localizedDocument = null;

            if (isset($translations[$language])) {
                $localizedDocument = Document::getById($translations[$language]);
            } else {
                $localizedDocument = Document::getByPath($target);
            }

            if ($localizedDocument instanceof Document && $localizedDocument->getPublished()) {
                $links[] = [
                    'language' => $language,
                    'target' => $localizedDocument->getFullPath(),
                    'displayLanguage' => \Locale::getDisplayLanguage($language, $language),
                ];
            }
        }

        return $links;
    }
}