<?php

namespace App\Controller\User;

use App\Entity\UserBreak;
use App\Form\UserBreakType;
use DateInterval;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints\Date;

class UserController extends AbstractController
{
    /**
     * @Route("/", name="app_home")
     */
    public function home(): Response
    {
        return $this->render('base.html.twig');
    }
}
