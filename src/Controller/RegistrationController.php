<?php namespace App\Controller;

use App\Entity\User;
use App\Form\Type\RegistrationFormType;
use App\Security\EmailVerifier;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{Request, Response};
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class RegistrationController
 * @package App\Controller
 */
class RegistrationController extends AbstractController
{
	/**
	 * @var EmailVerifier
	 */
	private $emailVerifier;

	/**
	 * RegistrationController constructor.
	 *
	 * @param EmailVerifier $emailVerifier
	 */
	public function __construct(EmailVerifier $emailVerifier)
    {
        $this->emailVerifier = $emailVerifier;
    }

    /**
     * @param Request $request
     * @param ManagerRegistry $doctrine
     * @param UserPasswordHasherInterface $passwordEncoder
     *
     * @return Response
     * @throws TransportExceptionInterface
     */
    #[Route("/register", name: "app_register")]
    public function register(Request $request, ManagerRegistry $doctrine, UserPasswordHasherInterface $passwordEncoder): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $passwordEncoder->hashPassword(
                    $user,
                    $form->get('password')->getData()
                )
            )
	            ->setUsername($form->get('username')->getData())
	            ->setFullName($form->get('fullName')->getData())
	            ->setEmail($form->get('email')->getData())
	            ->setIsVerified(false);

            $user->setRoles(['0' => $form->get('roles')->getData()]);

            $entityManager = $doctrine->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            // generate a signed url and email it to the user
            $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
                (new TemplatedEmail())
                    ->from(new Address('webmaster@my-journal.us', 'My Journal Mail Bot'))
                    ->to($user->getEmail())
                    ->subject('Please Confirm your Email')
                    ->htmlTemplate('registration/confirmation_email.html.twig')
            );

            $this->verifyUserEmail($request);
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

	/**
	 * @param Request $request
	 * @return Response
	 */
    #[Route("/verify/email", name: "app_verify_email")]
    public function verifyUserEmail(Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        // validate email confirmation link, sets User::isVerified=true and persists
        try {
            $this->emailVerifier->handleEmailConfirmation($request, $this->getUser());
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('verify_email_error', $exception->getReason());

            return $this->redirectToRoute('app_register');
        }

        $this->addFlash('success', 'Your email address has been verified.');

        return $this->redirectToRoute('homepage');
    }
}
