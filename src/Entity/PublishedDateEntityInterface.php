<?php


namespace App\Entity;


interface PublishedDateEntityInterface
{
    public function  setDate(\DateTimeInterface $date): PublishedDateEntityInterface;
}