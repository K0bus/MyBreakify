<?php

namespace App\Controller\User;

use App\Entity\User;
use App\Entity\UserBreak;
use App\Entity\UserRecovery;
use App\Form\RecoveryType;
use App\Form\UserBreakType;
use DateInterval;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints\Date;

class UserRecoveryController extends AbstractController
{
    /**
     * @Route("/recovery", name="app_recovery")
     */
    public function home(Request $request, UserInterface $user): Response
    {
        $entityManager = $this->getDoctrine()->getManager();

        $recovery = new UserRecovery();

        $form = $this->createForm(RecoveryType::class, $recovery);

        $errors = array();

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $recovery = $form->getData();
            $recovery->setUserId($user);
            $recovery->setStatus(0);
            $recovery->setRequestAt(new DateTime());
            if($recovery->getTimefrom() > $recovery->getTimeto())
            {
                $start = $recovery->getTimeto();
                $end = $recovery->getTimefrom();

                $recovery->setTimefrom($start);
                $recovery->setTimeto($end);
            }
            $entityManager->persist($recovery);
            $entityManager->flush();
        }

        $recoveries = $this->getDoctrine()
            ->getRepository(UserRecovery::class)
            ->findBy([
                'user_id' => $user
            ]);

        return $this->render('user/recovery.html.twig', [
            "form" => $form->createView(),
            "recoveries" => $recoveries
        ]);
    }
    /**
     * @Route("/recovery/remove/{id<\d+>?1}", name="app_recovery_remove", requirements={"id"="\d+"})
     */
    public function break_remove(int $id, Request $request, UserInterface $user): Response
    {
        $recovery = $this->getDoctrine()
            ->getRepository(UserRecovery::class)
            ->find($id);
        if($user == $recovery->getUserId())
        {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($recovery);
            $entityManager->flush();
        }
        return $this->redirectToRoute("app_recovery");
    }
}