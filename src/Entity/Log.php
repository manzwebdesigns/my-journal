<?php namespace App\Entity;

use App\Repository\LogRepository;
use DateTime;
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
    private $id;

	/**
	 * @Assert\NotBlank()
	 * @ORM\Column(type="date", nullable=false)
	 */
	private $log_date;

	/**
	 * @Assert\NotBlank()
	 * @ORM\Column(type="text", nullable=false)
	 */
	private $log_message;

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
	public function getLogDate(): DateTime {
		return $this->log_date;
	}

	/**
	 * @param mixed $log_date
	 *
	 * @return Log
	 */
	public function setLogDate( $log_date ): Log {
		$this->log_date = $log_date;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getLogMessage(): string {
		return $this->log_message;
	}

	/**
	 * @param string $log_message
	 *
	 * @return Log
	 */
	public function setLogMessage( string $log_message ): Log {
		$this->log_message = $log_message;

		return $this;
	}

}
