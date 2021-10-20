<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Bundle\FrontendBundle\Twig;

use CoreShop\Component\Core\Context\ShopperContextInterface;
use Pimcore\Model\Document;
use Pimcore\Model\Site;
use Pimcore\Tool;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class LocaleSwitcherExtension extends AbstractExtension
{
    public function __construct(private Document\Service $documentService, private ShopperContextInterface $shopperContext)
    {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('coreshop_locale_switcher', [$this, 'getLocalizedLinks']),
        ];
    }

    public function getLocalizedLinks(Document $document): array
    {
        $translations = $this->documentService->getTranslations($document);
        $links = [];
        $basePath = '/';

        $store = $this->shopperContext->getStore();

        if ($store->getSiteId()) {
            try {
                $site = Site::getById($store->getSiteId());

                if ($site instanceof Site) {
                    $basePath = $site->getRootDocument()->getRealFullPath() . '/';
                }
            } catch (\Exception) {
                $basePath = '/';
            }
        }

        foreach (Tool::getValidLanguages() as $language) {
            $target = $basePath . $language;

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
