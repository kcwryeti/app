<?php

namespace AppBundle\Controller;

use AppBundle\Form\UserPasswordForm;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserController extends Controller
{
    /**
     * @Route("/account", name="user_account_page")
     */
    public function indexAction()
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();
        return $this->render('user/user_account.html.twig',[
            'user' => $user,
        ]);
    }

    /**
     * @Route("/userpass", name="user_password_change")
     */
    public function changePasswordAction(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $user = $this->getUser();

        $form = $this->createForm(UserPasswordForm::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $data = $form->getData();

            if ($passwordEncoder->isPasswordValid($user,$data['password'])){
                $user->setPlainPassword($data['plainPassword']);
                $em = $this->getDoctrine()->getManager()->flush();
                return $this->redirectToRoute('user_account_page');
            }

            $form->get('password')->addError(new FormError('Nieprawidłowe hasło'));
        }

        return $this->render('user/password.reset.html.twig',[
            'form' => $form->createView(),
        ]);
    }
}
