<?php

namespace AppBundle\Controller\Api;

use AppBundle\Entity\User;
use AppBundle\Form\UserForm;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class UserController
 * @package AppBundle\Controller\Api
 */
class UserController extends ApiBaseController
{
    /**
     * @Route("/adm/api/user")
     * @Method("POST")
     */
    public function newAction(Request $request)
    {

        $user = new User();

        $form = $this->createForm(UserForm::class, $user,[
            'csrf_protection' => false
        ]);
        $this->processForm($request,$form);

        if (!$form->isValid()) {
            $this->throwApiProblemValidationException($form);
        }

        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();

        $userURL = $this->generateUrl('api_user_show',[
           'username' => $user->getUsername()
        ]);
        $response = $this->createApiResponse($user,201);
        $response->headers->set('Location',$userURL);

        return $response;

    }

    /**
     * @Route("/adm/api/user/{username}", name="api_user_show")
     * @Method("GET")
     */
    public function showAction($username)
    {
        $user = $this->getDoctrine()
            ->getRepository(User::class)
            ->findOneBy(['username' => $username]);
        if (!$user) {
            throw $this->createNotFoundException(sprintf(
                'No programmer found with nickname "%s"',
                $username
            ));
        }

        $response = $this->createApiResponse($user,200);
        return $response;
    }

    /**
     * @Route("/adm/api/user")
     * @Method("GET")
     */
    public function listAction()
    {

        $users = $this->getDoctrine()
            ->getRepository(User::class)
            ->findAll();

        return $this->createApiResponse(['users' => $users ]);

    }


    /**
     * @Route("/adm/api/user/{username}")
     * @Method({"PUT","PATCH"})
     */
    public function updateAction($username, Request $request)
    {
        $user = $this->getDoctrine()
            ->getRepository(User::class)
            ->findOneBy(['username' => $username]);

        if (!$user) {
            throw $this->createNotFoundException(sprintf(
                'No programmer found with nickname "%s"',
                $username
            ));
        }

        $form = $this->createForm(UserForm::class, $user,[
            'is_edit' => true,
            'csrf_protection' => false
        ]);

        $this->processForm($request,$form);

        if (!$form->isValid()) {
            $this->throwApiProblemValidationException($form);
        }

        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();

        $response = $this->createApiResponse($user,200);
        return $response;
    }

    /**
     * @Route("/adm/api/user/{username}")
     * @Method("DELETE")
     */
    public function deleteAction($username)
    {
        $user = $this->getDoctrine()
            ->getRepository(User::class)
            ->findOneBy(['username' => $username]);

        if ($user) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($user);
            $em->flush();
        }

        return $this->createApiResponse(null,204);

    }

}
