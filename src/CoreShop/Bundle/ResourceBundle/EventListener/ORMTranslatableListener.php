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

namespace CoreShop\Bundle\ResourceBundle\EventListener;

use CoreShop\Component\Resource\Metadata\MetadataInterface;
use CoreShop\Component\Resource\Metadata\RegistryInterface;
use CoreShop\Component\Resource\Model\TranslatableInterface;
use CoreShop\Component\Resource\Model\TranslationInterface;
use CoreShop\Component\Resource\Translation\TranslatableEntityLocaleAssignerInterface;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Events;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Doctrine\Persistence\Event\LifecycleEventArgs;

final class ORMTranslatableListener implements EventSubscriber
{
    public function __construct(
        private RegistryInterface $resourceMetadataRegistry,
        private TranslatableEntityLocaleAssignerInterface $translatableEntityLocaleAssigner,
    ) {
    }

    public function getSubscribedEvents(): array
    {
        return [
            Events::loadClassMetadata,
            Events::postLoad,
        ];
    }

    public function loadClassMetadata(LoadClassMetadataEventArgs $eventArgs): void
    {
        /**
         * @var ClassMetadataInfo $classMetadata
         */
        $classMetadata = $eventArgs->getClassMetadata();
        $reflection = $classMetadata->reflClass;

        if ($reflection->isAbstract()) {
            return;
        }

        if ($reflection->implementsInterface(TranslatableInterface::class)) {
            $this->mapTranslatable($classMetadata);
        }

        if ($reflection->implementsInterface(TranslationInterface::class)) {
            $this->mapTranslation($classMetadata);
        }
    }

    public function postLoad(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();

        if (!$entity instanceof TranslatableInterface) {
            return;
        }

        $this->translatableEntityLocaleAssigner->assignLocale($entity);
    }

    private function mapTranslatable(ClassMetadataInfo $metadata): void
    {
        $className = $metadata->name;

        try {
            $resourceMetadata = $this->resourceMetadataRegistry->getByClass($className);
        } catch (\InvalidArgumentException) {
            return;
        }

        if (!$resourceMetadata->hasParameter('translation')) {
            return;
        }

        /** @var MetadataInterface $translationResourceMetadata */
        $translationResourceMetadata = $this->resourceMetadataRegistry->get($resourceMetadata->getAlias() . '_translation');

        if (!$metadata->hasAssociation('translations')) {
            $metadata->mapOneToMany([
                'fieldName' => 'translations',
                'targetEntity' => $translationResourceMetadata->getClass('model'),
                'mappedBy' => 'translatable',
                'fetch' => ClassMetadataInfo::FETCH_EXTRA_LAZY,
                'indexBy' => 'locale',
                'cascade' => ['persist', 'merge', 'remove'],
                'orphanRemoval' => true,
            ]);
        }
    }

    private function mapTranslation(ClassMetadataInfo $metadata): void
    {
        $className = $metadata->name;

        try {
            $resourceMetadata = $this->resourceMetadataRegistry->getByClass($className);
        } catch (\InvalidArgumentException) {
            return;
        }

        /** @var MetadataInterface $translatableResourceMetadata */
        $translatableResourceMetadata = $this->resourceMetadataRegistry->get(str_replace('_translation', '', $resourceMetadata->getAlias()));

        if (!$metadata->hasAssociation('translatable')) {
            $metadata->mapManyToOne([
                'fieldName' => 'translatable',
                'targetEntity' => $translatableResourceMetadata->getClass('model'),
                'inversedBy' => 'translations',
                'joinColumns' => [[
                    'name' => 'translatable_id',
                    'referencedColumnName' => 'id',
                    'onDelete' => 'CASCADE',
                    'nullable' => false,
                ]],
            ]);
        }

        if (!$metadata->hasField('locale')) {
            $metadata->mapField([
                'fieldName' => 'locale',
                'type' => 'string',
                'nullable' => false,
                'length' => 5,
            ]);
        }

        // Map unique index.
        $columns = [
            $metadata->getSingleAssociationJoinColumnName('translatable'),
            'locale',
        ];

        if (!$this->hasUniqueConstraint($metadata, $columns)) {
            $constraints = $metadata->table['uniqueConstraints'] ?? [];

            $constraints[$metadata->getTableName() . '_uniq_trans'] = [
                'columns' => $columns,
            ];

            $metadata->setPrimaryTable([
                'uniqueConstraints' => $constraints,
            ]);
        }
    }

    private function hasUniqueConstraint(ClassMetadataInfo $metadata, array $columns): bool
    {
        if (!isset($metadata->table['uniqueConstraints'])) {
            return false;
        }

        foreach ($metadata->table['uniqueConstraints'] as $constraint) {
            if (!array_diff($constraint['columns'], $columns)) {
                return true;
            }
        }

        return false;
    }
}
