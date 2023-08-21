<?php

namespace App\Fixtures\Providers;

class CategorieProvider
{
    public function generateTagTitle(): string
    {
        $tags = [  // il faut en mettre au moins 10 si on veut en générer 10 aléatoirement
            'Symfony',
            'Api Rest',
            'Php',
            'Frontend',
            'Backend',
            'FullStack',
            'VueJs',
            'Rust',
            'Javascript',
            'Sass',
            'HTML',
            'CSS'
        ];

        // return $tags[0]
        // le array_rand renvoie un index aléatoire du tableau $tags
        return $tags[array_rand($tags)];
    }
}