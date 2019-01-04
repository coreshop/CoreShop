<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2019 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Component\Core\Locale\Context;

use CoreShop\Component\Locale\Context\LocaleContextInterface;
use CoreShop\Component\Locale\Context\LocaleNotFoundException;
use CoreShop\Component\Resource\Translation\Provider\TranslationLocaleProviderInterface;
use Pimcore\Bundle\AdminBundle\Security\User\TokenStorageUserResolver;
use Pimcore\Http\Request\Resolver\PimcoreContextResolver;
use Pimcore\Model\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class PimcoreAdminLocaleContext implements LocaleContextInterface
{
    /**
     * @var PimcoreContextResolver
     */
    private $pimcoreContextResolver;

    /**
     * @var TokenStorageUserResolver
     */
    private $tokenStorageUserResolver;

    /**
     * @var TranslationLocaleProviderInterface
     */
    private $localeProvider;

    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @param PimcoreContextResolver             $pimcoreContextResolver
     * @param TokenStorageUserResolver           $tokenStorageUserResolver
     * @param TranslationLocaleProviderInterface $localeProvider
     * @param RequestStack                       $requestStack
     */
    public function __construct(
        PimcoreContextResolver $pimcoreContextResolver,
        TokenStorageUserResolver $tokenStorageUserResolver,
        TranslationLocaleProviderInterface $localeProvider,
        RequestStack $requestStack
    ) {
        $this->pimcoreContextResolver = $pimcoreContextResolver;
        $this->tokenStorageUserResolver = $tokenStorageUserResolver;
        $this->localeProvider = $localeProvider;
        $this->requestStack = $requestStack;
    }

    /**
     * {@inheritdoc}
     */
    public function getLocaleCode()
    {
        $request = $this->requestStack->getCurrentRequest();

        if (!$request instanceof Request) {
            throw new LocaleNotFoundException('No Request in RequestStack, cannot determine Pimcore Context');
        }

        if ($this->pimcoreContextResolver->getPimcoreContext($request) !== PimcoreContextResolver::CONTEXT_ADMIN) {
            throw new LocaleNotFoundException('Not in Admin Mode');
        }

        $user = $this->tokenStorageUserResolver->getUser();

        if (!$user instanceof User) {
            throw new LocaleNotFoundException('No valid Admin User found');
        }

        $backendLanguage = $user->getLanguage();
        $localizedTableLanguage = null;

        $frontendLanguages = $this->localeProvider->getDefinedLocalesCodes();

        // no frontend language defined. this should never happen.
        if (empty($frontendLanguages)) {
            throw new LocaleNotFoundException('No valid Frontend Languages found');
        }

        if (!empty($backendLanguage) && in_array($backendLanguage, $frontendLanguages)) {
            $localizedTableLanguage = strtolower($backendLanguage);
        } else {
            $first = reset($frontendLanguages);
            $localizedTableLanguage = strtolower($first);
        }

        return $localizedTableLanguage;
    }
}
