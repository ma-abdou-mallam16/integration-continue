<?php

namespace App\test\Entity;

use App\Entity\Product;
use PHPUnit\Framework\TestCase;

class ProductTest extends TestCase
{
    public function testCalculeTVA1()
    {
        $product = new Product('Un produit', Product::FOOD_PRODUCT, 20);

        $this->assertSame(1.1, $product->calculeTVA());
    }

    public function testCalculeTV2()
    {
        $product = new Product('Un autre produit', 'Un autre type de produit', 20);

        $this->assertSame(3.92, $product->calculeTVA());
    }

    public function testNegativePriceComputeTVA()
    {
        $product = new Product('Un produit', Product::FOOD_PRODUCT, -20);

        $this->expectException('LogicException');

        $product->calculeTVA();
    }

    /**
     * @dataProvider pricesForFoodProduct
     */
    public function testcomputeTVAFoodProduct($price, $expectedTva)
    {
        $product = new Product('Un produit', Product::FOOD_PRODUCT, $price);

        $this->assertSame($expectedTva, $product->calculeTVA());
    }

    public function pricesForFoodProduct()
    {
        return [
            [0, 0.0],
            [20, 1.1],
            [100, 5.5]
        ];
    }
}
