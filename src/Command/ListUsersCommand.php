<?php namespace App\Command;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\{InputInterface, InputOption};
use Symfony\Component\Console\Output\{BufferedOutput, OutputInterface};
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Mailer\{Exception\TransportExceptionInterface, MailerInterface};
use Symfony\Component\Mime\Email;

#[AsCommand(name: 'app:list-users')]
class ListUsersCommand extends Command
{
    private MailerInterface $mailer;
    private $emailSender;
    private UserRepository $users;

    public function __construct(MailerInterface $mailer, $emailSender, UserRepository $users)
    {
        parent::__construct();

        $this->mailer = $mailer;
        $this->emailSender = $emailSender;
        $this->users = $users;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $this
            ->setDescription('Lists all the existing users')
            ->setHelp(<<<'HELP'
The <info>%command.name%</info> command lists all the users registered in the application:

  <info>php %command.full_name%</info>

By default the command only displays the 50 most recent users. Set the number of
results to display with the <comment>--max-results</comment> option:

  <info>php %command.full_name%</info> <comment>--max-results=2000</comment>

In addition to displaying the user list, you can also send this information to
the email address specified in the <comment>--send-to</comment> option:

  <info>php %command.full_name%</info> <comment>--send-to=manz.bud@gmail.com</comment>

HELP
            )
            // commands can optionally define arguments and/or options (mandatory and optional)
            // see https://symfony.com/doc/current/components/console/console_arguments.html
            ->addOption('max-results', null, InputOption::VALUE_OPTIONAL, 'Limits the number of users listed', 50)
            ->addOption('send-to', null, InputOption::VALUE_OPTIONAL, 'If set, the result is sent to the given email address')
        ;
    }

	/**
	 * This method is executed after initialize(). It usually contains the logic
	 * to execute to complete this command task.
	 *
	 * @param InputInterface $input
	 * @param OutputInterface $output
	 *
	 * @return int
	 * @throws TransportExceptionInterface
	 */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $maxResults = $input->getOption('max-results');
        // Use ->findBy() instead of ->findAll() to allow result sorting and limiting
        $allUsers = $this->users->findBy([], ['id' => 'DESC'], $maxResults);

        // Doctrine query returns an array of objects and we need an array of plain arrays
        $usersAsPlainArrays = array_map(static function (User $user) {
            return [
                $user->getId(),
                $user->getFullName(),
                $user->getUsername(),
                $user->getEmail(),
                implode(', ', $user->getRoles()),
            ];
        }, $allUsers);

        // In your console commands you should always use the regular output type,
        // which outputs contents directly in the console window. However, this
        // command uses the BufferedOutput type instead, to be able to get the output
        // contents before displaying them. This is needed because the command allows
        // to send the list of users via email with the '--send-to' option
        $bufferedOutput = new BufferedOutput();
        $io = new SymfonyStyle($input, $bufferedOutput);
        $io->table(
            ['ID', 'Full Name', 'Username', 'Email', 'Roles'],
            $usersAsPlainArrays
        );

        // instead of just displaying the table of users, store its contents in a variable
        $usersAsATable = $bufferedOutput->fetch();
        $output->write($usersAsATable);

        if (null !== $email = $input->getOption('send-to')) {
            $this->sendReport($usersAsATable, $email);
        }

        return 0;
    }

	/**
	 * Sends the given $contents to the $recipient email address.
	 * @param string $contents
	 * @param string $recipient
	 * @throws TransportExceptionInterface
*/
    private function sendReport(string $contents, string $recipient): void
    {
        $email = (new Email())
            ->from($this->emailSender)
            ->to($recipient)
            ->subject(sprintf('app:list-users report (%s)', date('Y-m-d H:i:s')))
            ->text($contents);

        $this->mailer->send($email);
    }
}
