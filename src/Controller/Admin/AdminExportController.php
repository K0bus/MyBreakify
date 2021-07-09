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

class AdminExportController extends AbstractController
{
    /**
     * @Route("/admin/export", name="app_admin_export")
     */
    public function export(Request $request): Response
    {
        $errors = array();

        $entityManager = $this->getDoctrine()->getManager();

        $exportRequestType = $request->request->get('typeForm');
        if($exportRequestType != NULL)
        {
            $start = DateTime::createFromFormat("d/m/Y",$request->request->get('filter_date_start'))->format('d/m/y');
            $end = DateTime::createFromFormat("d/m/Y", $request->request->get('filter_date_end'))->format('d/m/y');
            if($exportRequestType == "recovery")
            {
                if($start == $end)
                {
                    $breaks = $this->getDoctrine()
                    ->getRepository(UserRecovery::class)
                    ->findAllBy([
                        "date" => $start
                    ]);
                }
                else
                {
                    $recoveries = $this->getDoctrine()
                    ->getManager()
                    ->createQuery('SELECT b FROM App\Entity\UserRecovery b WHERE b.date >= :start AND b.date <= :end')
                    ->setParameter('start', $start)
                    ->setParameter('end', $end)
                    ->getResult();
                }
                $rows = array();
                $data = array("id", "date", "username", "time_from", "time_to", "status", "comment", "requested_at");
                $rows[] = implode(';', $data);
                foreach ($recoveries as $recovery) {
                    $data = array($recovery->getId(),
                    $recovery->getDate()->format('d/m/Y'),
                    $recovery->getUserId()->getUsername(),
                    $recovery->getTimeFrom()->format('H:i'),
                    $recovery->getTimeTo()->format('H:i'),
                    $recovery->getStatus(),
                    $recovery->getComment(),
                    $recovery->getRequestAt()->format('d/m/Y H:i'));
                    $rows[] = implode(';', $data);
                }
                if(count($rows) > 1)
                {
                    $content = implode("\n", $rows);
                    $response = new Response($content);
                    $response->headers->set('Content-Type', 'text/csv;');
                    $response->headers->set('Content-Disposition', 'attachment; filename='.basename('recovery_data_'.$start->format('dmY').'_'.$end->format('dmY').'.csv'));
                    return $response;
                }
                else {
                    array_push($errors, "Aucune données à extraire pour la période du ".$start->format('d/m/Y')." au ".$end->format('d/m/Y'));
                }
            }
            if($exportRequestType == "breaks")
            {
                if($start == $end)
                {
                    $breaks = $this->getDoctrine()
                    ->getRepository(UserBreak::class)
                    ->findAllBy([
                        "date" => $start
                    ]);
                }
                else
                {
                    $breaks = $this->getDoctrine()
                        ->getManager()
                        ->createQuery('SELECT b FROM App\Entity\UserBreak b WHERE b.date >= :start AND b.date <= :end')
                        ->setParameter('start', $start)
                        ->setParameter('end', $end)
                        ->getResult();
                }
                $breaks = $this->getDoctrine()
                    ->getManager()
                    ->createQuery('SELECT b FROM App\Entity\UserBreak b WHERE b.date >= :start AND b.date <= :end')
                    ->setParameter('start', $start)
                    ->setParameter('end', $end)
                    ->getResult();
                    $rows = array();
                    $data = array("id", "date", "username", "time", "requested_at");
                    $rows[] = implode(';', $data);
                    foreach ($breaks as $break) {
                        $data = array($break->getId(), $break->getDate()->format('d/m/Y'), $break->getUserId()->getUsername(), $break->getTime()->format('H:i'), $break->getRequestedAt()->format('d/m/Y H:i'));
                        $rows[] = implode(';', $data);
                    }
                    if(count($rows) > 1)
                    {
                        $content = implode("\n", $rows);
                        $response = new Response($content);
                        $response->headers->set('Content-Type', 'text/csv;');
                        $response->headers->set('Content-Disposition', 'attachment; filename='.basename('break_data_'.$start->format('dmY').'_'.$end->format('dmY').'.csv'));
                        return $response;
                    }
                    else {
                        array_push($errors, "Aucune données à extraire pour la période du ".$start->format('d/m/Y')." au ".$end->format('d/m/Y'));
                    }
            }

        }

        $this->denyAccessUnlessGranted('ROLE_N1');
        return $this->render('admin/export.html.twig', [
            'errors' => $errors
        ]);
    }
}