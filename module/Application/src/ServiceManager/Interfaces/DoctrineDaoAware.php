<?php
/**
 * Class DoctrineDaoAware
 * @package Application\ServiceManager\Interfaces
 *
 * since: 27.04.2021
 * author: alexej@kisselev.de
 */

namespace Application\ServiceManager\Interfaces;


use Doctrine\ORM\Query;

interface DoctrineDaoAware
{
    public function getNamedQueryResult(string $name): array;
    public function setNamedQueryParameter(Query $query): void;
    public function getDropDownChoiceText(array $data): string;
}