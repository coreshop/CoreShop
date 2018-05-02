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

namespace CoreShop\Bundle\SEOBundle\EventListener;

use CoreShop\Component\SEO\Generator\HeadMetaGeneratorInterface;
use CoreShop\Component\SEO\Model\SEOAwareInterface;
use Pimcore\Templating\Helper\HeadMeta;
use Pimcore\Templating\Helper\HeadTitle;
use Pimcore\Templating\Helper\Placeholder\Container;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class MetaDataListener implements EventSubscriberInterface
{
    /**
     * @var HeadMeta
     */
    protected $headMeta;

    /**
     * @var HeadTitle
     */
    protected $headTitle;

    /**
     * @var HeadMetaGeneratorInterface
     */
    protected $headMetaGenerator;

    /**
     * @param HeadMeta $headMeta
     * @param HeadTitle $headTitle
     * @param HeadMetaGeneratorInterface $headMetaGenerator
     */
    public function __construct(
        HeadMeta $headMeta,
        HeadTitle $headTitle,
        HeadMetaGeneratorInterface $headMetaGenerator
    )
    {
        $this->headMeta = $headMeta;
        $this->headTitle = $headTitle;
        $this->headMetaGenerator = $headMetaGenerator;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::RESPONSE => ['onKernelResponse'],
        ];
    }

    /**
     * @param FilterResponseEvent $event
     */
    public function onKernelResponse(FilterResponseEvent $event)
    {
        $request = $event->getRequest();

        if (!$event->isMasterRequest()) {
            return;
        }

        $seoObject = $request->get('seo_object');

        if (!$seoObject instanceof SEOAwareInterface) {
            return;
        }

        foreach ($this->headMetaGenerator->generateMeta($seoObject) as $property => $content) {
            if (!empty($content)) {
                $this->headMeta->appendProperty($property, $content);
            }
        }

        $this->headMeta->setDescription($this->headMetaGenerator->generateDescription($seoObject));
        $title = $this->headMetaGenerator->generateTitle($seoObject);

        switch ($this->headMetaGenerator->getTitlePosition()) {
            case Container::SET:
                $this->headTitle->set($title);
                break;
            case Container::PREPEND:
                $this->headTitle->prepend($title);
                break;
            case Container::APPEND:
            default:
                $this->headTitle->append($title);
                break;
        }
    }
}
