<?php

    /**
     *
     * FilterUtils.php
     *
     * @since  01.07.2017
     * @author Alexej Kisselev <alexej.kisselev@gmail.com>
     */

    namespace Application\Utils;

    use Doctrine\ORM\Mapping\ClassMetadata;
    use Doctrine\ORM\Mapping\ClassMetadataInfo;
    use Doctrine\ORM\Mapping\MappingException;
    use Doctrine\ORM\Query\Expr\From;
    use Doctrine\ORM\Query\Expr\Join;
    use Doctrine\ORM\QueryBuilder;
    use InvalidArgumentException;
    use RuntimeException;

    abstract class DoctrineUtils
    {

        public const NONE = 'NONE';
        public const EQUALS = 'EQUALS';
        public const GT = 'GT';
        public const GTE = 'GTE';
        public const LT = 'LT';
        public const LTE = 'LTE';
        public const RIGHT = 'RIGHT';
        public const IN = 'IN';
        public const LEFT = 'LEFT';
        public const LIKE = 'LIKE';

        private static int $uniqueKey = 0;

        private static function getUniqueKey(): string
        {
            self::$uniqueKey++;
            return (string) self::$uniqueKey;
        }

        /**
         * @param QueryBuilder $qb
         * @param mixed $rawTerm
         * @param string $field
         * @param string $associationAlias
         */
        public static function addFilter(QueryBuilder $qb, $rawTerm, $field, $associationAlias = null): void
        {
            $term = self::prepareTerm($rawTerm);
            if ((string)$term === 0) {
                return;
            }
            $alias = empty($associationAlias) ? self::getRootAlias($qb, false) : $associationAlias;
            $filterType = self::getFilterType($term);

            $normalizeType = static function (ClassMetadata $cm, $field) use (&$term, &$filterType) {
                if ($cm->hasField($field)) {
                    $fm = $cm->getFieldMapping($field);
                    if (in_array($filterType, [self::NONE, self::LIKE, self::LEFT, self::RIGHT], true)) {
                        switch ($fm['type']) {
                            case 'integer':
                                $term = (int)$term;
                                $filterType = self::EQUALS;
                                break;
                            case 'float':
                                $term = (float)$term;
                                $filterType = self::EQUALS;
                                break;
                            case 'boolean':
                                $term = (boolean)$term;
                                $filterType = self::EQUALS;
                                break;
                        }
                    }
                }
            };

            /** @var From[] $from */
            $from = $qb->getDQLPart('from');
            $em = $qb->getEntityManager();
            $cm = $em->getClassMetadata($from[0]->getFrom());
            try {
                $normalizeType($cm, $field);
                $am = $cm->getAssociationMapping($field);
                if (!empty($am['relationToTargetKeyColumns']) && is_array($am['relationToTargetKeyColumns']) && count($am['relationToTargetKeyColumns']) > 1) {
                    throw new RuntimeException('Joins to tables with multi-column identifiers are not supported');
                }
                if (empty(array_filter($qb->getDQLPart('join'), static function (array $joins) use ($am) {
                    return array_filter($joins, static function (Join $join) use ($am) {
                        return $join->getJoin() === $am['targetEntity'];
                    });
                }))
                ) {
                    $ta = $am['fieldName'];
                    if(!in_array($ta, $qb->getAllAliases(),true)){
                        $qb->innerJoin(sprintf('%s.%s', $alias, $field), $ta);
                    }

                    $alias = $ta;
                }

                $field = '';
                if ($am['type'] === ClassMetadataInfo::MANY_TO_MANY) {
                    if (isset($am['relationToTargetKeyColumns'])) {
                        $field = array_shift($am['relationToTargetKeyColumns']);
                    }
                }
                elseif (isset($am['sourceToTargetKeyColumns'])) {
                    $field = array_shift($am['sourceToTargetKeyColumns']);
                }
                if (empty($field)) {
                    //@todo remove kostyl
                    $field = 'id';
                }
                $normalizeType($em->getClassMetadata($am['targetEntity']), $field);
            } catch (MappingException $e) {
            }

            $expr = null;
            $exprColumn = sprintf('%s.%s', $alias, $field);
            $exprParam = ':' . $alias . $field . self::getUniqueKey();

            switch ($filterType) {
                case self::NONE:
                case self::LIKE:
                    $expr = $qb->expr()->like($exprColumn, $exprParam);
                    $term = "%$term%";
                    break;
                case self::EQUALS:
                    $expr = $qb->expr()->eq($exprColumn, $exprParam);
                    break;
                case self::GT:
                    $expr = $qb->expr()->gt($exprColumn, $exprParam);
                    break;
                case self::GTE:
                    $expr = $qb->expr()->gte($exprColumn, $exprParam);
                    break;
                case self::LT:
                    $expr = $qb->expr()->lt($exprColumn, $exprParam);
                    break;
                case self::LTE:
                    $expr = $qb->expr()->lte($exprColumn, $exprParam);
                    break;
                case self::LEFT:
                    $expr = $qb->expr()->like($exprColumn, $exprParam);
                    $term = "%$term";
                    break;
                case self::RIGHT:
                    $expr = $qb->expr()->like($exprColumn, $exprParam);
                    $term = "$term%";
                    break;
                case self::IN:
                    $expr = $qb->expr()->in($exprColumn, $exprParam);
                    $term = explode(',', $term);
                    break;
                default:
                    throw new InvalidArgumentException("No filter type[$filterType] found");
            }
            $qb->andWhere($expr);
            $qb->setParameter($exprParam, $term);
        }

        /** @noinspection RegExpRedundantEscape */
        private static function getFilterType(&$term): string
        {
            if (is_array($term) || preg_match('/[,]/', $term)) {
                return self::IN;
            }
            if (preg_match('/[*]/', $term)) {
                $param = explode('*', preg_replace('/[*]+/', '*', $term));
                $term = str_replace('*', '', $term);
                $function = self::LIKE;

                if (count($param) === 2) {
                    $function = empty($param[0]) ? self::LEFT : self::RIGHT;
                }
                return $function;
            }
            if (preg_match('/^[\>]/', $term)) {
                $term = preg_replace('/^[\>]/', '', $term);
                $f = self::GT;
                if (preg_match('/^[=]/', $term)) {
                    $term = preg_replace('/^[=]+/', '', $term);
                    $f = self::GTE;
                }
                return $f;
            }
            if (preg_match('/^[\<]/', $term)) {
                $term = preg_replace('/^[\<]/', '', $term);
                $f = self::LT;
                if (preg_match('/^[=]/', $term)) {
                    $term = preg_replace('/^[=]+/', '', $term);
                    $f = self::LTE;
                }
                return $f;
            }
            if (preg_match('/^[=]/', $term)) {
                $term = preg_replace('/^[=]+/', '', $term);
                return self::EQUALS;
            }
            return self::NONE;
        }

        /**
         * @param mixed $rawTerm
         * @return string|null
         */
        private static function prepareTerm($rawTerm): ?string
        {
            $term = $rawTerm;
            if (empty($term) && !is_numeric($term)) {
                return null;
            }
            if (is_array($rawTerm)) {
                $param = preg_grep('/^(\w+)$/', array_map('trim', (array)$term));
                $term = implode(',', $param);
                if (empty($term)) {
                    return null;
                }
            }
            return trim($term);
        }

        public static function addSort(QueryBuilder $qb, $rawOrder, $associationAlias = null): void
        {
            if (empty($rawOrder) || !is_string($rawOrder)) {
                return;
            }
            $order = explode(URL_VALUE_SEPARATOR, $rawOrder);
            $field = $order[0];
            $dir = empty($order[1]) ? 'ASC' : $order[1];
            $alias = empty($associationAlias) ? self::getRootAlias($qb, false) : $associationAlias;
            $qb->orderBy("$alias.$field", $dir);
        }

        /**
         * @param QueryBuilder $qb
         * @param bool $dot
         * @return string
         */
        public static function getRootAlias(QueryBuilder $qb, $dot = true): string
        {
            $all = $qb->getRootAliases();
            return !empty($all[0]) ? $all[0] . ($dot ? '.' : '') : '';
        }
    }
