<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GPLv3 and CCL
 */

declare(strict_types=1);

namespace CoreShop\Bundle\CoreBundle\Fixtures\Data\Demo;

use CoreShop\Bundle\FixtureBundle\Fixture\VersionedFixtureInterface;
use CoreShop\Component\Variant\Model\AttributeColorInterface;
use CoreShop\Component\Variant\Model\AttributeGroupInterface;
use CoreShop\Component\Variant\Model\AttributeInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Persistence\ObjectManager;
use Pimcore\Model\DataObject\Data\RgbaColor;
use Pimcore\Model\DataObject\Service;
use Pimcore\Tool;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class AttributeGroupsFixture extends AbstractFixture implements ContainerAwareInterface, VersionedFixtureInterface
{
    private ?ContainerInterface $container;

    public function getVersion(): string
    {
        return '2.0';
    }

    public function setContainer(ContainerInterface $container = null): void
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager): void
    {
        if (!count($this->container->get('coreshop.repository.attribute_group')->findAll())) {
            $data = [
                'color' => [
                    'red', 'blue', 'black'
                ],
                'size' => [
                    's', 'm', 'l', 'xl'
                ],
                'season' => [
                    'winter', 'summer'
                ]
            ];

            $colorMap = [
                'red' => new RgbaColor(255, 0, 0),
                'blue' => new RgbaColor(0, 0, 255),
                'black' => new RgbaColor(0, 0, 0),
            ];

            $index = 10;

            foreach ($data as $key => $attributes) {
                /**
                 * @var AttributeGroupInterface $attributeGroup
                 */
                $attributeGroup = $this->container->get('coreshop.factory.attribute_group')->createNew();
                $attributeGroup->setKey($key);
                $attributeGroup->setPublished(true);
                $attributeGroup->setParent(Service::createFolderByPath('/demo/attributes'));
                $attributeGroup->setSorting($index);

                foreach (Tool::getValidLanguages() as $language) {
                    $attributeGroup->setName(ucfirst($key), $language);
                }

                if ($key === 'color') {
                    $attributeGroup->setShowInList(true);
                }

                $attributeGroup->save();

                foreach ($attributes as $index => $attributeKey) {
                    /**
                     * @var AttributeInterface $attribute
                     */
                    $attribute = $key === 'color' ?
                        $this->container->get('coreshop.factory.attribute_color')->createNew() :
                        $this->container->get('coreshop.factory.attribute_value')->createNew();

                    $attribute->setParent($attributeGroup);
                    $attribute->setKey($attributeKey);
                    $attribute->setPublished(true);
                    $attribute->setAttributeGroup($attributeGroup);
                    $attribute->setSorting(($index+1)*10);

                    foreach (Tool::getValidLanguages() as $language) {
                        if ($key === 'size') {
                            $attribute->setName(strtoupper($attributeKey), $language);
                        }
                        else {
                            $attribute->setName(ucfirst($attributeKey), $language);
                        }
                    }

                    $attribute->setValueText($attributeKey);

                    if ($attribute instanceof AttributeColorInterface) {
                        $attribute->setValueColor($colorMap[$attributeKey]);
                    }

                    $attribute->save();
                }

                $index += 10;
            }
        }
    }
}
