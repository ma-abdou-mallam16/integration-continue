<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

class Product
{
    const FOOD_PRODUCT = 'food';
    private $name;
    private $type;
    private $price;

    public function __construct($name, $type, $price)
    {
        $this->name = $name;
        $this->type = $type;
        $this->price = $price;
    }

    public function calculeTva()
    {
        if (self::FOOD_PRODUCT == $this->type) {
            return $this->price * 0.055;
        }

        return $this->price * 0.196;
    }
}
