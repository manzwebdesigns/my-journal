<?php namespace App\Command;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Console\Input\{InputArgument, InputInterface, InputOption};
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use \Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Stopwatch\Stopwatch;
use function Symfony\Component\String\u;
use \ParagonIE\Halite\KeyFactory;

/**
 * A console command that creates users and stores them in the database.
 *
 * To use this command, open a terminal window, enter into your project
 * directory and execute the following:
 *
 *     $ php bin/console app:add-user
 *
 * To output detailed information, increase the command verbosity:
 *
 *     $ php bin/console app:add-user -vv
 *
 * See https://symfony.com/doc/current/console.html
 *
 * We use the default services.yaml configuration, so command classes are registered as services.
 * See https://symfony.com/doc/current/console/commands_as_services.html
 *
 * @author Javier Eguiluz <javier.eguiluz@gmail.com>
 * @author Yonel Ceruto <yonelceruto@gmail.com>
 */
class CreateHaliteCommand extends Command
{
    // to make your command lazily loaded, configure the $defaultName static property,
    // so it will be instantiated only when the command is actually called.
	/**
	 * @var string
	 */
	protected static $defaultName = 'app:create-key';

    /**
     * @var SymfonyStyle
     */
	private SymfonyStyle $io;

    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $this
            ->setDescription('Creates a Halite key and stores it in the root folder of the site')
            ->setHelp($this->getCommandHelp())
            // commands can optionally define arguments and/or options (mandatory and optional)
            // see https://symfony.com/doc/current/components/console/console_arguments.html
        ;
    }

	/**
	 * This optional method is the first one executed for a command after configure()
	 * and is useful to initialize properties based on the input arguments and options.
	 *
	 * @param InputInterface $input
	 * @param OutputInterface $output
	 */
    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        // SymfonyStyle is an optional feature that Symfony provides so you can
        // apply a consistent look to the commands of your application.
        // See https://symfony.com/doc/current/console/style.html
        $this->io = new SymfonyStyle($input, $output);
    }

	/**
	 * This method is executed after initialize() and before execute(). Its purpose
	 * is to check if some of the options/arguments are missing and interactively
	 * ask the user for those values.
	 *
	 * This method is completely optional. If you are developing an internal console
	 * command, you probably should not implement this method because it requires
	 * quite a lot of work. However, if the command is meant to be used by external
	 * users, this method is a nice way to fall back and prevent errors.
	 * @param InputInterface $input
	 * @param OutputInterface $output
	 */
    protected function interact(InputInterface $input, OutputInterface $output): void
    {
        $this->io->title('Add Halite key Command Interactive Wizard');
        $this->io->text([
            'If you prefer to not use this interactive wizard, provide the',
            'arguments required by this command as follows:',
            '',
            ' $ php bin/console app:create-key',
            '',
            '',
        ]);
    }

	/**
	 * This method is executed after interact() and initialize(). It usually
	 * contains the logic to execute to complete this command task.
	 * @param InputInterface $input
	 * @param OutputInterface $output
	 * @return int
	 */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $stopwatch = new Stopwatch();
        $stopwatch->start('create-key-command');

        // Generate a new random encryption key:
        $encryptionKey = KeyFactory::generateEncryptionKey();

        // Saving a key to a file:
        KeyFactory::save($encryptionKey, './Halite.key');

        $this->io->success('The Halite key was successfully created!');

        $event = $stopwatch->stop('create-key-command');

        return 0;
    }

    /**
     * The command help is usually included in the configure() method, but when
     * it's too long, it's better to define a separate method to maintain the
     * code readability.
     */
    private function getCommandHelp(): string
    {
        return <<<'HELP'
The <info>%command.name%</info> command creates a Halite key and saves it in the file root

  <info>php %command.full_name%</info>

HELP;
    }
}
