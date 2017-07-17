<?php

namespace EntitiesPHP\Repository;

use Doctrine\ORM\ {
    EntityManager as Em,
    Tools\SchemaTool,
    Tools\Setup
};

class EntityManager {

    /**
     * Caminho para as entidades do modelo de persistência
     */
    private static $entidades = ['vendor\ablima\solicitacao'];

    /**
     * configurações de conexão. Coloque aqui os seus dados
     */
    private static $dbParams = ['driver' => 'mysqli',
        'user' => 'root',
        'password' => '',
        'dbname' => 'solicitacao',
        'charset' => 'utf8',
        'host' => 'localhost'
    ];

    /**
     * Modo de Desenvolvimento
     * Padrão: FALSE
     * Caso seja FALSE ele não permite o modo de criação ou alteração do 
     * banco de dados, por esse motivo a constante deve ser alterada para FALSE
     * quando o desenvolvimento estiver concluído e assim evitar a modificação
     * indevida.
     * 
     * @var Boolean 
     */
    private static $isDevMod = \TRUE;

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
     * setEntitiesPath
     * 
     * @param array $entidades
     * @return \self
     */
    public static function setEntitiesPath(array $entidades): self {
        self::$entidades;
        return self;
    }

    /**
     * setDbParams
     * 
     * @param array $dbParams
     * @return \self
     */
    public static function setDbParams(array $dbParams): self {
        self::$dbParams = $dbParams;
        return self;
    }

    /**
     * Retorna \Doctrine\ORM\EntityManager
     * 
     * @return Em
     */
    public static function get_instance() {

        if (self::$_entityManager === null) {
            //setando as configurações definidas anteriormente
            $config = Setup::createAnnotationMetadataConfiguration(self::$entidades, self::$isDevMod);
            $config->addCustomStringFunction('group_concat', 'Oro\ORM\Query\AST\Functions\String\GroupConcat');
            $config->addCustomNumericFunction('hour', 'Oro\ORM\Query\AST\Functions\SimpleFunction');
            $config->addCustomNumericFunction('timestampdiff', 'Oro\ORM\Query\AST\Functions\Numeric\TimestampDiff');
            $config->addCustomDatetimeFunction('date', 'Oro\ORM\Query\AST\Functions\SimpleFunction');
            //criando o Entity Manager com base nas configurações de dev e banco de dados
            self::$_entityManager = Em::create(self::$dbParams, $config);
        }

        return self::$_entityManager;
    }

    public static function updateSchema() {

        $em = self::get_instance();
        $tool = new SchemaTool($em);
        $entidades = self::getAllEntities();
        $classes = array();
        foreach ($entidades as $entidade) {
            $classes [] = $em->getClassMetadata($entidade);
        }
//        $tmpConnection = $em->getConnection();
//        $tmpConnection->getSchemaManager()->createDatabase('solicitacao');
        $tool->updateSchema($classes, 'force');
    }

    public static function getAllEntities() {
        $entities = array();
        $em = self::get_instance();
        $meta = $em->getMetadataFactory()->getAllMetadata();
        foreach ($meta as $m) {
            $entities[] = $m->getName();
        }
        return $entities;
    }

}
