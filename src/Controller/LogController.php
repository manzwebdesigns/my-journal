<?php namespace App\Controller;

use Doctrine\Persistence\ManagerRegistry;
use Exception;
use App\Entity\{Log, User};
use App\Form\Type\LogType;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{Request, Response};
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Log Controller
 */
class LogController extends AbstractController {
    /**
     * @param ManagerRegistry $doctrine
     *
     * @return Response
     */
    #[Route("/log", name: "log")]
	public function index( ManagerRegistry $doctrine ): Response {
		$this->denyAccessUnlessGranted( 'IS_AUTHENTICATED_FULLY' );

		/** @var User $user */
		$user    = $this->getUser();
		$user_id = $user->getId();
		$log     = $doctrine->getRepository( Log::class )
		                ->findBy( [ 'user_id' => $user_id ], [ 'log_date' => 'DESC' ] );

		return $this->render( 'log/index.html.twig', [ 'log' => $log, 'user' => $user ] );
	}

    /**
     * @param ManagerRegistry $doctrine
     * @param ValidatorInterface $validator
     * @param Request $request
     *
     * @return Response
     */
    #[Route("/log/new", name: "new_log")]
	public function new( ManagerRegistry $doctrine, ValidatorInterface $validator, Request $request ): Response {
		$this->denyAccessUnlessGranted( 'IS_AUTHENTICATED_FULLY' );

		$log = new Log();
		/** @var User $user */
		$user = $this->getUser();
		$log->setUserId( $user->getId() )
		    ->setLogDate( new DateTime() )
			->setPublishedAt( new DateTime());

		$form = $this->createForm( LogType::class, $log );

		$form->handleRequest( $request );
		if ( $form->isSubmitted() && $form->isValid() ) {
			$log = $form->getData();
			$entityManager = $doctrine->getManager();
			$entityManager->persist( $log );
			$entityManager->flush();

			$errors = $validator->validate( $log );
			if ( count( $errors ) > 0 ) {
				return new Response( (string) $errors, 400 );
			}

			return $this->redirectToRoute( 'log' );
		}

		return $this->render( 'log/new.html.twig', [
			'log'  => $log,
			'form' => $form->createView(),
		] );
	}

    /**
     * @param Log $log
     * @param ManagerRegistry $doctrine
     * @param Request $request
     *
     * @return Response
     */
    #[Route("/log/edit/{id}", name: "edit_log")]
	public function update(Log $log, ManagerRegistry $doctrine, Request $request): Response
	{
        $form = $this->createForm( LogType::class, $log );

		$form->handleRequest( $request );
		if ( $form->isSubmitted() && $form->isValid() ) {
			$log = $form->getData();
			$entityManager = $doctrine->getManager();
			$entityManager->persist( $log );
			$entityManager->flush();

			return $this->redirectToRoute('log');
		}

		return $this->render( 'log/edit.html.twig', [
		'log'  => $log,
		'form' => $form->createView(),
		] );
	}

    /**
     * @param Log $log
     * @param ManagerRegistry $doctrine
     *
     * @return Response
     */
    #[Route("/log/delete/{id}", name: "delete_log")]
    public function delete(Log $log, ManagerRegistry $doctrine): Response
    {
        try {
            $entityManager = $doctrine->getManager();

            $entityManager->remove($log);
            $entityManager->flush();

            $this->addFlash(
                'success',
                'Successfully deleted your record dated ' . $log->getLogDate()->format('Y-m-d')
            );
        } catch (Exception $e) {
            $this->addFlash(
                'danger',
                'Attempt to delete your record dated ' . $log->getLogDate()->format('Y-m-d') .
                ' failed with error:\r\n' . $e->getMessage()
            );
        }

        return $this->redirectToRoute('log');
    }
}
