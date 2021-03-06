<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Entity\UserBreak;
use App\Form\UserType;
use App\Form\UserPasswordType;
use DateInterval;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


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
        $passForm = $this->createForm(UserPasswordType::class, $user);
        $form->handleRequest($request);
        $passForm->handleRequest($request);
        $success = array();
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getDoctrine()
                ->getRepository(User::class)
                ->findOneBy(
                    [
                        "id" => $id
                    ]
                );
            $user->setUsername($form->get('username')->getData());
            $user->setEmail($form->get('email')->getData());
            $user->setFirstname($form->get('firstname')->getData());
            $user->setLastname($form->get('lastname')->getData());

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();
            array_push($success, "L'utilisateur est édité avec succès !");
        }
        if($passForm->isSubmitted() && $passForm->isValid())
        {
            $user = $this->getDoctrine()
                ->getRepository(User::class)
                ->findOneBy(
                    [
                        "id" => $id
                    ]
                );
            if($passForm->get('password')->getData() != null && $passForm->get('password')->getData() != "")
            {
                $user->setPassword(
                    $passwordEncoder->encodePassword(
                        $user,
                        $passForm->get('password')->getData()
                    )
                );
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($user);
                $entityManager->flush();
            }
            array_push($success, "Le mot de passe est edité avec succès !");
        }
        return $this->render('admin/staff_editor.html.twig',[
            "form" => $form->createView(),
            "passForm" => $passForm->createView(),
            "success" => $success
        ]);
    }

}