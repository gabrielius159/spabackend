<?php declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\BlogPost;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $blogPost = new BlogPost();
        $blogPost->setTitle('Test title 1');
        $blogPost->setAuthor('Tester 1');
        $blogPost->setContent('Dummy data 1');
        $blogPost->setPublished(new \DateTime('now'));
        $blogPost->setSlug('test-title-one');

        $manager->persist($blogPost);

        $blogPost = new BlogPost();
        $blogPost->setTitle('Test title 2');
        $blogPost->setAuthor('Tester 2');
        $blogPost->setContent('Dummy data 2');
        $blogPost->setPublished(new \DateTime('now'));
        $blogPost->setSlug('test-title-two');

        $manager->persist($blogPost);
        $manager->flush();
    }
}
