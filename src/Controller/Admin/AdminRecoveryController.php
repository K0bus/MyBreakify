<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Entity\UserBreak;
use App\Entity\UserRecovery;
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

class AdminRecoveryController extends AbstractController
{
    /**
     * @Route("/admin/recovery", name="app_admin_recovery")
     */
    public function recovery(Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_N1');
        
        $entityManager = $this->getDoctrine()->getManager();

        $recoveries = $this->getDoctrine()
        ->getRepository(UserRecovery::class)
        ->findBy([
            "date" => new DateTime(),
        ]);
        $users = array();
        foreach ($recoveries as $key => $value) {
            $user = array();
            $user["data"] = $value->getUserId();

            $user["recovery_data"]["nb_7d"] = 0;
            $user["recovery_data"]["nb_30d"] = 0;
            $user["recovery_data"]["time_7d"] = 0;
            $user["recovery_data"]["time_30d"] = 0;

            $qb = $entityManager->createQueryBuilder();

            $user_recoveries = $qb->select('r')
                                  ->from("App\Entity\UserRecovery", 'r')
                                  ->where('r.date >= :date')
                                  ->andWhere('r.user_id = :user')
                                  ->setParameter('date', new DateTime("-30 days"))
                                  ->setParameter('user', $user["data"])
                                  ->getQuery()
                                  ->getResult();

            foreach ($user_recoveries as $k2 => $v2) {
                $user["recovery_data"]["nb_30d"] = $user["recovery_data"]["nb_30d"] + 1;
                if($v2->getStatus() == 1)
                    $user["recovery_data"]["time_30d"] = $user["recovery_data"]["time_30d"] + ($v2->getTimeFrom->diff($v2->getTimeTo())->i);    
            }

            $qb = $entityManager->createQueryBuilder();
            $user_recoveries = $qb->select('r')
                                  ->from("App\Entity\UserRecovery", 'r')
                                  ->where('r.date >= :date')
                                  ->andWhere('r.user_id = :user')
                                  ->setParameter('date', new DateTime("-7 days"))
                                  ->setParameter('user', $user["data"])
                                  ->getQuery()
                                  ->getResult();

            foreach ($user_recoveries as $k2 => $v2) {
                $user["recovery_data"]["nb_7d"] = $user["recovery_data"]["nb_7d"] + 1;
                if($v2->getStatus() == 1)
                    $user["recovery_data"]["time_7d"] = $user["recovery_data"]["time_7d"] + ($v2->getTimeFrom->diff($v2->getTimeTo())->i);    
            }

            if(!array_key_exists($user["data"]->getId(), $users))
                $users[$user["data"]->getId()] = $user;
        }

        return $this->render('admin/recovery.html.twig',[
            "recoveries" => $recoveries,
            "users" => $users
        ]);
    }
    /**
     * @Route("/admin/recovery/edit/{id<\d+>?1}", name="app_admin_recovery_edit", requirements={"id"="\d+"})
     */
    public function break_remove(int $id, Request $request, UserInterface $user): Response
    {
        $this->denyAccessUnlessGranted('ROLE_N1');

        $userRecovery = $this->getDoctrine()
            ->getRepository(UserRecovery::class)
            ->find($id);
        
        $userRecovery->setStatus($request->request->get('status'));
        $userRecovery->setTimeFrom($request->request->get('timeFrom'));
        $userRecovery->setTimeTo($request->request->get('timeTo'));

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->update($userRecovery);
        $entityManager->flush();
        return $this->redirectToRoute("app_admin_recovery");
    }
}