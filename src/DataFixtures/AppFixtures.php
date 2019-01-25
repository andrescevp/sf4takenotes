<?php

namespace App\DataFixtures;

use App\Entity\Note;
use App\Entity\Tag;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $nNotes = 30;
        $faker = Factory::create();

        for ($i = 0; $i <= $nNotes; $i++) {
            $note = new Note();
            $note->setTitle($faker->name);
            $note->setBody($faker->text(100));
            $note->setBackgroundColor(str_replace('#', '', $faker->safeHexColor));

            $tags = new ArrayCollection();
            for ($t = 0; $t < 4; $t++) {
                $tag = new Tag();
                $tag->setName($faker->company);
                $tags->add($tag);
            }

            $note->setTags($tags);

            $manager->persist($note);
        }


        $manager->flush();
    }
}
