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

        $date = new DateTime();

        if($request->request->get('filter_date') != null)
        {
            $date = DateTime::createFromFormat("d/m/Y", $request->request->get('filter_date'));
        }  

        $recoveries = $this->getDoctrine()
        ->getRepository(UserRecovery::class)
        ->findBy([
            "date" => $date,
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
                    $user["recovery_data"]["time_30d"] = $user["recovery_data"]["time_30d"] + ($v2->getTimeFrom()->diff($v2->getTimeTo())->i);    
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
                    $user["recovery_data"]["time_7d"] = $user["recovery_data"]["time_7d"] + ($v2->getTimeFrom()->diff($v2->getTimeTo())->i);    
            }

            if(!array_key_exists($user["data"]->getId(), $users))
                $users[$user["data"]->getId()] = $user;
        }

        return $this->render('admin/recovery.html.twig',[
            "recoveries" => $recoveries,
            "users" => $users,
            "date_filter" => $date
        ]);
    }
    /**
     * @Route("/admin/recovery/edit/{id<\d+>?1}", name="app_admin_recovery_edit", requirements={"id"="\d+"})
     */
    public function recovery_editor(int $id, Request $request, UserInterface $user): Response
    {
        $this->denyAccessUnlessGranted('ROLE_N1');

        $entityManager = $this->getDoctrine()->getManager();

        $userRecovery = $entityManager
            ->getRepository(UserRecovery::class)
            ->find($id);
        
        $userRecovery->setStatus(intval($request->request->get('status')));
        $userRecovery->setTimeFrom(new DateTime($request->request->get('timeFrom')));
        $userRecovery->setTimeTo(new DateTime($request->request->get('timeTo')));

        $entityManager->flush();
        return $this->redirectToRoute("app_admin_recovery");
    }
        /**
     * @Route("/admin/recovery/pdf/html/{id<\d+>?1}", name="app_recovery_pdf_html", requirements={"id"="\d+"})
     */
    public function recovery_pdf_html(int $id, Request $request, UserInterface $user): Response
    {
        $recovery = $this->getDoctrine()
            ->getRepository(UserRecovery::class)
            ->find($id);
        return $this->render('admin/recovery_pdf.html.twig', [
            "recovery" => $recovery
        ]);
    }
}