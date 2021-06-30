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
     * @Route("/admin/export", name="app_admin_export")
     */
    public function break(Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_N1');
        return $this->render('admin/export.html.twig');
    }
}