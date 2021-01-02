<?php

namespace App\Services;

use Doctrine\ORM\EntityManagerInterface;

class Pagination{
    private $entityClass;
    private $limit = 10;
    private $currentPage = 1;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    public function getData(){
        //Calculer l'offset
        $start = $this->currentPage * $this->limit - $this->limit;

        //Récupéré les informations dans la base données
        $repo = $this->manager->getRepository($this->entityClass);
        $data = $repo->findBy([],[], $this->limit, $start);

        //renvoyer les données
        return $data;

    }
    public function getPages(){
        //Connaitre le total des enregistrements
        $repo = $this->manager->getRepository($this->getEntityClass());
        $total = count($repo->findAll());

        //Calculer le nombre de pages en fonction du nombre d'annonce
        $pages = ceil($total / $this->limit);//si on a 20 et une limit de 5 en aura 20/5 = 4 page

        return $pages;

    }

    public function getPage(){
        return $this->currentPage;
    }
    public function setPage($page){
        $this->currentPage = $page;

        return $this;
    }

    
    public function setEntityClass($entityClass){
        $this->entityClass = $entityClass;

        return $this;
    }

    public function getEntityClass(){
        return $this->entityClass;
    }

    public function setLimit($limit){
        $this->limit = $limit;

        return $this;
    }

    public function getLimit(){
        return $this->limit;
    }
    
}