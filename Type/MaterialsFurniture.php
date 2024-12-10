<?php
/*
 *  Copyright 2023.  Baks.dev <admin@baks.dev>
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
 *  FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 *  AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 *  LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 *  OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 *  THE SOFTWARE.
 *
 *
 */

namespace BaksDev\Reference\Materials\Furniture\Type;

use BaksDev\Reference\Materials\Furniture\Type\Materials\MaterialsFurnitureInterface;

final class MaterialsFurniture
{
    public const TYPE = 'material_furniture_type';

    private ?MaterialsFurnitureInterface $material = null;


    public function __construct(MaterialsFurnitureInterface|self|string $material)
    {

        if(is_string($material) && class_exists($material))
        {
            $instance = new $material();

            if($instance instanceof MaterialsFurnitureInterface)
            {
                $this->material = $instance;
                return;
            }
        }

        if($material instanceof MaterialsFurnitureInterface)
        {
            $this->material = $material;
            return;
        }

        if($material instanceof self)
        {
            $this->material = $material->getMaterial();
            return;
        }

        /** @var MaterialsFurnitureInterface $declare */
        foreach(self::getDeclared() as $declare)
        {
            if($declare::equals($material))
            {
                $this->material = new $declare();
                return;
            }
        }

        //throw new InvalidArgumentException(sprintf('Not found Materials %s', $material));

    }


    public function __toString(): string
    {
        return $this->material->getValue();
    }


    /** Возвращает значение MaterialsFurnitureInterface */
    public function getMaterial(): ?MaterialsFurnitureInterface
    {
        return $this->material;
    }


    /** Возвращает значение ColorsInterface */
    public function getMaterialValue(): ?string
    {
        return $this->material?->getValue();
    }


    public static function cases(): array
    {
        $case = [];

        foreach(self::getDeclared() as $key => $declare)
        {
            /** @var MaterialsFurnitureInterface $declare */
            $class = new $declare();
            $case[$class::sort().$key] = new self($class);
        }

        ksort($case);

        return $case;
    }


    public static function getDeclared(): array
    {
        return array_filter(
            get_declared_classes(),
            static function($className) {
                return in_array(MaterialsFurnitureInterface::class, class_implements($className), true);
            }
        );
    }

    public function equals(mixed $status): bool
    {
        $status = new self($status);

        return $this->getMaterialValue() === $status->getMaterialValue();
    }

}
