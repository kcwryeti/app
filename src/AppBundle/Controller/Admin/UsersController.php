<?php
namespace AppBundle\Controller\Admin;

use AppBundle\Entity\User;
use AppBundle\Model\UserManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class UsersController extends Controller
{
    /**
     * @Route("/adm/users", name="admin_users_list")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function indexAction(Request $request)
    {
        $queryBuilder = $this->getDoctrine()->getRepository(User::class)
                    ->findAllQueryBuilder($request);

        $paginator = $this->get('knp_paginator');

        $users = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page',1),
            $request->query->getInt('limit',15)
        );

        return $this->render('admin/users_list.html.twig', [
            'users' => $users
        ]);
    }
}
