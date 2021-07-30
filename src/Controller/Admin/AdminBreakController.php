<?php

namespace App\Controller\Admin;

use App\Entity\UserBreak;
use App\Entity\User;
use App\Entity\TimeParam;
use App\Form\AdminUserBreakType;
use DateInterval;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints\Date;

class AdminBreakController extends AbstractController
{
    /**
     * @Route("/admin/break", name="app_admin_break")
     */
    public function break(Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_N1');

        $entityManager = $this->getDoctrine()->getManager();

        $userBreak = new UserBreak();

        $errors = array();


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
            $time_arr[$t]["allowed_adm"] = $param->getBreakAdm();
            $time_arr[$t]["time"] = $t;
            $time_arr[$t]["color"] = "";
        }
        foreach ($breaks_glob as $key => $break) {
            $t = $break->getTime()->format("H:i");
            if(isset($time_arr[$t]["taken"]))
                $time_arr[$t]["taken"]++;
            else
                $time_arr[$t]["taken"] = 1;
            if(isset($time_arr[$t]["breaks"]))
                array_push($time_arr[$t]["breaks"], $break);
            else
            {
                $time_arr[$t]["breaks"] = array();
                array_push($time_arr[$t]["breaks"], $break);
            }
            if($time_arr[$t]["taken"] >= $time_arr[$t]["allowed"])
            {
                if($time_arr[$t]["taken"] >= ($time_arr[$t]["allowed"]+$time_arr[$t]["allowed_adm"]))
                {
                    $time_arr[$t]["color"] = "orange";
                }
                else
                    $time_arr[$t]["color"] = "red lighten-3";
            }
        }

        $users = $this->getDoctrine()->getRepository(User::class)
            ->findBy(array(), array("firstname" => "ASC"));
        $user_list = array();
        foreach ($users as $key => $user) {
            $user_list[$user->getFirstname() . ", " . $user->getLastname()] = $user;
        }
        $form = $this->createForm(AdminUserBreakType::class, $userBreak, [
            'time_list' => $time,
            'user_list' => $user_list
        ]);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $userBreak = $form->getData();
            $userBreak->setDate(new DateTime());
            $userBreak->setRequestedAt(new DateTime());
            
            $entityManager->persist($userBreak);
            $entityManager->flush();
        }

        return $this->render('admin/break.html.twig', [
            "time_steps" => $time_arr,
            "form" => $form->createView(),
            "date_filter" => $date
        ]);
    }
    /**
     * @Route("/admin/break/remove/{id<\d+>?1}", name="app_admin_break_remove", requirements={"id"="\d+"})
     */
    public function break_remove(int $id, Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_N1');

        $userBreak = $this->getDoctrine()
            ->getRepository(UserBreak::class)
            ->find($id);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($userBreak);
        $entityManager->flush();
        return $this->redirectToRoute("app_admin_break");
    }
}