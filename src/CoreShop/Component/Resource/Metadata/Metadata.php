<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2020 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Component\Resource\Metadata;

use Doctrine\Common\Inflector\Inflector;

final class Metadata implements MetadataInterface
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $applicationName;

    /**
     * @var string
     */
    private $driver;

    /**
     * @var string
     */
    private $templatesNamespace;

    /**
     * @var array
     */
    private $parameters;

    /**
     * @param string $name
     * @param string $applicationName
     * @param array  $parameters
     */
    private function __construct($name, $applicationName, array $parameters)
    {
        $this->name = $name;
        $this->applicationName = $applicationName;

        $this->driver = $parameters['driver'];
        $this->templatesNamespace = array_key_exists('templates', $parameters) ? $parameters['templates'] : null;

        $this->parameters = $parameters;
    }

    /**
     * @param string $alias
     * @param array  $parameters
     *
     * @return self
     */
    public static function fromAliasAndConfiguration($alias, array $parameters): Metadata
    {
        list($applicationName, $name) = self::parseAlias($alias);

        return new self($name, $applicationName, $parameters);
    }

    /**
     * {@inheritdoc}
     */
    public function getAlias(): string
    {
        return $this->applicationName . '.' . $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function getApplicationName(): string
    {
        return $this->applicationName;
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function getHumanizedName(): string
    {
        return trim(strtolower(preg_replace(['/([A-Z])/', '/[_\s]+/'], ['_$1', ' '], $this->name)));
    }

    /**
     * {@inheritdoc}
     */
    public function getPluralName(): string
    {
        return Inflector::pluralize($this->name);
    }

    /**
     * {@inheritdoc}
     */
    public function getDriver(): string
    {
        return $this->driver;
    }

    /**
     * {@inheritdoc}
     */
    public function getTemplatesNamespace(): string
    {
        return $this->templatesNamespace;
    }

    /**
     * {@inheritdoc}
     */
    public function getParameter($name)
    {
        if (!$this->hasParameter($name)) {
            throw new \InvalidArgumentException(sprintf('Parameter "%s" is not configured for resource "%s".', $name, $this->getAlias()));
        }

        return $this->parameters[$name];
    }

    /**
     * {@inheritdoc}
     */
    public function hasParameter($name): bool
    {
        return array_key_exists($name, $this->parameters);
    }

    /**
     * {@inheritdoc}
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }

    /**
     * {@inheritdoc}
     */
    public function getClass($name)
    {
        if (!$this->hasClass($name)) {
            throw new \InvalidArgumentException(sprintf('Class "%s" is not configured for resource "%s".', $name, $this->getAlias()));
        }

        return $this->parameters['classes'][$name];
    }

    /**
     * {@inheritdoc}
     */
    public function hasClass($name): bool
    {
        return isset($this->parameters['classes'][$name]);
    }

    /**
     * {@inheritdoc}
     */
    public function getServiceId($serviceName): string
    {
        return sprintf('%s.%s.%s', $this->applicationName, $serviceName, $this->name);
    }

    /**
     * {@inheritdoc}
     */
    public function getPermissionCode($permissionName): string
    {
        return sprintf('%s.%s.%s', $this->applicationName, $this->name, $permissionName);
    }

    /**
     * @param string $alias
     *
     * @return array
     */
    private static function parseAlias($alias): array
    {
        if (false === strpos($alias, '.')) {
            throw new \InvalidArgumentException('Invalid alias supplied, it should conform to the following format "<applicationName>.<name>".');
        }

        return explode('.', $alias);
    }
}
