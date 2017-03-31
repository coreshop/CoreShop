<?php

namespace CoreShop\Component\Core\Metadata;

final class Registry implements RegistryInterface
{
    /**
     * @var array
     */
    private $metadata = [];

    /**
     * {@inheritdoc}
     */
    public function getAll()
    {
        return $this->metadata;
    }

    /**
     * {@inheritdoc}
     */
    public function get($alias)
    {
        if (!array_key_exists($alias, $this->metadata)) {
            throw new \InvalidArgumentException(sprintf('Model "%s" does not exist.', $alias));
        }

        return $this->metadata[$alias];
    }

    /**
     * {@inheritdoc}
     */
    public function getByClass($className)
    {
        foreach ($this->metadata as $metadata) {
            if ($className === $metadata->getClass('model')) {
                return $metadata;
            }
        }

        throw new \InvalidArgumentException(sprintf('Model with class "%s" does not exist.', $className));
    }

    /**
     * {@inheritdoc}
     */
    public function add(MetadataInterface $metadata)
    {
        $this->metadata[$metadata->getAlias()] = $metadata;
    }

    /**
     * {@inheritdoc}
     */
    public function addFromAliasAndConfiguration($alias, array $configuration)
    {
        $this->add(Metadata::fromAliasAndConfiguration($alias, $configuration));
    }
}
