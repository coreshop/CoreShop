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

use CoreShop\Component\Pimcore\DataObject\InheritanceHelper;
use Pimcore\Model\DataObject\AbstractObject;
use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\DataObject\Data\UrlSlug;
use Pimcore\Model\Site;
use Pimcore\Tool;

class DataObjectSlugGenerator implements DataObjectSlugGeneratorInterface
{
    public function __construct(
        private DataObjectSiteSlugGeneratorInterface $generator,
    ) {
    }

    public function generateSlugs(SluggableInterface $sluggable): void
    {
        $sites = new Site\Listing();
        $sites = $sites->getSites();

        foreach (Tool::getValidLanguages() as $language) {
            $fallbackSlug = $this->generator->generateSlugsForSite($sluggable, $language);

            $newSlugs = [
                new UrlSlug($fallbackSlug, 0),
            ];
            $actualSlugs = [];
            $existingSlugs = InheritanceHelper::useInheritedValues(
                fn () => $sluggable->getSlug($language),
                false,
            );

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
                            // $existingSlug is the slug to be saved from backend
                            $dbSlug = null;
                            if($sluggable instanceof Concrete) {
                                /** @psalm-suppress InternalMethod */
                                $dbSlug = $sluggable->retrieveSlugData(['fieldname' => 'slug', 'ownertype' => 'object', 'position' => $language, 'siteId' => $existingSlug->getSiteId()])[0]['slug'] ?? null;
                                if ($dbSlug === null) {
                                    /** @psalm-suppress InternalMethod */
                                    $dbSlug = $sluggable->retrieveSlugData(['fieldname' => 'slug', 'ownertype' => 'object', 'position' => $language])[0]['slug'] ?? null; // fallback slug
                                }
                            }

                            if ($dbSlug && $dbSlug !== $existingSlug->getSlug()) {
                                $existingSlug->setPreviousSlug($dbSlug);
                            } elseif (!$dbSlug && !$existingSlug->getSlug()) {
                                $actualSlugs[] = $newSlug;
                            } else {
                                $actualSlugs[] = $existingSlug;
                            }
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
