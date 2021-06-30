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

        $exportRequestType = $request->query->get('type');
        if(isset($exportRequestType) && $exportRequestType != NULL)
        {
            $start = DateTime::createFromFormat("d/m/Y",$request->query->get('filter_date_start'));
            $end = DateTime::createFromFormat("d/m/Y", $request->query->get('filter_date_end'));
            if($exportRequestType == "breaks")
            {
                $breaks = $this->getDoctrine()
                    ->getManager()
                    ->createQuery('SELECT b FROM App\Entity\UserBreak b WHERE b.date >= :start AND b.date <= :end')
                    ->setParameter('start', $start)
                    ->setParameter('end', $end)
                    ->getResult();
                header('Content-Type: text/csv; charset=utf-8');
                header('Content-Disposition: attachment; filename="FooBarFileName_' . date('Ymd') . '.csv"');
                header("Pragma: no-cache");
                header("Expires: 0");
                $this->outputCSV($breaks);
            }
        }

        $this->denyAccessUnlessGranted('ROLE_N1');
        return $this->render('admin/export.html.twig', [
            'errors' => $errors
        ]);
    }

    public function outputCSV($data, $useKeysForHeaderRow = true) {
        if ($useKeysForHeaderRow) {
            array_unshift($data, array_keys(reset($data)));
        }
    
        $outputBuffer = fopen("php://output", 'w');
        foreach($data as $v) {
            fputcsv($outputBuffer, $v);
        }
        fclose($outputBuffer);
    }
}