<?php namespace App\Controller;

use App\Entity\User;
use App\Form\Type\{ChangePasswordType, UserType};
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{Request, Response};
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * Controller used to manage current user.
 *
 * @IsGranted("ROLE_USER")
 *
 * @author Romain Monteil <monteil.romain@gmail.com>
 */
#[Route("/profile")]
class UserController extends AbstractController
{
    /**
     * @param Request $request
     * @param ManagerRegistry $doctrine
     *
     * @return Response
     */
    #[Route("/edit", name: "user_edit", methods: "GET|POST")]
    public function edit(Request $request, ManagerRegistry $doctrine): Response
    {
        $user = $this->getUser();

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $doctrine->getManager()->flush();

            $this->addFlash('success', 'user.updated_successfully');

            return $this->redirectToRoute('user_edit');
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param Request $request
     * @param ManagerRegistry $doctrine
     * @param UserPasswordHasherInterface $encoder
     *
     * @return Response
     */
    #[Route("/change-password", name: "user_change_password", methods: "GET|POST")]
    public function changePassword(Request $request, ManagerRegistry $doctrine, UserPasswordHasherInterface $encoder): Response
    {
	    /** @var User $user */
	    $user = $this->getUser();

        $form = $this->createForm(ChangePasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword($encoder->hashPassword($user, $form->get('newPassword')->getData()));

            $doctrine->getManager()->flush();

            return $this->redirectToRoute('security_logout');
        }

        return $this->render('user/change_password.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
