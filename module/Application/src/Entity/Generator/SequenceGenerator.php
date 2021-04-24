<?php
    /**
     * Class SequenceGenerator
     * @package Application\Entity\Generator
     *
     * since: 20.08.2020
     * author: alexej@kisselev.de
     */

    namespace Application\Entity\Generator;


    use Application\Utils\ClassUtils;
    use Doctrine\DBAL\DBALException;
    use Doctrine\ORM\EntityManager;
    use Doctrine\ORM\Id\AbstractIdGenerator;

    class SequenceGenerator extends AbstractIdGenerator
    {

        public function generate(EntityManager $em, $entity)
        {
            $name = ClassUtils::getShortName($entity);
            /** @noinspection SqlResolve */
            $sql = "INSERT INTO `app_sequence_id` (`name`) VALUES ('$name')";
            try {
                $em->getConnection()->exec($sql);
            } catch (DBALException $e) {
            }
            return $em->getConnection()->lastInsertId('app_sequence_id');
        }
    }

 /*
    CREATE TABLE `app_sequence_id` (
     `id` INT(11) NOT NULL AUTO_INCREMENT,
     `name` VARCHAR(128) NOT NULL,
     PRIMARY KEY (`id`)
 )
 COLLATE='latin1_general_ci'
 ENGINE=InnoDB
 AUTO_INCREMENT=100
 ;
 */