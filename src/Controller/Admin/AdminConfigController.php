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
        $success = array();

        $entityManager = $this->getDoctrine()->getManager();

        $file_break = $request->files->get('file_break');
        $file_user = $request->files->get('file_user');
        if($file_break != NULL)
        {
                $serializer = new Serializer([new ObjectNormalizer()], [new CsvEncoder()]);
                $data = $serializer->decode(str_replace(";",",",file_get_contents($file_break->getPathname())), 'csv');
                $needed = array("hour", "date", "break", "recovery", "adm_break");
                foreach ($needed as $key => $value) {
                    if(!array_key_exists($value, $data[0]))
                        array_push($errors, "Le fichier CSV " . $file_user->getClientOriginalName() . " ne contient pas la colonne " . $value );
                }
                if(count($errors) == 0)
                {
                    $i = 0;
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
                        $i++;
                    }
                    array_push($success, $i . " données injectées avec succès !");
                }
        }
        if($file_user != NULL)
        {
            $serializer = new Serializer([new ObjectNormalizer()], [new CsvEncoder()]);
            $data = $serializer->decode(str_replace(";",",",file_get_contents($file_user->getPathname())), 'csv');
            $now = new DateTime();
            if(isset($data[0]))
            {
                $needed = array("username", "email", "firstname", "lastname", "password", "role");
                foreach ($needed as $key => $value) {
                    if(!array_key_exists($value, $data[0]))
                        array_push($errors, "Le fichier CSV " . $file_user->getClientOriginalName() . " ne contient pas la colonne " . $value );
                }
                if(count($errors) == 0)
                {
                    $i_edit = 0;
                    $i_new = 0;
                    foreach ($data as $key => $v) {
                        if($this->updateUser($v, $encoder))
                        {
                            $i_new++;
                        }
                        else
                        {
                            $i_edit++;
                        }
                    }
                    if($i_new>0)
                    {
                        array_push($success, $i_new." nouveau comptes créé(s) !");
                    }
                    if($i_edit>0)
                    {
                        array_push($success, $i_edit." comptes édité(s) !");
                    }
                }
            }
            else
            {
                array_push($errors, "Le fichier CSV " . $file_user->getClientOriginalName() . " est vide !" );
            }

        }

        return $this->render('admin/config.html.twig', [
            "errors" => $errors,
            "success" => $success
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

    public function updateUser($v, $encoder)
    {
        $now = new DateTime();
        $entityManager = $this->getDoctrine()->getManager();
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
        switch ($v["role"]) {
            case 'ADMIN':
                $t->setRoles(array("ROLE_ADMIN", "ROLE_N1"));
                break;
            case 'N1':
                $t->setRoles(array("ROLE_N1"));
                break;
            
            default:
                $t->setRoles(array("ROLE_USER"));
                break;
        }
        $entityManager->persist($t);
        $entityManager->flush();
        return $new;
    }
}