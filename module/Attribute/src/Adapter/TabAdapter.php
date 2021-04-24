<?php
/**
 * Class UserAdapter
 * @package Attribute\Adapter
 *
 * since: 30.07.2020
 * author: alexej@kisselev.de
 */

namespace Attribute\Adapter;

use Application\View\Manager\EmptyAdapter;
use Attribute\Entity\AttributeTab;

class TabAdapter extends EmptyAdapter
{
    public function getEntityName(): string
    {
        return AttributeTab::class;
    }
}
