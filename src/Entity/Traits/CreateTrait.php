<?php

namespace App\Entity\Traits;

use App\EventSubscriber\CreateTraitSubscriber;

trait CreateTrait
{
    protected $createdAt;
    protected $createdBy;

    /**
     * @return \DateTimeImmutable
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @return int
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * will be a prePersist lifecycle hook.
     *
     * @param null $eventSubscriberName
     * @param null $userId
     *
     * @throws \Exception
     */
    public function setCreated($eventSubscriberName = null, $userId = null)
    {
        if ($eventSubscriberName !== CreateTraitSubscriber::class) {
            throw new \RuntimeException('In order to create an entity call entityManager->add() method');
        }
        if ($this->getCreatedAt() !== null) {
            throw new \RuntimeException('Could not do this transition. This is already Created');
        }

        $this->createdBy = $userId;

        $this->createdAt = new \DateTimeImmutable('now');
    }
}
