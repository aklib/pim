<?php
/**
 * Class AttributeEdit
 * @package Category\Form
 *
 * since: 15.10.2020
 * author: alexej@kisselev.de
 */

namespace Category\Form;


use Application\Form\EntityEdit;

class CategoryCreate extends EntityEdit
{

    protected function isPropertyShown($name): bool
    {
        $yes = parent::isPropertyShown($name);
        if (!$yes) {
            return false;
        }
        switch ($name) {
            case 'lft':
            case 'rgt':
            case 'children':
            case 'level':
            case 'root':
                return false;
        }
        return true;
    }
}