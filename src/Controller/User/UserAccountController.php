<?php

namespace App\Controller\User;

use App\Entity\UserBreak;
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

class UserAccountController extends AbstractController
{
    /**
     * @Route("/account", name="app_account")
     */
    public function home(Request $request, UserInterface $user, UserPasswordEncoderInterface $passwordEncoder): Response
    {

        $passForm = $this->createForm(UserPasswordType::class, $user);
        $passForm->handleRequest($request);
        $success = array();
        $errors = array();
        if($passForm->isSubmitted() && $passForm->isValid())
        {
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
            array_push($success, "Votre mot de passe a été modifié avec succès !");
        }
        elseif ($passForm->isSubmitted()) {
            $errors = $passForm->getErrors();
        }
        return $this->render('user/account.html.twig', [
            "passForm" => $passForm->createView(),
            "success" => $success,
            "errors" => $errors
        ]);
    }
}
