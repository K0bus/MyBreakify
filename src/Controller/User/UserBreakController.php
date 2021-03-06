<?php

namespace App\Controller\User;

use App\Entity\UserBreak;
use App\Entity\TimeParam;
use App\Form\UserBreakType;
use DateInterval;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints\Date;

class UserBreakController extends AbstractController
{
    /**
     * @Route("/break", name="app_break")
     */
    public function break(Request $request, UserInterface $user): Response
    {
        $entityManager = $this->getDoctrine()->getManager();

        $userBreak = new UserBreak();
        $startDate = new DateTime("09:00");
        $endDate = new DateTime("19:30");
        $minutes_to_add = 10;
        $time = array();
        $time_blacklist = array();
        $temp = $startDate;

        $errors = array();

        $breaks = $this->getDoctrine()
            ->getRepository(UserBreak::class)
            ->findBy([
                "user_id" => $user,
            ]);
        foreach ($breaks as $key => $break) {
            array_push($time_blacklist, $break->getTime()->format("H:i"));
        }
        while ($temp <= $endDate)
        {
            //TODO : Get hour
            $now = new DateTime("now");
            $now->sub(new DateInterval('PT5M'));

            $breaks = $this->getDoctrine()
            ->getRepository(UserBreak::class)
            ->findBy([
                "time" => $temp,
                "date" => new DateTime(),
            ]);

            $time_param = $this->getDoctrine()
            ->getRepository(TimeParam::class)
            ->findOneBy([
                "time" => $temp,
                "date" => new DateTime(),
            ]);

            if($time_param != null)
                if($now < $temp && !in_array($temp->format("H:i"), $time_blacklist) && count($breaks) < $time_param->getBreak())
                    $time[$temp->format("H:i")] = new DateTime($temp->format("H:i"));
            $temp->add(new DateInterval('PT' . $minutes_to_add . 'M'));
        }
        $form = $this->createForm(UserBreakType::class, $userBreak, [
            'time_list' => $time
        ]);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $userBreak = $form->getData();
            $userBreak->setUserId($user);
            $userBreak->setDate(new DateTime());
            $userBreak->setRequestedAt(new DateTime());
            
            $entityManager->persist($userBreak);
            $entityManager->flush();
            unset($time[$userBreak->getTime()->format("H:i")]);
            $form = $this->createForm(UserBreakType::class, $userBreak, [
                'time_list' => $time
            ]);
        }
        $breaks = $this->getDoctrine()
            ->getRepository(UserBreak::class)
            ->findBy([
                "user_id" => $user,
                "date" => new DateTime()
            ]);
        return $this->render('user/break.html.twig', [
            "form" => $form->createView(),
            "breaks" => $breaks,
            "errors" => $errors,
        ]);
    }
    /**
     * @Route("/break/remove/{id<\d+>?1}", name="app_break_remove", requirements={"id"="\d+"})
     */
    public function break_remove(int $id, Request $request, UserInterface $user): Response
    {
        $userBreak = $this->getDoctrine()
            ->getRepository(UserBreak::class)
            ->find($id);
        if($user == $userBreak->getUserId())
        {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($userBreak);
            $entityManager->flush();
        }
        return $this->redirectToRoute("app_break");
    }
}