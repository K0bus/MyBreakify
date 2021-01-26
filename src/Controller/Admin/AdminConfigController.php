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
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class AdminConfigController extends AbstractController
{
    /**
     * @Route("/admin/settings", name="app_admin_settings")
     */
    public function settings(Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $entityManager = $this->getDoctrine()->getManager();

        $file_break = $request->files->get('file_break');
        $file_user = $request->files->get('file_user');
        if($file_break != NULL)
        {
            $serializer = new Serializer([new ObjectNormalizer()], [new CsvEncoder()]);
            $data = $serializer->decode(file_get_contents($file_break->getPathname()), 'csv');
            foreach ($data as $key => $v) {
                $t = $this->getDoctrine()
                ->getRepository(TimeParam::class)
                ->findOneBy([
                    "time" => DateTime::createFromFormat('H:i', $v["hour"]),
                    "date" => DateTime::createFromFormat('j/m/Y', $v["date"]),
                ]);
                if($t == null)
                    $t = new TimeParam();
                $t->setDate(DateTime::createFromFormat('j/m/Y', $v["date"]));
                $t->setTime(DateTime::createFromFormat('H:i', $v["hour"]));
                $t->setBreak($v["break"]);
                $t->setRecovery($v["recovery"]);
                $t->setBreakAdm($v["adm_break"]);
                $entityManager->persist($t);
                $entityManager->flush();
            }
        }
        if($file_user != NULL)
        {
            $serializer = new Serializer([new ObjectNormalizer()], [new CsvEncoder()]);
            $data = $serializer->decode(file_get_contents('data.csv'), 'csv');
        }

        return $this->render('admin/config.html.twig', [
            "form" => null        ]);
    }
    /**
     * @Route("/admin/recovery/remove/{id<\d+>?1}", name="app_admin_recovery_remove", requirements={"id"="\d+"})
     */
    public function recovery_remove(int $id, Request $request, UserInterface $user): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $userBreak = $this->getDoctrine()
            ->getRepository(UserBreak::class)
            ->find($id);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($userBreak);
        $entityManager->flush();
        return $this->redirectToRoute("app_admin_recovery");
    }
}