<?php

namespace EntitiesPHP\Repository;

use Doctrine\ORM\ {
    EntityManager,
    Tools\Setup
};

class EntityManager {

    /**
     * Entity Manager
     * @var EntityManager 
     */
    private static $_entityManager = null;

    /**
     * Padrão Singleton + Factory
     */
    private function __construct() {
        
    }

    /**
     * Retorna \Doctrine\ORM\EntityManager
     * 
     * @return EntityManager
     */
    public static function get_instance() {

        if (self::$_entityManager === null) {
            //setando as configurações definidas anteriormente
            $config = Setup::createAnnotationMetadataConfiguration(self::$entidades, self::IS_DEV_MOD);
            $config->addCustomStringFunction('group_concat', 'Oro\ORM\Query\AST\Functions\String\GroupConcat');
            $config->addCustomNumericFunction('hour', 'Oro\ORM\Query\AST\Functions\SimpleFunction');
            $config->addCustomNumericFunction('timestampdiff', 'Oro\ORM\Query\AST\Functions\Numeric\TimestampDiff');
            $config->addCustomDatetimeFunction('date', 'Oro\ORM\Query\AST\Functions\SimpleFunction');
            $config->setProxyDir($_SERVER['DOCUMENT_ROOT'] . '/sys/core/Doctrine/Proxies');
            //criando o Entity Manager com base nas configurações de dev e banco de dados
            self::$_entityManager = EntityManager::create(self::$dbParams, $config);
        }

        return self::$_entityManager;
    }

}
