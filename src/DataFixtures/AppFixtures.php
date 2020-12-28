<?php namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

/**
 * Class AppFixtures
 * @package App\DataFixtures
 */
class AppFixtures extends Fixture
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private UserPasswordEncoderInterface $passwordEncoder;
    /**
     * @var SluggerInterface
     */
    private SluggerInterface $slugger;

    /**
     * AppFixtures constructor.
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param SluggerInterface $slugger
     */
    public function __construct(UserPasswordEncoderInterface $passwordEncoder, SluggerInterface $slugger)
    {
        $this->passwordEncoder = $passwordEncoder;
        $this->slugger = $slugger;
    }

    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager): void
    {
        $this->loadUsers($manager);
    }

    /**
     * @param ObjectManager $manager
     */
    private function loadUsers(ObjectManager $manager): void
    {
        foreach ($this->getUserData() as [$fullname, $username, $password, $email, $roles]) {
            $user = new User();
            $user->setFullName($fullname);
            $user->setUsername($username);
            $user->setPassword($this->passwordEncoder->encodePassword($user, $password));
            $user->setEmail($email);
            $user->setRoles($roles);

            $manager->persist($user);
            $this->addReference($username, $user);
        }

        $manager->flush();
    }

    /**
     * @return array[]
     */
    private function getUserData(): array
    {
        return [
            // $userData = [$fullname, $username, $password, $email, $roles];
            ['Bud Manz', 'bud', 'not4me', 'manz.bud@gmail.com', ['ROLE_ADMIN']],
        ];
    }
}
