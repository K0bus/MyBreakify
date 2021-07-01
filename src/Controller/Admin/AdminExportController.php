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
    public function break(Request $request): Response
    {
        $errors = array();
        array_push($errors, "Export actuellement non disponible.");

        $entityManager = $this->getDoctrine()->getManager();

        $exportRequestType = $request->request->get('typeForm');
        if($exportRequestType != NULL)
        {
            $start = DateTime::createFromFormat("d/m/Y",$request->request->get('filter_date_start'));
            $end = DateTime::createFromFormat("d/m/Y", $request->request->get('filter_date_end'));
            echo $exportRequestType;
            if($exportRequestType == "breaks")
            {
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
                    $content = implode("\n", $rows);
                    $response = new Response($content);
                    $response->headers->set('Content-Type', 'text/csv;');
                    $response->headers->set('Content-Disposition', 'attachment; filename='.basename('break_data.csv'));
                    return $response;
            }
            elseif($exportRequestType == "recovery")
            {
                $recoveries = $this->getDoctrine()
                    ->getManager()
                    ->createQuery('SELECT b FROM App\Entity\UserRecovery b WHERE b.date >= :start AND b.date <= :end')
                    ->setParameter('start', $start)
                    ->setParameter('end', $end)
                    ->getResult();
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
                            $recovery->getRequestedAt()->format('d/m/Y H:i'));
                        $rows[] = implode(';', $data);
                    }
                    $content = implode("\n", $rows);
                    $response = new Response($content);
                    $response->headers->set('Content-Type', 'text/csv;');
                    $response->headers->set('Content-Disposition', 'attachment; filename='.basename('recovery_data.csv'));
                    return $response;
            }
        }

        $this->denyAccessUnlessGranted('ROLE_N1');
        return $this->render('admin/export.html.twig', [
            'errors' => $errors
        ]);
    }
}