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
        $time_list = array();
        $time_blacklist = array();
        $temp = $startDate;

        $errors = array();

        $breaks = $this->getDoctrine()
            ->getRepository(UserBreak::class)
            ->findBy([
                "user_id" => $user,
                "date" => new DateTime(),
            ]);
        foreach ($breaks as $key => $break) {
            array_push($time_blacklist, $break->getTime()->format("H:i"));
        }

        // Fetch time param / break taken by all users

        $breaks_glob = $this->getDoctrine()
        ->getRepository(UserBreak::class)
        ->findBy([
            "date" => new DateTime(),
        ]);

        $time_param = $this->getDoctrine()
        ->getRepository(TimeParam::class)
        ->findBy([
            "date" => new DateTime(),
        ]);

        $time_arr = array();

        foreach ($time_param as $key => $param) {
            $t = $param->getTime()->format("H:i");
            $time_arr[$t]["allowed"] = $param->getBreak();
        }
        foreach ($breaks_glob as $key => $break) {
            $t = $break->getTime()->format("H:i");
            if(isset($time_arr[$t]["taken"]))
                $time_arr[$t]["taken"]++;
            else
                $time_arr[$t]["taken"] = 1;
        }
        foreach ($time_arr as $key => $v) {
            $now = new DateTime("now");
            $now->sub(new DateInterval('PT5M'));
            $time = new DateTime($key);

            if($now > $time || in_array($time->format("H:i"), $time_blacklist))
            {
                $temp->add(new DateInterval('PT' . $minutes_to_add . 'M'));
                continue;
            }
            // Set default data
            if(!isset($v["taken"]))
                $v["taken"] = 0;
            if(!isset($v["allowed"]))
                $v["allowed"] = 0;

            if($v["taken"] < $v["allowed"])
                $time_list[$temp->format("H:i")] = new DateTime($temp->format("H:i"));
        }

        $form = $this->createForm(UserBreakType::class, $userBreak, [
            'time_list' => $time_list
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
            unset($time_list[$userBreak->getTime()->format("H:i")]);
            array_push($breaks, $userBreak);
            $form = $this->createForm(UserBreakType::class, $userBreak, [
                'time_list' => $time_blacklist
            ]);
        }

        return $this->render('user/break.html.twig', [
            "form" => $form->createView(),
            "breaks" => $breaks,
            "errors" => $errors,
            "test_data" => $time_blacklist
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