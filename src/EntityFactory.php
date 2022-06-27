<?php

namespace App;

class EntityFactory
{

    private static $_em;

    public static function setEntityManager(EntityManager $em)
    {
        self::$_em = $em;
    }

    public static function getEntity($entityName, $data): Entity
    {
        return  self::$_em->create($entityName, $data);
    }
}