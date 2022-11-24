<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under two different licenses:
 *  - GNU General Public License version 3 (GPLv3)
 *  - CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Component\Pimcore\Slug;

use Pimcore\Model\DataObject\Data\UrlSlug;
use Pimcore\Model\Site;
use Pimcore\Tool;

class DataObjectSlugGenerator implements DataObjectSlugGeneratorInterface
{
    public function __construct(
        private DataObjectSiteSlugGeneratorInterface $generator
    ) {
    }

    public function generateSlugs(SluggableInterface $sluggable): void
    {
        $sites = new Site\Listing();
        $sites = $sites->getSites();

        foreach (Tool::getValidLanguages() as $language) {
            $fallbackSlug = $this->generator->generateSlugsForSite($sluggable, $language);

            $newSlugs = [
                new UrlSlug($fallbackSlug, 0)
            ];
            $actualSlugs = [];
            $existingSlugs = $sluggable->getSlug($language);

            foreach ($sites as $site) {
                $siteSlug = $this->generator->generateSlugsForSite($sluggable, $language, $site);

                if ($siteSlug === $fallbackSlug) {
                    continue;
                }

                $newSlugs[] = new UrlSlug($siteSlug, $site->getId());
            }

            foreach ($newSlugs as $newSlug) {
                $found = false;

                foreach ($existingSlugs as $existingSlug) {
                    if ($existingSlug->getSiteId() === $newSlug->getSiteId()) {
                        if ($existingSlug->getSlug() === $newSlug->getSlug()) {
                            $actualSlugs[] = $existingSlug;
                        } else {
                            /**
                             * @psalm-suppress InternalMethod
                             */
                            $newSlug->setPreviousSlug($existingSlug->getSlug());
                            $actualSlugs[] = $newSlug;
                        }
                        $found = true;

                        break;
                    }
                }

                if (!$found) {
                    $actualSlugs[] = $newSlug;
                }
            }

            $sluggable->setSlug($actualSlugs, $language);
        }
    }
}
