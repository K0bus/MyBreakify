<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Entity\UserBreak;
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

class AdminController extends AbstractController
{
    /**
     * @Route("/admin", name="app_admin_dashboard")
     */
    public function dashboard(): Response
    {
        return $this->render('admin/dashboard.html.twig');
    }

}