<?php

namespace App\Entity\Traits;

use App\EventSubscriber\UpdateTraitSubscriber;

trait UpdateTrait
{
    protected $updatedAt = null;
    protected $updatedBy = null;

    /**
     * @return \DateTimeImmutable
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @return int
     */
    public function getUpdatedBy()
    {
        return $this->updatedBy;
    }

    /**
     * Called on doctrine event subscribers.
     *
     * @param $eventSubscriberName
     * @param $userId
     *
     * @throws \Exception
     *
     * @return bool
     */
    public function setUpdated($eventSubscriberName = null, $userId = null)
    {
        if ($eventSubscriberName !== UpdateTraitSubscriber::class) {
            throw new \Exception('In order to update, do a flush on the entity');
        }
        $this->updatedAt = new \DateTimeImmutable('now');

        if ($userId) {
            $this->updatedBy = $userId;
        }
    }
}
