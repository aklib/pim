<?php /** @noinspection PhpUnused */

/**
 * Class User
 *
 * Decorated outputs of User data in UI
 *
 * @since 09.07.2020
 * @author Alexej Kisselev <alexej.kisselev@gmail.com>
 */

namespace User\Decorator;

use Application\Decorator\AbstractPrettyBootstrapElement;
use Company\Entity\Advertiser;
use Company\Entity\Publisher;
use Doctrine\Common\Collections\Collection;
use User\Entity\User as UserEntity;

/**
 * @method UserEntity getObject()
 */
class User extends AbstractPrettyBootstrapElement
{

    public function getUserRole(): string
    {
        return $this->getObject()->getUserRole()->getName();
    }

    /**
     * @return string
     *
     */
    public function getDisplayName(): string
    {
        $subHeadline = $this->getObject()->getFirstName();
        if ($this->getObject()->getLastName() !== null) {
            $subHeadline .= ' ' . $this->getObject()->getLastName();
        }
        return $subHeadline;
    }

    public function getPublishers(): string
    {
        $publishers = $this->getObject()->getPublishers();
        if (!$publishers instanceof Collection) {
            return '';
        }
        $names = [];
        /** @var Publisher $publisher */
        foreach ($publishers as $publisher) {
            $names[] = $publisher->getName();
        }
        return implode(', ', $names);
    }

    public function getAdvertisers(): string
    {
        $advertisers = $this->getObject()->getAdvertisers();
        if (!$advertisers instanceof Collection) {
            return '';
        }
        $names = [];
        /** @var Advertiser $advertiser */
        foreach ($advertisers as $advertiser) {
            $names[] = $advertiser->getName();
        }
        return implode(', ', $names);
    }
}
