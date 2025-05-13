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
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    https://www.coreshop.com/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Bundle\ThemeBundle\EventListener;

use CoreShop\Bundle\ThemeBundle\Service\DocumentThemeResolverInterface;
use CoreShop\Bundle\ThemeBundle\Service\ThemeNotResolvedException;
use Pimcore\Event\DocumentEvents;
use Pimcore\Event\Model\DocumentEvent;
use Sylius\Bundle\ThemeBundle\Context\SettableThemeContext;
use Sylius\Bundle\ThemeBundle\Repository\ThemeRepositoryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class DocumentRenderListener implements EventSubscriberInterface
{
    public function __construct(
        private ThemeRepositoryInterface $themeRepository,
        private SettableThemeContext $themeContext,
        private DocumentThemeResolverInterface $themeResolver,
    ) {
    }

    public static function getSubscribedEvents()
    {
        return [
            DocumentEvents::RENDERER_PRE_RENDER => 'preRender',
        ];
    }

    public function preRender(DocumentEvent $event)
    {
        $doc = $event->getDocument();

        try {
            $themeName = $this->themeResolver->resolveThemeForDocument($doc);

            if (!$themeName) {
                return;
            }

            $theme = $this->themeRepository->findOneByName($themeName);

            if (!$theme) {
                return;
            }

            $this->themeContext->setTheme($theme);
        } catch (ThemeNotResolvedException) {
            //Ignore
        }
    }
}
