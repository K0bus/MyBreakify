<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Entity\UserBreak;
use App\Form\AdminUserBreakType;
use App\Form\UserBreakType;
use DateInterval;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints\Date;

class AdminStaffController extends AbstractController
{
    /**
     * @Route("/admin/staff", name="app_admin_staff")
     */
    public function staff(): Response
    {
        $users = $this->getDoctrine()
        ->getRepository(User::class)
        ->findAll();
        
        return $this->render('admin/staff.html.twig',[
            "users" => $users
        ]);
    }
    /**
     * @Route("/admin/staff/edit/{id<\d+>?1}", name="app_admin_staff_editor", requirements={"id"="\d+"})
     */
    public function user_editor(int $id, Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $user = $this->getDoctrine()
        ->getRepository(User::class)
        ->findOneBy(
            [
                "id" => $id
            ]
        );
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            if($form->get('plainPassword') != null || $form->get('plainPassword') != "")
                $user->setPassword(
                    $passwordEncoder->encodePassword(
                        $user,
                        $form->get('plainPassword')->getData()
                    )
                );
            $user->setUsername($form->get('username'));
            $user->setEmail($form->get('email'));
            $user->setRoles(array($form->get('roles')));
            $user->setFirstname($form->get('firstname'));
            $user->setLastname($form->get('lastname'));

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();
        }
        return $this->render('admin/staff_editor.html.twig',[
            "form" => $form->createView()
        ]);
    }

}