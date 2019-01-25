<?php

namespace App\EventSubscriber;

use App\Entity\Traits\DeleteTrait;
use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Events;
use Doctrine\ORM\Mapping\ClassMetadataInfo;

class DeleteTraitSubscriber extends UserEnvironmentEventSubscriber implements EventSubscriber
{
    /**
     * Pre trait-delete event.
     *
     * @var string
     */
    const PRE_TRAIT_DELETE = 'preTraitDelete';

    /**
     * Post trait-delete event.
     *
     * @var string
     */
    const POST_TRAIT_DELETE = 'postTraitDelete';

    /**
     * {@inheritdoc}
     */
    public function getSubscribedEvents()
    {
        return [Events::loadClassMetadata, Events::onFlush];
    }

    /**
     * Maps dinamically the deleted_at and deleted_by fields.
     *
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

        if (in_array(DeleteTrait::class, $traits, true)) {
            $metadata->mapField(
                [
                    'type' => 'datetime',
                    'fieldName' => 'deletedAt',
                    'columnName' => 'deleted_at',
                    'nullable' => true,
                ]
            );
            $metadata->mapField(
                [
                    'type' => 'string',
                    'fieldName' => 'deletedBy',
                    'columnName' => 'deleted_by',
                    'nullable' => true,
                ]
            );
        }
    }

    /**
     * If has the trait Delete, we must call the delete method
     * and skip the removal of the object.
     *
     * @param OnFlushEventArgs $lifecycleEventArgs
     */
    public function onFlush(OnFlushEventArgs $lifecycleEventArgs)
    {
        $entityManager = $lifecycleEventArgs->getEntityManager();
        $unitOfWork = $entityManager->getUnitOfWork();

        foreach ($unitOfWork->getScheduledEntityDeletions() as $object) {
            if (method_exists($object, 'delete') && property_exists($object, 'deletedAt')) {
                $eventManager = $entityManager->getEventManager();

                $eventManager->dispatchEvent(
                    self::PRE_TRAIT_DELETE,
                    new LifecycleEventArgs($object, $entityManager)
                );

                if ($object->getDeletedAt() instanceof \DateTime) {
                    continue;
                }

                $now = new \DateTimeImmutable();

                $object->delete(get_class($this), $this->getUserId());

                $entityManager->persist($object);

                $unitOfWork->propertyChanged($object, 'deletedAt', '', $now);
                $unitOfWork->propertyChanged($object, 'deletedBy', '', $this->getUserId());

                $unitOfWork->scheduleExtraUpdate($object, [
                    'deletedAt' => ['', $now],
                    'deletedBy' => ['', $this->getUserId()],
                ]);

                $eventManager->dispatchEvent(
                    self::POST_TRAIT_DELETE,
                    new LifecycleEventArgs($object, $entityManager)
                );
            }
        }
    }
}
