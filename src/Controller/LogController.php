<?php namespace App\Controller;

use Exception;
use App\Entity\{Log, User};
use App\Form\Type\LogType;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{Request, Response};
use Symfony\Component\Validator\Validator\ValidatorInterface;

class LogController extends AbstractController {
	/**
	 * @Symfony\Component\Routing\Annotation\Route("/log", name="log")
	 */
	public function index(): Response {
		$this->denyAccessUnlessGranted( 'IS_AUTHENTICATED_FULLY' );

		/** @var User $user */
		$user    = $this->getUser();
		$user_id = $user->getId();
		$log     = $this->getDoctrine()
		                ->getRepository( Log::class )
		                ->findBy( [ 'user_id' => $user_id ], [ 'log_date' => 'DESC' ] );

		if ( ! $log ) {
			throw $this->createNotFoundException(
				'No logs found for user_id ' . $user_id
			);
		}

		return $this->render( 'log/index.html.twig', [ 'log' => $log, 'user' => $user ] );
	}

	/**
	 * @Symfony\Component\Routing\Annotation\Route("/log/new", name="new_log")
	 * @param ValidatorInterface $validator
	 * @param Request $request
	 *
	 * @return Response
	 */
	public function new( ValidatorInterface $validator, Request $request ): Response {
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
			$entityManager = $this->getDoctrine()->getManager();
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
	 * @Symfony\Component\Routing\Annotation\Route("/log/edit/{id}", name="edit_log")
	 * @param int $id
	 * @param Request $request
	 *
	 * @return Response
	 */
	public function update(int $id, Request $request): Response
	{
		$entityManager = $this->getDoctrine()->getManager();
		$log = $entityManager->getRepository(Log::class)->find($id);

		if (!$log) {
			throw $this->createNotFoundException(
				'No log found for id ' . $id
			);
		}
		$form = $this->createForm( LogType::class, $log );

		$form->handleRequest( $request );
		if ( $form->isSubmitted() && $form->isValid() ) {
			$log = $form->getData();
			$entityManager = $this->getDoctrine()->getManager();
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
     * @Symfony\Component\Routing\Annotation\Route("/log/delete/{id}", name="delete_log")
     * @param int $id
     * @return Response
     */
    public function delete(int $id): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $log = $entityManager->getRepository(Log::class)->find($id);

        if (null === $log) {
            $this->addFlash(
                'danger',
                'Sorry, I can\'t delete that record.  No such log with an ID of ' . $id
            );
        } else {

            try {
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
        }

        return $this->redirectToRoute('log');
    }
}
