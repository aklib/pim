<?php
/**
 * Class DoctrineEventSubscriberInterface
 * @package Application\Doctrine\Event
 *
 * since: 11.10.2020
 * author: alexej@kisselev.de
 */

namespace Application\Doctrine\Event;


interface DoctrineEventSubscriberInterface
{
    public function getUniqueKey(): string;
}