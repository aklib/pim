<?php
/**
 * Class AbstractFormElementFactory
 * @package Application\Form\Factory * since: 17.07.2020
 * author: alexej@kisselev.de
 */

namespace Application\Form\Factory;


use Application\Repository\AbstractDoctrineDao;
use Application\ServiceManager\AbstractAwareContainer;
use Application\ServiceManager\Interfaces\Constant;
use Application\ServiceManager\Interfaces\DoctrineDaoAware;
use Application\ServiceManager\Interfaces\TranslatorAware;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Exception;
use Laminas\Form\Element;
use Laminas\Form\Element\Checkbox;
use Laminas\Form\Element\Date;
use Laminas\Form\Element\Email;
use Laminas\Form\Element\Password;
use Laminas\Form\Element\Select;
use Laminas\Form\Element\Text;
use Laminas\Form\Element\Url;
use Laminas\I18n\Translator\TranslatorInterface;

abstract class AbstractEntityFormElementFactory extends AbstractAwareContainer implements TranslatorAware
{
    private ?TranslatorInterface $translator;
    private array $checkboxOptions = [];
    private array $elementOptions = [];

//======================= METHODS =======================

    protected function getCustomElementMeta(string $colName): array
    {
        if (!class_exists($this->getEntityName())) {
            return [];
        }

        $classMetadata = $this->getEntityManager()->getClassMetadata($this->getEntityName());
        $mapping = [];
        if ($classMetadata->hasField($colName)) {
            $mapping = $classMetadata->fieldMappings[$colName];
        } elseif ($classMetadata->hasAssociation($colName)) {
            $mapping = $classMetadata->associationMappings[$colName];
        }
        return $mapping['element'] ?? [];
    }

    /**
     * Gets type of entity field from doctrine mapping
     * @param string $colName
     * @return string
     */
    protected function getFieldType(string $colName): string
    {
        if (!class_exists($this->getEntityName())) {
            return 'string';
        }

        $classMetadata = $this->getEntityManager()->getClassMetadata($this->getEntityName());
        if ($classMetadata->hasField($colName)) {
            return $classMetadata->getTypeOfField($colName);
        }

        if ($classMetadata->hasAssociation($colName)) {
            return $classMetadata->associationMappings[$colName]['type'];
        }
        return '';
    }

    public function getCheckboxOptions(): array
    {
        if (!empty($this->checkboxOptions)) {
            return $this->checkboxOptions;
        }
        $col = $this->getRequest()->isXmlHttpRequest() ? 9 : 6;
        $this->checkboxOptions =
            [
                'label_attributes'   => [
                    'class' => 'col-lg-3 col-form-label text-lg-right text-md-left text-sm-left'
                ],
                'use_hidden_element' => true,
                'checked_value'      => 1,
                'unchecked_value'    => 0,
                'extended'           => [
                    'wrap' => ['class' => sprintf("col-lg-%d col-form-label", $col)],
                    'info' => ''
                ]
            ];
        return $this->checkboxOptions;
    }

    public function getElementOptions(): array
    {
        if (!empty($this->elementOptions)) {
            return $this->elementOptions;
        }
        $col = $this->getRequest()->isXmlHttpRequest() ? 9 : 6;
        $this->elementOptions =
            [
                'label'            => '',
                'label_attributes' => [
                    'class' => 'col-lg-3 col-form-label text-lg-right text-md-left text-sm-left'
                ],
                'extended'         => [
                    'wrap' => ['class' => sprintf("col-lg-%d", $col)],
                    'info' => ''
                ]
            ];
        return $this->elementOptions;
    }

    protected function getElementHTML5(string $colName, string $type, bool $isFilter = false): ?Element
    {
        $element = null;
        switch ($type) {
            case 'datetime':
                $element = new Date($colName, $this->getElementOptions());
                $element->setFormat('d.m.Y');
                break;
            case 'url':
                if (!$isFilter) {
                    $element = new Url($colName, $this->getElementOptions());
                }
        }
        if ($element instanceof Element) {
            return $element;
        }
        if (!$isFilter) {
            switch ($colName) {
                case 'email':
                    $element = new Email($colName, $this->getElementOptions());
                    break;
                case 'password':
                    $element = new Password($colName, $this->getElementOptions());
                    break;
                case 'url':
                    $element = new Url($colName, $this->getElementOptions());
            }
        }
        return $element;
    }

    /** @noinspection PhpMissingBreakStatementInspection */
    protected function createElement(string $colName, bool $isFilter = true): ?Element
    {
        if ($colName === 'attributeValues') {
            return null;
        }
        $multiple = false;
        $classMetadata = $this->getEntityManager()->getClassMetadata($this->getEntityName());

        $props = $this->getCustomElementMeta($colName);
        $elementClass = null;
        if (!empty($props['type'])) {
            $elementClass = $props['type'];
        }
        switch ($this->getFieldType($colName)) {
            case ClassMetadataInfo::MANY_TO_MANY:
            case ClassMetadataInfo::ONE_TO_MANY:
                $multiple = true;
            case ClassMetadataInfo::MANY_TO_ONE:
                $elementClass = $elementClass ?? Select::class;
                /** @var AbstractDoctrineDao $repository */
                $targetEntity = $classMetadata->getAssociationTargetClass($colName);
                $repository = $this->getEntityManager()->getRepository($targetEntity);
                $element = new $elementClass($colName, $this->getElementOptions());

                if ($repository instanceof DoctrineDaoAware && $element instanceof Select) {
                    // add with empty - not selected value
                    try {

                        $options = $isFilter ? ['' => ''] : [];
                        $options = array_replace_recursive($options, $repository->getNamedQueryResult(Constant::NAMED_QUERY_DROPDOWN_CHOICE));
                        foreach ($options as $key => $option) {
                            $options[$key] = $this->translate($option);
                        }
                        $element->setValueOptions($options);
                    } catch (Exception $e) {
                        die($e->getMessage());
                        $element->setValueOptions([]);
                    }
                }
                break;
            case 'boolean':
                $element = new Checkbox($colName, $this->getCheckboxOptions());
                break;
            default:
                if (empty($elementClass)) {
                    $element = $this->getElementHTML5($colName, $this->getFieldType($colName));
                    if ($element === null) {
                        $element = new Text($colName, $this->getElementOptions());
                    }
                } else {
                    $element = new $elementClass($colName, $this->getElementOptions());
                }

        }
        if ($multiple) {
            $element->setAttribute('multiple', true);
        }
        return $element;
    }

//======================= INTERFACE IMPLEMENTATIONS =======================

    public function getEntityName(): string
    {
        if (parent::getEntityName() === '') {
            $this->setEntityName($this->getAdapter()->getEntityName());
        }
        return parent::getEntityName();
    }

    public function setTranslator(TranslatorInterface $translator = null): void
    {
        $this->translator = $translator;
    }

    public function translate(string $text = null): string
    {
        if ($text === null || $this->translator === null) {
            return $text;
        }
        return $this->translator->translate($text);
    }
}