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

            $user_recoveries = $this->getDoctrine()
            ->getManager()
            ->createQuery('SELECT e FROM App\Entity\UserRecovery e WHERE e.date > DATE_SUB(CURRENT_TIME(), INTERVAL 30 DAY);')
            ->getResult();

            foreach ($user_recoveries as $k2 => $v2) {
                $user["recovery_data"]["nb_30d"] = $user["recovery_data"]["nb_30d"] + 1;
                if($v2->getStatus() == 1)
                    $user["recovery_data"]["time_30d"] = $user["recovery_data"]["time_30d"] + ($v2->getTimeFrom->diff($v2->getTimeTo())->i);    
            }

            $user_recoveries = $this->getDoctrine()
            ->getManager()
            ->createQuery('SELECT e FROM App\Entity\UserRecovery e WHERE e.date > DATE_SUB(CURRENT_TIME(), INTERVAL 7 DAY);')
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

}