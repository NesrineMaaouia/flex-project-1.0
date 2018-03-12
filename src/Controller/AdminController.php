<?php

namespace App\Controller;


use App\Entity\User;
use App\Form\EditUserType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


class AdminController extends Controller
{
    /**
     * @Route("/admin", name="admin_dashboard")
     */
    public function dashboardAction()
    {
        return $this->render('admin/dashboard.html.twig');
    }
    /**
     * @Route("/admin/users", name="admin_users")
     */
    public function usersAction()
    {
        $users = $this->getDoctrine()
            ->getRepository(User::class)
            ->findAll();
        return $this->render('admin/users.html.twig',array('users'=>$users));
    }


    /**
     * @Route("/admin/user/{id}/edit", name="admin_user_edit")
     */
    public function editUserAction(Request $request, User $user, UserPasswordEncoderInterface $passwordEncoder){

        $form = $this->createForm(EditUserType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() and $form->isValid()) {
            /** @var Participation $participation */
            $user = $form->getData();
            $password = $passwordEncoder->encodePassword($user, $user->getPassword());
            $user->setPassword($password);

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

        }

        return $this->render('admin/edit_user.html.twig', array(
            'form' => $form->createView(),
            'user' => $user
        ));

    }

}
