<?php

namespace EntitiesPHP\Repository;

use Doctrine\ORM\{
    EntityManager as Em,
    Tools\SchemaTool,
    Tools\Setup
};
use Exception;

class EntityManager {

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
     * Settings
     * @var array 
     */
    private static $settings = [];

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
            self::initConfig();
            //setando as configurações definidas anteriormente
            $config = Setup::createAnnotationMetadataConfiguration(self::$settings['entidades'], self::$settings['isDevMod']);
            $config->addCustomStringFunction('group_concat', 'Oro\ORM\Query\AST\Functions\String\GroupConcat');
            $config->addCustomNumericFunction('hour', 'Oro\ORM\Query\AST\Functions\SimpleFunction');
            $config->addCustomNumericFunction('timestampdiff', 'Oro\ORM\Query\AST\Functions\Numeric\TimestampDiff');
            $config->addCustomDatetimeFunction('date', 'Oro\ORM\Query\AST\Functions\SimpleFunction');
            //criando o Entity Manager com base nas configurações de dev e banco de dados
            self::$_entityManager = Em::create(self::$settings['dbParams'], $config);
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
    
    public static function getRepository($entidade) {
        $em = self::get_instance();
        return $em->getRepository($entidade);
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

    private static function initConfig() {
        // Instantiate the app
        $pathSettings = __DIR__ . '/../../../../src/settings.php';
        try {
            if (!file_exists($pathSettings)) {
                throw new Exception("Arquivo de configuração não encontrado. Caminho procurado: {$pathSettings}.");
            }
            // Instantiate the app
            self::$settings = require $pathSettings;
            if (!isset(self::$settings['settings'])) {
                throw new Exception("Formato do arquivo de configurações não obedece os padrões. O array deve conter a chave settings.");
            } else {
                self::$settings = self::$settings['settings'];
                self::validConfig();
            }
//            dump(self::$settings);
        } catch (Exception $ex) {
            echo $ex->getMessage();
            exit;
        }
    }

    private static function validConfig() {

        if (!isset(self::$settings['entidades'])) {
            throw new Exception("Não existe a chave entidades no array de configurações. Essa chave é necessária para informar quais entidades serão mapeadas pelo ORM");
        }
        if (!isset(self::$settings['dbParams'])) {
            throw new Exception("Não existe a chave dbParams no array de configurações. Essa chave é necessária para informar os dados de conexão com o banco.");
        }
        if (!isset(self::$settings['isDevMod'])) {
            self::$settings['isDevMod'] = self::$isDevMod;
        }
    }

}
