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

        return $this->render('admin/recovery.html.twig',[
            "recoveries" => $recoveries
        ]);
    }

}