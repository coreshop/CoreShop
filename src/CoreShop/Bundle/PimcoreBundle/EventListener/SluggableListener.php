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

namespace CoreShop\Bundle\PimcoreBundle\EventListener;

use CoreShop\Component\Pimcore\Exception\SlugNotPossibleException;
use CoreShop\Component\Pimcore\Slug\SluggableInterface;
use CoreShop\Component\Pimcore\Slug\SluggableSluggerInterface;
use Pimcore\Event\DataObjectEvents;
use Pimcore\Event\Model\DataObjectEvent;
use Pimcore\Model\DataObject\Data\UrlSlug;
use Pimcore\Model\Site;
use Pimcore\Tool;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class SluggableListener implements EventSubscriberInterface
{
    public function __construct(protected SluggableSluggerInterface $slugger)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            DataObjectEvents::PRE_UPDATE => 'preUpdate',
            DataObjectEvents::PRE_ADD => 'preUpdate',
        ];
    }

    public function preUpdate(DataObjectEvent $dataObjectEvent): void
    {
        $object = $dataObjectEvent->getObject();

        if (!$object instanceof SluggableInterface) {
            return;
        }

        $sites = new Site\Listing();

        foreach (Tool::getValidLanguages() as $language) {
            $newSlugs = [];
            $actualSlugs = [];

            try {
                $slug = $this->slugger->slug($object, $language);
            } catch (SlugNotPossibleException $exception) {
                continue;
            }

            $i = 1;

            while (true) {
                /** @psalm-suppress InternalMethod */
                $existingSlug = UrlSlug::resolveSlug($slug);

                if (null === $existingSlug || $existingSlug->getObjectId() === $object->getId()) {
                    break;
                }

                $slug = $this->slugger->slug($object, $language, (string)$i);
                $i++;
            }

            $newSlugs[] = new UrlSlug($slug, 0);

            foreach ($sites->getSites() as $site) {
                $newSlugs[] = new UrlSlug($slug, $site->getId());
            }

            $existingSlugs = $object->getSlug($language);

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

            $object->setSlug($actualSlugs, $language);
        }
    }
}
