<?php

namespace App\EventSubscriber;

use App\Entity\Traits\CreateTrait;
use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use Doctrine\ORM\Mapping\ClassMetadataInfo;

class CreateTraitSubscriber extends UserEnvironmentEventSubscriber implements EventSubscriber
{
    /**
     * Pre trait-create event.
     *
     * @var string
     */
    const PRE_TRAIT_CREATE = 'preTraitCreate';

    /**
     * Post trait-create event.
     *
     * @var string
     */
    const POST_TRAIT_CREATE = 'postTraitCreate';

    /**
     * {@inheritdoc}
     */
    public function getSubscribedEvents()
    {
        return [
            Events::loadClassMetadata,
            'prePersist',
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
        if (in_array(CreateTrait::class, $traits, true)) {
            $metadata->mapField(
                [
                    'type' => 'datetime',
                    'fieldName' => 'createdAt',
                    'columnName' => 'created_at',
                    'nullable' => 'true',
                ]
            );
            $metadata->mapField(
                [
                    'type' => 'string',
                    'fieldName' => 'createdBy',
                    'columnName' => 'created_by',
                    'nullable' => 'true',
                ]
            );
        }
    }

    public function prePersist(LifecycleEventArgs $lifecycleEventArgs)
    {
        $entity = $lifecycleEventArgs->getEntity();
        $objectManager = $lifecycleEventArgs->getObjectManager();
        $eventManager = $lifecycleEventArgs->getEntityManager()->getEventManager();

        // Only act if
        // - have the Timestampable trait
        if (in_array(CreateTrait::class, class_uses($entity), true)) {
            $object = $lifecycleEventArgs->getObject();

            $eventManager->dispatchEvent(
                self::PRE_TRAIT_CREATE,
                new LifecycleEventArgs($object, $objectManager)
            );

            $object->setCreated(get_class($this), $this->getUserId());

            $eventManager->dispatchEvent(
                self::POST_TRAIT_CREATE,
                new LifecycleEventArgs($object, $objectManager)
            );
        }
    }
}
