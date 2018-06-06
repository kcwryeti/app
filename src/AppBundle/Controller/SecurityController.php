<?php

namespace AppBundle\Controller;

use AppBundle\Form\LoginForm;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends Controller
{
    /**
     * @Route("/login", name="login_page")
     */
    public function indexAction(AuthenticationUtils $authenticationUtils)
    {
        $lastUser = $authenticationUtils->getLastUsername();

        $error = $authenticationUtils->getLastAuthenticationError();

        $form = $this->createForm(LoginForm::class,[
            'username' => $lastUser,
        ]);

        return $this->render('security/login.html.twig', [
            'error' => $error,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/logout", name="logout")
     */
    public function logoutAction()
    {

    }
}
