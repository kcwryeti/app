<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class DefaultController
 * @package AppBundle\Controller
 *
 */
class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        return $this->render('default/index.html.twig', [
        ]);
    }

    /**
     * @Route("/create", name="create_users")
     */
    public function createAction()
    {
        for ($i = 10; $i<100;$i++) {
            $user = new User();
            $user->setUsername('test_user_'.$i);
            $user->setPlainPassword('password'.$i);
            $user->setEmail('tuser_'.$i.'@example.com');
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
        }
        return new Response('OK');
    }
}
