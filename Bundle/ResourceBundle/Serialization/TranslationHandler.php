<?php

namespace CoreShop\Bundle\ResourceBundle\Serialization;

use CoreShop\Component\Resource\Model\ResourceInterface;
use CoreShop\Component\Resource\Model\TranslatableInterface;
use CoreShop\Component\Resource\Model\TranslationInterface;
use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\Context;
use JMS\Serializer\JsonDeserializationVisitor;
use JMS\Serializer\JsonSerializationVisitor;

class TranslationHandler
{
    public function deserializeRelation(JsonDeserializationVisitor $visitor, $value, array $type, Context $context)
    {
        if ($visitor->getResult() instanceof TranslatableInterface) {
            $visitor->getResult()->getId();

            foreach ($value as $lang => $values) {
                foreach ($values as $key => $val) {
                    $translation = $visitor->getResult()->getTranslation($lang);

                    if ($translation instanceof TranslationInterface) {
                        $method = "set" . ucfirst($key);

                        if (method_exists($translation, $method)) {
                            $translation->$method($val);
                        }
                    }
                }
            }
        }
    }
}