<?php declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\BlogPost;
use App\Entity\Comment;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    const USER_REFERENCE_NAME = User::class.'_admin';
    const BLOG_POST_REFERENCE_NAME = BlogPost::class.'_';

    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    /**
     * @var \Faker\Factory
     */
    private $faker;

    /**
     * AppFixtures constructor.
     *
     * @param UserPasswordEncoderInterface $passwordEncoder
     */
    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
        $this->faker = \Faker\Factory::create();
    }

    /**
     * @param ObjectManager $manager
     *
     * @throws \Exception
     */
    public function load(ObjectManager $manager): void
    {
        $this->loadUsers($manager);
        $this->loadBlogPosts($manager);
        $this->loadComments($manager);
    }

    /**
     * @param ObjectManager $manager
     *
     * @throws \Exception
     */
    private function loadBlogPosts(ObjectManager $manager): void
    {
        /*** @var User $user */
        $user = $this->getReference(self::USER_REFERENCE_NAME);

        for ($i = 0; $i < 100; $i++) {
            $blogPost = new BlogPost();
            $blogPost->setTitle($this->faker->realText(30));
            $blogPost->setAuthor($user);
            $blogPost->setContent($this->faker->realText(200));
            $blogPost->setPublished($this->faker->dateTimeThisYear);
            $blogPost->setSlug($this->faker->slug);

            $this->addReference(self::BLOG_POST_REFERENCE_NAME.$i, $blogPost);

            $manager->persist($blogPost);
        }

        $manager->flush();
    }

    /**
     * @param ObjectManager $manager
     */
    private function loadComments(ObjectManager $manager): void
    {
        for ($i = 0; $i < 100; $i++) {
            for ($j = 0; $j < rand(1, 10); $j++) {
                $comment = new Comment();
                $comment->setContent($this->faker->realText());
                $comment->setPublished($this->faker->dateTimeThisYear);
                $comment->setAuthor($this->getReference(self::USER_REFERENCE_NAME));
                $comment->setBlogPost($this->getReference(self::BLOG_POST_REFERENCE_NAME.$i));

                $manager->persist($comment);
            }
        }

        $manager->flush();
    }

    /**
     * @param ObjectManager $manager
     */
    private function loadUsers(ObjectManager $manager): void
    {
        $user = new User();
        $user->setUsername('admin');
        $user->setEmail('admin@gmail.com');
        $user->setName('Admin');
        $user->setPassword($this->passwordEncoder->encodePassword($user, 'secret123'));

        $this->addReference(self::USER_REFERENCE_NAME, $user);

        $manager->persist($user);
        $manager->flush();
    }
}
