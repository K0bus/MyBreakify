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
            $errors = $this->getErrorMessage($passForm);
        }
        $passForm->clearErrors(true);
        return $this->render('user/account.html.twig', [
            "passForm" => $passForm->createView(),
            "success" => $success,
            "errors" => $errors
        ]);
    }

    private function getErrorMessage(\Symfony\Component\Form\Form $form) {
        $errors = array();

        foreach ($form->getErrors() as $key => $error) {
            array_push($errors, $error->getMessage());
        }

        foreach ($form->all() as $child) {
            if(!$child->isValid())
                foreach ($this->getErrorMessage($child) as $key => $error) {
                    array_push($errors, $error);
                }
        }
        
        return $errors;
    }
}
