<?php

namespace App\Entity\Traits;

use App\EventSubscriber\DeleteTraitSubscriber;

trait DeleteTrait
{
    protected $deletedAt = null;
    protected $deletedBy = null;

    /**
     * @return \DateTimeImmutable
     */
    public function getDeletedAt()
    {
        return $this->deletedAt;
    }

    /**
     * @return int
     */
    public function getDeletedBy()
    {
        return $this->deletedBy;
    }

    /**
     * @return bool
     */
    public function isDeleted()
    {
        if ($this->deletedAt !== null) {
            return true;
        }

        return false;
    }

    /**
     * In order to delete an entity, do a entitymanager->remove().
     *
     * @param $eventSubscriberName
     * @param $userId
     * @param $dateTime null
     *
     * @throws \Exception
     *
     * @return bool
     */
    public function delete($eventSubscriberName = null, $userId = null, $dateTime = null)
    {
        if ($eventSubscriberName !== DeleteTraitSubscriber::class) {
            throw new \RuntimeException('To remove this entity call to this entitymanager->remove() method');
        }
        if ($this->isDeleted()) {
            throw new \RuntimeException('Could not do this transition. This is already deleted');
        }

        $this->deletedBy = $userId;

        $this->deletedAt = new \DateTimeImmutable('now');

        if ($dateTime) {
            $this->deletedAt = $dateTime;
        }
    }

    public function recover(): self
    {
        $this->deletedAt = null;
        $this->deletedBy = null;

        return $this;
    }
}
