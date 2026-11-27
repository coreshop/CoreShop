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


namespace CoreShop\Component\Address\Formatter;

use CoreShop\Component\Address\Model\AddressIdentifierInterface;
use CoreShop\Component\Address\Model\CountryInterface;
use CoreShop\Component\Address\Model\StateInterface;
use Symfony\Bridge\Twig\Extension\TranslationExtension;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;
use Twig\Extension\SandboxExtension;
use Twig\Loader\ArrayLoader;
use Twig\Sandbox\SecurityPolicy;
use Twig\Sandbox\SecurityPolicyInterface;

/**
 * Creates the Twig environment used to render a country's address format.
 *
 * The address format is editable by back-office users, so it must never be rendered
 * through the application's Twig environment. This environment is dedicated to it
 * (own loader, no application extensions/globals) and always sandboxed with a strict
 * allow-list policy.
 */
final class AddressFormatSandboxFactory
{
    public const ALLOWED_TAGS = ['if', 'for', 'set', 'apply'];

    public const ALLOWED_FILTERS = [
        'abs',
        'capitalize',
        'default',
        'e',
        'escape',
        'first',
        'format',
        'join',
        'last',
        'length',
        'lower',
        'nl2br',
        'replace',
        'slice',
        'split',
        'striptags',
        'title',
        'trans',
        'trim',
        'upper',
    ];

    public const ALLOWED_FUNCTIONS = [];

    public const ALLOWED_TESTS = ['defined', 'empty', 'null', 'none', 'same as', 'odd', 'even', 'divisible by', 'iterable'];

    public const ALLOWED_METHODS = [
        CountryInterface::class => ['getName', 'getIsoCode'],
        StateInterface::class => ['getName', 'getIsoCode'],
        AddressIdentifierInterface::class => ['getName'],
    ];

    public const ALLOWED_PROPERTIES = [];

    public static function createSecurityPolicy(): SecurityPolicyInterface
    {
        $policy = new SecurityPolicy(
            self::ALLOWED_TAGS,
            self::ALLOWED_FILTERS,
            self::ALLOWED_METHODS,
            self::ALLOWED_PROPERTIES,
            self::ALLOWED_FUNCTIONS,
        );

        if (method_exists($policy, 'setAllowedTests')) {
            $policy->setAllowedTests(self::ALLOWED_TESTS);
        }

        if (method_exists($policy, 'setStrict')) {
            $policy->setStrict(true);
        }

        return $policy;
    }

    /**
     * @psalm-suppress InternalClass, InternalMethod
     */
    public static function create(
        ?TranslatorInterface $translator = null,
        ?SecurityPolicyInterface $securityPolicy = null,
        string|false $cache = false,
    ): Environment {
        $environment = new Environment(new ArrayLoader(), [
            'autoescape' => 'html',
            'strict_variables' => false,
            'cache' => $cache,
        ]);

        if (null !== $translator && class_exists(TranslationExtension::class)) {
            $environment->addExtension(new TranslationExtension($translator));
        }

        $environment->addExtension(new SandboxExtension($securityPolicy ?? self::createSecurityPolicy(), true));

        return $environment;
    }

    /**
     * @psalm-suppress InternalClass, InternalMethod
     */
    public static function isSandboxed(Environment $environment): bool
    {
        if (!$environment->hasExtension(SandboxExtension::class)) {
            return false;
        }

        return $environment->getExtension(SandboxExtension::class)->isSandboxed();
    }
}
