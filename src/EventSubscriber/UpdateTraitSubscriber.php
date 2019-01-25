<?php

namespace App\EventSubscriber;

use App\Entity\Traits\UpdateTrait;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Events;
use Doctrine\ORM\Mapping\ClassMetadataInfo;

class UpdateTraitSubscriber extends UserEnvironmentEventSubscriber implements EventSubscriber
{
    /**
     * Pre trait-create event.
     *
     * @var string
     */
    const PRE_TRAIT_UPDATE = 'preTraitUpdate';

    /**
     * Post trait-create event.
     *
     * @var string
     */
    const POST_TRAIT_UPDATE = 'postTraitUpdate';

    /**
     * {@inheritdoc}
     */
    public function getSubscribedEvents()
    {
        return [
            Events::loadClassMetadata,
            'preUpdate',
        ];
    }

    /**
     * @param LoadClassMetadataEventArgs $eventArgs
     */
    public function loadClassMetadata(LoadClassMetadataEventArgs $eventArgs)
    {
        /** @var ClassMetadataInfo $metadata */
        $metadata = $eventArgs->getClassMetadata();
        $reflectionClass = $metadata->getReflectionClass();
        if (!$reflectionClass) {
            return;
        }
        $traits = $reflectionClass->getTraitNames();
        if (in_array(UpdateTrait::class, $traits, true)) {
            $metadata->mapField(
                [
                    'type' => 'datetime',
                    'fieldName' => 'updatedAt',
                    'columnName' => 'updated_at',
                    'nullable' => 'true',
                ]
            );
            $metadata->mapField(
                [
                    'type' => 'string',
                    'fieldName' => 'updatedBy',
                    'columnName' => 'updated_by',
                    'nullable' => 'true',
                ]
            );
        }
    }

    public function preUpdate(LifecycleEventArgs $lifecycleEventArgs)
    {
        $entity = $lifecycleEventArgs->getEntity();
        $objectManager = $lifecycleEventArgs->getObjectManager();
        $eventManager = $lifecycleEventArgs->getEntityManager()->getEventManager();

        // Only act if
        // - have the Timestampable trait
        // - php_sapi_name is not cli OR we are on test mode
        if (in_array(UpdateTrait::class, class_uses($entity), true) &&
            $this->isCorrectEnvironment()
        ) {
            $object = $lifecycleEventArgs->getObject();

            $eventManager->dispatchEvent(
                self::PRE_TRAIT_UPDATE,
                new LifecycleEventArgs($object, $objectManager)
            );

            $object->setUpdated(get_class($this), $this->getUserId());

            $object = $lifecycleEventArgs->getObject();

            $eventManager->dispatchEvent(
                self::POST_TRAIT_UPDATE,
                new LifecycleEventArgs($object, $objectManager)
            );
        }
    }
}
