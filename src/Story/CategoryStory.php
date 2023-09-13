<?php

namespace App\Story;

use App\Factory\CategoryFactory;
use Zenstruck\Foundry\Story;

final class CategoryStory extends Story
{
    public function build(): void
    {
        $categories = explode(PHP_EOL, file_get_contents(__DIR__.'/../../data/category.txt'));
        $this->addState('category_without_advertisement', CategoryFactory::createOne(['name' => $categories[0]]));

        for ($i = 1; $i < count($categories); ++$i) {
            $this->addToPool('categories', CategoryFactory::createOne(['name' => $categories[$i]]));
        }
    }
}
