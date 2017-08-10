<?php

namespace EntitiesPHP\Repository;

use Symfony\Component\VarDumper\VarDumper;
use \Doctrine\ORM\ORMInvalidArgumentException;

function save($entity): void {
    try {
        $em = EntityManager::get_instance();
        $em->persist($entity);
        $em->flush();
    } catch (ORMInvalidArgumentException $ex) {
        die($ex->getMessage());
    }
}

function remove($entity): void {
    try {
        $em = EntityManager::get_instance();
        $em->remove($entity);
        $em->flush();
    } catch (ORMInvalidArgumentException $ex) {
        die($ex->getMessage());
    }
}

function get(string $entity, int $id = 0) {
    $em = EntityManager::get_instance();

    if ($id !== 0) {
        $retorno = $em->getRepository($entity)->find($id);
    } else {
        $retorno = $em->getRepository($entity)->findAll();
    }
    return $retorno;
}

function dump($var) {
    VarDumper::dump($var);
}
