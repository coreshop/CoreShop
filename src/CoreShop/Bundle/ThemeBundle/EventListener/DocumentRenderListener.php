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
        private DocumentThemeResolverInterface $themeResolver
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
        }
        catch (ThemeNotResolvedException) {
            //Ignore
        }
    }
}
