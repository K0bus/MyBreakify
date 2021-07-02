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
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AdminConfigController extends AbstractController
{
    /**
     * @Route("/admin/settings", name="app_admin_settings")
     */
    public function settings(Request $request, UserPasswordEncoderInterface $encoder): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $errors = array();

        $entityManager = $this->getDoctrine()->getManager();

        $file_break = $request->files->get('file_break');
        $file_user = $request->files->get('file_user');
        if($file_break != NULL)
        {
                $serializer = new Serializer([new ObjectNormalizer()], [new CsvEncoder()]);
                $data = $serializer->decode(str_replace(";",",",file_get_contents($file_break->getPathname())), 'csv');
                if(!array_key_exists("hour", $data[1]) || !array_key_exists("date", $data[1]) || !array_key_exists("break", $data[1]) || 
                    !array_key_exists("recovery", $data[1]) || !array_key_exists("adm_break", $data[1]))
                {
                    array_push($errors, "Le fichier CSV " . $file_break->getClientOriginalName() . " n'est pas correcte !" );
                }
                else
                {
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
        }
        if($file_user != NULL)
        {
            $serializer = new Serializer([new ObjectNormalizer()], [new CsvEncoder()]);
            $data = $serializer->decode(str_replace(";",",",file_get_contents($file_user->getPathname())), 'csv');
            $now = new DateTime();
            if(!array_key_exists("username", $data[1]) || !array_key_exists("email", $data[1]) || !array_key_exists("firstname", $data[1]) || 
                !array_key_exists("lastname", $data[1]) || !array_key_exists("password", $data[1]) || !array_key_exists("role", $data[1]))
            {
                array_push($errors, "Le fichier CSV " . $file_user->getClientOriginalName() . " n'est pas correcte !" );
            }
            else
            {
                foreach ($data as $key => $v) {
                    $t = $this->getDoctrine()
                    ->getRepository(User::class)
                    ->findOneBy([
                        "username" => $v["username"]
                    ]);
                    $new = false;
                    if($t == null)
                    {
                        $t = new User();
                        $new = true;
                    }
                    $t->setUsername($v["username"]);
                    $t->setEmail($v["email"]);
                    $t->setFirstname($v["firstname"]);
                    $t->setLastname($v["lastname"]);
                    if($new && $v["password"] != "")
                    {
                        $t->setPassword($encoder->encodePassword($t, $v["password"]));
                    }
                    elseif($new)
                    {
                        $t->setPassword($encoder->encodePassword($t, md5(microtime())));
                    }
                    if(!$new && $v["password"] != "")
                        $t->setPassword($encoder->encodePassword($t, $v["password"]));
                    if($new)
                    {
                        $t->setCreatedAt($now);
                        $t->setLoggedAt($now);
                    }
                    if($v["role"] == "ADMIN")
                    {
                        $t->setRoles(array("ROLE_ADMIN", "ROLE_N1"));
                    }
                    elseif ($v["role"] == "N1") {
                        $t->setRoles(array("ROLE_N1"));
                    }
                    else ($v["role"] == "USER")
                    {
                        $t->setRoles(array());
                    }
                    //TODO : Add role
                    $entityManager->persist($t);
                    $entityManager->flush();
                }
            }
        }

        return $this->render('admin/config.html.twig', [
            "errors" => $errors,
        ]);
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