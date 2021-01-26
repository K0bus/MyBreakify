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
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $entityManager = $this->getDoctrine()->getManager();

        $userBreak = new UserBreak();
        $startDate = new DateTime("09:00");
        $endDate = new DateTime("19:30");
        $minutes_to_add = 10;
        $time = array();
        $temp = $startDate;

        $errors = array();
        while ($temp <= $endDate)
        {
            $time[$temp->format("H:i")] = new DateTime($temp->format("H:i"));
            $temp->add(new DateInterval('PT' . $minutes_to_add . 'M'));
        }

        $users = $this->getDoctrine()->getRepository(User::class)
            ->findAll();
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
        $startDate = new DateTime("09:00");
        $endDate = new DateTime("19:30");
        $minutes_to_add = 10;
        $time_step = array();
        $temp = $startDate;

        $errors = array();

        while ($temp <= $endDate)
        {
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

            $data = array();
            $data["time"] = $temp->format("H:i");
            $data["breaks"] = $breaks;
            $data["count"] = count($breaks);
            $data["max"] = -1;
            $data["adm_max"] = 0;
            if($time_param != null)
            {
                $data["max"] = $time_param->getBreak();
                $data["adm_max"] = $time_param->getBreakAdm();
            }
                

            $v = $data["count"] / $data["max"] * 100;
            if($data["count"]<$data["max"])
                $data["color"] = "";
            elseif($data["count"]<($data["adm_max"] + $data["max"]))
                $data["color"] = "orange";
            else
                $data["color"] = "red lighten-3";
            
            array_push($time_step, $data);

            $temp->add(new DateInterval('PT' . $minutes_to_add . 'M'));
        }
        return $this->render('admin/break.html.twig', [
            "time_steps" => $time_step,
            "form" => $form->createView()
        ]);
    }
    /**
     * @Route("/admin/break/remove/{id<\d+>?1}", name="app_admin_break_remove", requirements={"id"="\d+"})
     */
    public function break_remove(int $id, Request $request, UserInterface $user): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $userBreak = $this->getDoctrine()
            ->getRepository(UserBreak::class)
            ->find($id);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($userBreak);
        $entityManager->flush();
        return $this->redirectToRoute("app_admin_break");
    }
}