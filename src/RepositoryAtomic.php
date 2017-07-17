<?php

namespace EntitiesPHP\Repository;

use Symfony\Component\VarDumper\VarDumper;

function save($entity): void {
    $em = EntityManager::get_instance();
    $em->persist($entity);
    $em->flush();
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

function dump($var){
    VarDumper::dump($var);
}
