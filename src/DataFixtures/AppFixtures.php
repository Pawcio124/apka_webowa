<?php

namespace App\DataFixtures;

use App\Entity\Project;
use App\Entity\Task;
use App\Entity\User;
use App\Security\TokenGenerator;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    /**
     * @var \Faker\Factory
     */
    private $faker;

    private const USERS =[
        [
            'username'=> 'admin',
            'email'=>'admin@blog.com',
            'password'=>'secret123#',
            'roles' => [User::ROLE_ADMIN],
            'enabled'=> true
        ],

        [
            'username'=> 'sysia',
            'email'=>'sysia@blog.com',
            'password'=>'secret123#',
            'roles' => [User::ROLE_USER],
            'enabled'=> true
        ],

        [
            'username'=> 'sysia2',
            'email'=>'sysia2@blog.com',
            'password'=>'secret123#',
            'roles' => [User::ROLE_USER],
            'enabled'=> true
        ],
        [
            'username'=> 'sysiaE',
            'email'=>'sysiaE@blog.com',
            'password'=>'secret123#',
            'roles' => [User::ROLE_USER],
            'enabled'=> true
        ],
        [
            'username'=> 'sOLOhAN',
            'email'=>'sysia22@blog.com',
            'password'=>'secret123#',
            'roles' => [User::ROLE_USER],
            'enabled'=> false
        ],
        [
            'username'=> 'jUDIT',
            'email'=>'Judit@blog.com',
            'password'=>'secret123#',
            'roles' => [User::ROLE_USER],
            'enabled'=> true
        ]
    ];
    /**
     * @var TokenGenerator
     */
    private $tokenGenerator;

    public function __construct(
        UserPasswordEncoderInterface $passwordEncoder,
    TokenGenerator $tokenGenerator
    )
    {
        $this->passwordEncoder= $passwordEncoder;
        $this->faker = \Faker\Factory::create();
        $this->tokenGenerator = $tokenGenerator;
    }

    public function load(ObjectManager $manager)
    {
        $this->loadUsers($manager);
        $this->loadProjects($manager);
        $this->loadTasks($manager);
    }

    public function loadProjects(ObjectManager $manager)
    {
        $user = $this->getReference('user_admin');

        for ($i = 0; $i < 1; $i++)
        {
        $project = new Project();
        $authorReference = $this->getRandomUserReference($project);
        $project->setAuthor($authorReference);
        $project->setContent($this->faker->realText());
        $project->setSlug($this->faker->slug);
        $project->setTitle($this->faker->realText(30));
        $project->setDate($this->faker->dateTimeThisYear);
        $project->setDateEnd($this->faker->dateTimeThisYear);
        $project->setPriority(rand(1,5));

        $this->setReference("project_$i", $project);

        $manager->persist($project);
        }

        $manager->flush();
    }

    public function loadTasks(ObjectManager $manager)
    {
        for ($i = 0; $i < 1; $i++)
        {
            for($j = 0;$j<rand(1,10); $j++)
            {
                $task = new Task();

                $authorReference= $this->getRandomUserReference($task);

                $task->setAuthor($authorReference);
                $task->setContent($this->faker->realText(50));
                $task->setPlace($this->faker->realText(25));
                $task->setProject($this->getReference("project_$i"));
                $task->setDone('false');
                $task->setTaskPriority(rand(1,5));

                $manager->persist($task);
            }

            $manager->flush();
        }

    }

    public function loadUsers(ObjectManager $manager)
    {
        foreach (self::USERS as $userFixture){
            $user = new User();
            $user->setUsername($userFixture['username']);
            $user->setPassword($this->passwordEncoder->encodePassword(
                $user,
                $userFixture['password']
            ));
            $user->setEmail($userFixture['email']);
            $user->setRoles($userFixture['roles']);
            $user->setEnabled($userFixture['enabled']);

            if (!$userFixture['enabled']){
                $user->setConfirmationToken(
                    $this->tokenGenerator->getRandomSecureToken()
                );
            }

            $this->addReference('user_'.$userFixture['username'], $user);

            $manager->persist($user);

        }

        $manager->flush();
    }

    protected function getRandomUserReference($entity): User
    {
        $randomUser = self::USERS[rand(0,5)];

        if ($entity instanceof Project &&
            !count(array_intersect($randomUser['roles'],
                [User::ROLE_ADMIN, User::ROLE_USER]
            )))
        {
            return $this->getRandomUserReference($entity);
        }
        if ($entity instanceof Task &&
            !count(array_intersect($randomUser['roles'],
                [User::ROLE_ADMIN,User::ROLE_USER]
            )))
        {
            return $this->getRandomUserReference($entity);
        }

        return $this->getReference('user_'.$randomUser['username']);
    }
}
