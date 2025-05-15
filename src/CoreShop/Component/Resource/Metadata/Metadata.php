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

namespace CoreShop\Component\Resource\Metadata;

use Doctrine\Inflector\InflectorFactory;

final class Metadata implements MetadataInterface
{
    private string $driver;

    private ?string $templatesNamespace;

    private array $parameters = [];

    private function __construct(
        private string $name,
        private string $applicationName,
        array $parameters,
    ) {
        $this->driver = $parameters['driver'];
        $this->templatesNamespace = array_key_exists('templates', $parameters) ? $parameters['templates'] : null;

        $this->parameters = $parameters;
    }

    public static function fromAliasAndConfiguration(string $alias, array $parameters): self
    {
        [$applicationName, $name] = self::parseAlias($alias);

        return new self($name, $applicationName, $parameters);
    }

    public function getAlias(): string
    {
        return $this->applicationName . '.' . $this->name;
    }

    public function getApplicationName(): string
    {
        return $this->applicationName;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getHumanizedName(): string
    {
        return trim(strtolower(preg_replace(['/([A-Z])/', '/[_\s]+/'], ['_$1', ' '], $this->name)));
    }

    public function getPluralName(): string
    {
        $inflector = InflectorFactory::create()->build();

        return $inflector->pluralize($this->name);
    }

    public function getDriver(): string
    {
        return $this->driver;
    }

    public function getTemplatesNamespace(): string
    {
        return $this->templatesNamespace;
    }

    public function getParameter(string $name)
    {
        if (!$this->hasParameter($name)) {
            throw new \InvalidArgumentException(sprintf('Parameter "%s" is not configured for resource "%s".', $name, $this->getAlias()));
        }

        return $this->parameters[$name];
    }

    public function hasParameter(string $name): bool
    {
        return array_key_exists($name, $this->parameters);
    }

    public function getParameters(): array
    {
        return $this->parameters;
    }

    public function getClass(string $name)
    {
        if (!$this->hasClass($name)) {
            throw new \InvalidArgumentException(sprintf('Class "%s" is not configured for resource "%s".', $name, $this->getAlias()));
        }

        return $this->parameters['classes'][$name];
    }

    public function hasClass(string $name): bool
    {
        return isset($this->parameters['classes'][$name]);
    }

    public function getServiceId(string $serviceName): string
    {
        return sprintf('%s.%s.%s', $this->applicationName, $serviceName, $this->name);
    }

    public function getPermissionCode($permissionName): string
    {
        return sprintf('%s.%s.%s', $this->applicationName, $this->name, $permissionName);
    }

    /**
     * @param string $alias
     */
    private static function parseAlias($alias): array
    {
        if (!str_contains($alias, '.')) {
            throw new \InvalidArgumentException('Invalid alias supplied, it should conform to the following format "<applicationName>.<name>".');
        }

        return explode('.', $alias);
    }
}
