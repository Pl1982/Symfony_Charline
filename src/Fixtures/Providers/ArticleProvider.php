<?php

namespace App\Fixtures\Providers;

use App\Entity\ArticleImage;
use Faker\Factory;
use Faker\Generator;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ArticleProvider
{
    public function __construct(
        private Generator $faker
    ) {
        $this->faker = Factory::create('fr_FR');
    }

    public function generateArticleContent(): string
    {
        // file_get_contents() : permet de lire tout un fichier et le mettre dans une chaine de caractères
        return file_get_contents('https://loripsum.net/api/10/long/headers/link/ul/dl');
    }

    public function generateArticleDate(): \DateTimeImmutable
    {
        // on converti un objet dattimemutable en datetimeimmutable
        return \DateTimeImmutable::createFromMutable($this->faker->dateTimeThisYear());
    }

    public function uploadArticleImage(): ArticleImage
    {
        // dirname -> recupère le dossier parent (chemin absolu)
        // __DIR__ -> récupère le dossier courant (chemin absolu)
        // /*.* -> n'importe quel nom de fichier avec n'importe quelle extension
        $files = glob(realpath(\dirname(__DIR__) . '/Images/Articles') . '/*.*' );
        // dd($files);

        // On choisit une image de manière aléatoire
        $file = new File($files[array_rand($files)]);
        // dd($file);

        $uploadFile = new UploadedFile($file, $file->getBaseName());
        // dd($uploadFile);

        return (new ArticleImage)
            ->setImage($uploadFile);
    }
}