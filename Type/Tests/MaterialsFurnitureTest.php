<?php
/*
 *  Copyright 2025.  Baks.dev <admin@baks.dev>
 *  
 *  Permission is hereby granted, free of charge, to any person obtaining a copy
 *  of this software and associated documentation files (the "Software"), to deal
 *  in the Software without restriction, including without limitation the rights
 *  to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 *  copies of the Software, and to permit persons to whom the Software is furnished
 *  to do so, subject to the following conditions:
 *  
 *  The above copyright notice and this permission notice shall be included in all
 *  copies or substantial portions of the Software.
 *  
 *  THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 *  IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 *  FITNESS FOR A PARTICULAR PURPOSE AND NON INFRINGEMENT. IN NO EVENT SHALL THE
 *  AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 *  LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 *  OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 *  THE SOFTWARE.
 */

declare(strict_types=1);

namespace BaksDev\Reference\Materials\Furniture\Tests;

use BaksDev\Ozon\Products\Api\Settings\AttributeValues\OzonAttributeValueRequest;
use BaksDev\Ozon\Type\Authorization\OzonAuthorizationToken;
use BaksDev\Reference\Materials\Furniture\Type\Materials\MaterialsFurnitureCollection;
use BaksDev\Reference\Materials\Furniture\Type\Materials\MaterialsFurnitureInterface;
use BaksDev\Reference\Materials\Furniture\Type\MaterialsFurniture;
use BaksDev\Reference\Materials\Furniture\Type\MaterialsFurnitureType;
use BaksDev\Users\Profile\UserProfile\Type\Id\UserProfileUid;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\Attribute\When;

/**
 * @group reference-materials-furniture
 */
#[When(env: 'test')]
final class MaterialsFurnitureTest extends KernelTestCase
{
    private static OzonAuthorizationToken $Authorization;

    public static function setUpBeforeClass(): void
    {
        self::$Authorization = new OzonAuthorizationToken(
            new UserProfileUid(),
            $_SERVER['TEST_OZON_TOKEN'],
            $_SERVER['TEST_OZON_CLIENT'],
            $_SERVER['TEST_OZON_WAREHOUSE'],
        );
    }

    public function testUseCase(): void
    {
        /** @var MaterialsFurnitureCollection $materialsFurnitureCollection */
        $materialsFurnitureCollection = self::getContainer()->get(MaterialsFurnitureCollection::class);
        $cases = $materialsFurnitureCollection->cases();


        /** @var OzonAttributeValueRequest $ozonAttributeRequest */
        $ozonAttributeRequest = self::getContainer()->get(OzonAttributeValueRequest::class);
        $ozonAttributeRequest->TokenHttpClient(self::$Authorization);


        // 80731485 - Полки и стеллаж
        // 95499 - Стеллаж модульный
        // 6656 - Материал корпуса


        // 17027918 - Столы
        // 95015 - Стол обеденный
        // 6688 - Материал столешницы


        // 17027919 - Шкафы
        // 504866300 - Шкаф-купе
        // 6704 - Материал фасада
        // 6656 - Материал корпуса


        // 17027916 - Стулья, скамьи, табуреты, пуфики
        // 970709003 - Табурет-стремянка

        // 80375615 - Диваны и кресла
        // 95030 - Кресло

        // 6654 - Материал обивки
        // 6643 - Материал наполнителя ?
        // 6657 - Покрытие корпуса ?
        // 6656 - Материал корпуса


        $attribute = $ozonAttributeRequest->findAll(17027916, 970709003, 6656);


        if($attribute->valid())
        {

            foreach($attribute as $attributeValueDTO)
            {
                $isset = false;

                /** @var MaterialsFurnitureInterface $case */
                foreach($cases as $case)
                {
                    if($case::equals($attributeValueDTO->getValue()))
                    {
                        $isset = true;
                        break;
                    }
                }

                self::assertTrue($isset, message: sprintf('отсутствует элемент %s', $attributeValueDTO->getValue()));

            }

        }


        self::assertCount(128, $cases);

        /** @var MaterialsFurnitureInterface $case */
        foreach($cases as $case)
        {
            $Material = new MaterialsFurniture($case->getValue());

            self::assertTrue($Material->equals($case::class)); // немспейс интерфейса
            self::assertTrue($Material->equals($case)); // объект интерфейса
            self::assertTrue($Material->equals($case->getValue())); // строка
            self::assertTrue($Material->equals($Material)); // объект класса

            $MaterialsFurnitureType = new MaterialsFurnitureType();
            $platform = $this
                ->getMockBuilder(AbstractPlatform::class)
                ->getMock();

            $convertToDatabase = $MaterialsFurnitureType->convertToDatabaseValue($Material, $platform);
            self::assertEquals($Material->getMaterialValue(), $convertToDatabase);

            $convertToPHP = $MaterialsFurnitureType->convertToPHPValue($convertToDatabase, $platform);
            self::assertInstanceOf(MaterialsFurniture::class, $convertToPHP);
            self::assertEquals($case, $convertToPHP->getMaterial());
        }

        self::assertTrue(true);
    }
}
