<?php

namespace App\Form\DataTransformers;

use DateTime;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class FrenchToDateTimeTransformer implements DataTransformerInterface{
    public function transform($date)
    {
        if($date === null){
            return "";
        }
        return $date->format('d/m/Y');
    }

    public function reverseTransform($frenchDate)
    {
        //frencDate = 20/12/2020

        if($frenchDate === null){
            //exception
            throw new TransformationFailedException("Vous devez entrer une nouvelle date");
        }

        $date = DateTime::createFromFormat('d/m/Y', $frenchDate);

        if($date ===  null){
            //exception
            throw new TransformationFailedException("le format de la date n'est pas bon");
        }

        return $date;
    }
}