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
use Pimcore\Model\DataObject\Concrete;
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
            DataObjectEvents::PRE_UPDATE => 'preUpdate'
        ];
    }

    public function preUpdate(DataObjectEvent $dataObjectEvent): void
    {
        $object = $dataObjectEvent->getObject();

        if (!$object instanceof SluggableInterface) {
            return;
        }

        foreach (Tool::getValidLanguages() as $language) {

            $urlSlugs = $object->getSlug($language);

            if($urlSlugs === null) {
                $urlSlugs = [];
            }

            $found = false;
            $key = null;
            foreach($urlSlugs as $key => $urlSlug) {
                if($urlSlug->getSiteId() === 0) {
                    $found = true;
                    break;
                }
            }

            if($found && !empty($urlSlugs[$key]->getSlug())) {
                continue;
            }

            try {
                $slug = $this->generateUniqueSlug($object, $language);
            } catch (SlugNotPossibleException $exception) {
                continue;
            }

            if(!$found) {
                $urlSlugs[] = new UrlSlug($slug, 0);
            } else {
                $urlSlugs[$key]->setSlug($slug);
            }

            $object->setSlug($urlSlugs, $language);
        }
    }

    private function generateUniqueSlug(SluggableInterface $object, string $language) : string
    {
        $slug = $this->slugger->slug($object, $language);

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

        return $slug;
    }
}
