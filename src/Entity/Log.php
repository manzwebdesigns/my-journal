<?php namespace App\Entity;

use Ambta\DoctrineEncryptBundle\Configuration\Encrypted;
use App\Repository\LogRepository;
use DateTime;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=LogRepository::class)
 * @ORM\Table(name="log")
 */
class Log
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private ?int $id;

	/**
	 * @Assert\NotBlank()
	 * @ORM\Column(type="date", nullable=false)
	 */
	private DateTime $log_date;

	/**
	 * @Assert\NotBlank()
	 * @Encrypted
	 * @ORM\Column(type="text", nullable=false)
	 */
	private string $log_message;

	/**
	 * @Assert\NotBlank()
	 * @ORM\Column(type="integer", nullable=false)
	 */
    private int $user_id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $publishedAt;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return DateTime
     */
    public function getLogDate(): DateTime
    {
        return $this->log_date;
    }

    /**
     * @param mixed $log_date
     *
     * @return Log
     */
    public function setLogDate($log_date): Log
    {
        $this->log_date = $log_date;

        return $this;
    }

    /**
     * @return string
     */
    public function getLogMessage(): string
    {
        return $this->log_message;
    }

    /**
     * @param string $log_message
     *
     * @return Log
     */
    public function setLogMessage(string $log_message): Log
    {
        $this->log_message = $log_message;

        return $this;
    }

    public function getUserId(): int
    {
        return $this->user_id;
    }

    public function setUserId(int $user_id): self
    {
        $this->user_id = $user_id;

        return $this;
    }

    public function getPublishedAt(): ?DateTimeInterface
    {
        return $this->publishedAt;
    }

    public function setPublishedAt( DateTimeInterface $publishedAt): self
    {
        $this->publishedAt = $publishedAt;

        return $this;
    }
}
