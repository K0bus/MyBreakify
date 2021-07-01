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
            echo $exportRequestType;
            $start = DateTime::createFromFormat("d/m/Y",$request->query->get('filter_date_start'));
            $end = DateTime::createFromFormat("d/m/Y", $request->query->get('filter_date_end'));
            if($exportRequestType == "breaks")
            {
                echo "TEST2";
                $breaks = $this->getDoctrine()
                    ->getManager()
                    ->createQuery('SELECT b FROM UserBreak b WHERE b.date >= :start AND b.date <= :end')
                    ->setParameter('start', $start)
                    ->setParameter('end', $end)
                    ->getResult();
                    $rows = array();
                    foreach ($breaks as $break) {
                        $data = array($break->getId(), $break->getName(), $break->getTime()->format('Y-m-d H:i:s'));
                        var_dump($data);
                        $rows[] = implode(',', $data);
                    }
                    $content = implode("\n", $rows);
                    $response = new Response($content);
                    $response->headers->set('Content-Type', 'text/csv');
                    return $response;
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