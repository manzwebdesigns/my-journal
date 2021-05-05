<?php namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Serializable;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @ORM\Table(name="user")
 *
 * Defines the properties of the User entity to represent the application users.
 * See https://symfony.com/doc/current/doctrine.html#creating-an-entity-class
 *
 * Tip: if you have an existing database, you can generate these entity class automatically.
 * See https://symfony.com/doc/current/doctrine/reverse_engineering.html
 *
 * @author Ryan Weaver <weaverryan@gmail.com>
 * @author Javier Eguiluz <javier.eguiluz@gmail.com>
 * @UniqueEntity(fields={"username"}, message="There is already an account with this username")
 */
class User implements UserInterface, Serializable {
	/**
	 * @var int
	 *
	 * @ORM\Id
	 * @ORM\GeneratedValue
	 * @ORM\Column(type="integer")
	 */
	private int $id;

	/**
	 * @var string
	 *
	 * @ORM\Column(type="string")
	 * @Assert\NotBlank()
	 */
	private string $fullName;

	/**
	 * @var string
	 *
	 * @ORM\Column(type="string", unique=true)
	 * @Assert\NotBlank()
	 * @Assert\Length(min=2, max=50)
	 */
	private string $username;

	/**
	 * @var string
	 *
	 * @ORM\Column(type="string", unique=true)
	 * @Assert\Email()
	 */
	private string $email;

	/**
	 * @var string
	 *
	 * @ORM\Column(type="string")
	 */
	private string $password;

	/**
	 * @var array
	 *
	 * @ORM\Column(type="json")
	 */
	private array $roles = [];

	/**
	 * @ORM\Column(type="boolean")
	 */
	private $isVerified = false;

	/**
	 * @return int|null
	 */
	public function getId(): ?int {
		return $this->id;
	}

	/**
	 * @param string $fullName
	 *
	 * @return $this
	 */
	public function setFullName( string $fullName ): self {
		$this->fullName = $fullName;

		return $this;
	}

	/**
	 * @return string|null
	 */
	public function getFullName(): ?string {
		return $this->fullName;
	}

	/**
	 * @return string|null
	 */
	public function getUsername(): ?string {
		return $this->username;
	}

	/**
	 * @param string $username
	 *
	 * @return $this
	 */
	public function setUsername( string $username ): self {
		$this->username = $username;

		return $this;
	}

	/**
	 * @return string|null
	 */
	public function getEmail(): ?string {
		return $this->email;
	}

	/**
	 * @param string $email

	 * @return $this
	 */
	public function setEmail( string $email ): self {
		$this->email = $email;

		return $this;
	}

	/**
	 * @return string|null
	 */
	public function getPassword(): ?string {
		return $this->password;
	}

	/**
	 * @param string $password
	 *
	 * @return $this
	 */
	public function setPassword( string $password ): self {
		$this->password = $password;

		return $this;
	}

	/**
	 * Returns the roles or permissions granted to the user for security.
	 */
	public function getRoles(): array {
		$r = $this->roles;

		// guarantees that a user always has at least one role for security
		if ( empty( $r ) ) {
			$roles[] = 'ROLE_USER';
		}

		return array_unique( $r );
	}

	/**
	 * @param array|null $roles
	 *
	 * @return $this
	 */
	public function setRoles( ?array $roles ): self {
		$this->roles = $roles ?? ['0' => 'ROLE_USER'];

		return $this;
	}

	/**
	 * Returns the salt that was originally used to encode the password.
	 *
	 * {@inheritdoc}
	 */
	public function getSalt(): ?string {
		// We're using bcrypt in security.yaml to encode the password, so
		// the salt value is built-in and and you don't have to generate one
		// See https://en.wikipedia.org/wiki/Bcrypt

		return null;
	}

	/**
	 * Removes sensitive data from the user.
	 *
	 * {@inheritdoc}
	 */
	public function eraseCredentials(): void {
		// if you had a plainPassword property, you'd nullify it here
		// $this->plainPassword = null;
	}

	/**
	 * {@inheritdoc}
	 */
	public function serialize(): string {
		// add $this->salt too if you don't use Bcrypt or Argon2i
		return serialize( [ $this->id, $this->username, $this->password ] );
	}

	/**
	 * {@inheritdoc}
	 */
	public function unserialize( $serialized ): void {
		// add $this->salt too if you don't use Bcrypt or Argon2i
		[ $this->id, $this->username, $this->password ] = unserialize( $serialized, [ 'allowed_classes' => false ] );
	}

	/**
	 * @return bool
	 */
	public function isVerified(): bool {
		return $this->isVerified;
	}

	/**
	 * @param bool $isVerified
	 *
	 * @return $this
	 */
	public function setIsVerified( bool $isVerified ): self {
		$this->isVerified = $isVerified;

		return $this;
	}
}
