<?php

namespace App\Controller\adminControllers;

use App\Entity\Classe;
use App\Entity\Etudiant;
use App\Entity\Groupe;
use App\Entity\Promotion;
use App\Entity\User;
use App\Type\ImportType;
use Doctrine\ORM\EntityManagerInterface;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

/**
 * @IsGranted("ROLE_ADMIN")
 */
class importController extends AbstractController {

    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * @Route("/admin/download", name="admin.download")
     */
    public function downloadFileAction(): BinaryFileResponse
    {
        $response = new BinaryFileResponse(__DIR__ . '/../../Tables/Exemple.xlsx');
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT,'Exemple.xlsx');
        return $response;
    }

    /**
     * @Route("/admin/import", name="admin.import")
     * @param Request $request
     * @param SluggerInterface $slugger
     * @param EntityManagerInterface $manager
     * @return Response
     */
    public function import(Request $request, SluggerInterface $slugger, EntityManagerInterface $manager): Response
    {
        $form = $this->createForm(ImportType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $brochureFile */
            $brochureFile = $form->get('tableau')->getData();
            $originalFilename = pathinfo($brochureFile->getClientOriginalName(), PATHINFO_FILENAME);
            $safeFilename = $slugger->slug($originalFilename);
            $newFilename = $safeFilename.'-'.uniqid().'.'.$brochureFile->guessExtension();
            try {
                $brochureFile->move(
                    $this->getParameter('table_directory'),
                    $newFilename
                );
                $this->read($newFilename, $form->get('promotion')->getData(), $manager);
            } catch (FileException $e) {  dd($e); }
            return $this->redirectToRoute('index.index');
        }
        return $this->render('Admin/adminImport.html.twig', ['form' => $form->createView()]);
    }

    public function read($filename, Promotion $promotion, EntityManagerInterface $manager){
        $tabReader = ["ine" => "A", "prenom" => "B", "nom" => "C", "username" => "D", "e-mail" => "E", "semestre" => "F", "classe" => "G", "groupe" => "H", "dateNaissance" => "I"];
        $row = 2;
        $reader = new Xlsx();
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load(__DIR__ . "/../../Tables/" . $filename);
        $workSheet = $spreadsheet->getActiveSheet();
        $verifEnd = false;
        $groupe = null;
        do {
            if(!($workSheet->getCell($tabReader["ine"] . $row)->getValue() === NULL || $workSheet->getCell($tabReader["ine"] . $row)->getValue() === "")){
                $classes = $promotion->getClasses();
                $verifClasse = null;
                foreach($classes as $classe) {
                    if($classe->getNomClasse() === ("S" . $workSheet->getCell($tabReader["semestre"] . $row)->getValue() . "-" . $workSheet->getCell($tabReader["classe"] . $row)->getValue())){
                        $verifClasse = $classe;
                        break;
                    }
                }
                if($verifClasse == null){
                    $new_classe = new Classe();
                    $new_classe->setNomClasse("S" . $workSheet->getCell($tabReader["semestre"] . $row)->getValue() . "-" . $workSheet->getCell($tabReader["classe"] . $row)->getValue());
                    $new_classe->setPromotion($promotion);
                    $new_classe->setAnnee($promotion->getAnnee());
                    $manager->persist($new_classe);
                    $promotion->addClass($new_classe);
                    $manager->flush();
                    $new_groupe = new Groupe();
                    $new_groupe->setNomGroupe("S" . $workSheet->getCell($tabReader["semestre"] . $row)->getValue() . "-" . $workSheet->getCell($tabReader["classe"] . $row)->getValue() . $workSheet->getCell($tabReader["groupe"] . $row)->getValue());
                    $new_groupe->addClass($new_classe);
                    $new_groupe->setAnnee($promotion->getAnnee());
                    $manager->persist($new_groupe);
                    $manager->flush();
                    $groupe = $new_groupe;
                }
                else{
                    $groupes = $verifClasse->getGroupes();
                    $verifGroupe = null;
                    foreach($groupes as $groupe){
                        if($groupe->getNomGroupe() === ($verifClasse->getNomClasse() . $workSheet->getCell($tabReader["groupe"] . $row)->getValue())){
                            $verifGroupe = $groupe;
                            break;
                        }
                    }
                    if($verifGroupe == null){
                        $new_groupe = new Groupe();
                        $new_groupe->setNomGroupe($verifClasse->getNomClasse() . $workSheet->getCell($tabReader["groupe"] . $row)->getValue());
                        $new_groupe->addClass($verifClasse);
                        $new_groupe->setAnnee($promotion->getAnnee());
                        $manager->persist($new_groupe);
                        $manager->flush();
                        $groupe = $new_groupe;
                    }
                    else{ $groupe = $verifGroupe; }
                }
                $etudiant = $manager->getRepository(Etudiant::class)->findOneBy(['ine' => $workSheet->getCell($tabReader["ine"] . $row)->getValue()]);
                if($etudiant == null){
                    $new_etudiant = new Etudiant();
                    $new_etudiant->setIne($workSheet->getCell($tabReader["ine"] . $row));
                    $new_etudiant->setPrenomEtudiant($workSheet->getCell($tabReader["prenom"] . $row));
                    $new_etudiant->setNomEtudiant($workSheet->getCell($tabReader["nom"] . $row));
                    $new_etudiant->setEmailEtudiant($workSheet->getCell($tabReader["e-mail"] . $row));
                    $new_etudiant->setDateNaissance(str_replace("#","",$workSheet->getCell($tabReader["dateNaissance"] . $row)->getValue()));
                    $new_etudiant->addGroupe($groupe);
                    $manager->persist($new_etudiant);
                    $manager->flush();
                    $new_user = new User();
                    $new_user->setUsername($workSheet->getCell($tabReader["username"] . $row));
                    $new_user->setPassword($this->passwordEncoder->encodePassword($new_user, $workSheet->getCell($tabReader["ine"] . $row)));
                    $new_user->setRoles(["ROLE_STUDENT"]);
                    $new_user->setType("Etudiant");
                    $new_user->setTypeId($new_etudiant->getId());
                    $manager->persist($new_user);
                    $manager->flush();
                }
                else{
                    $etudiant->setIne($workSheet->getCell($tabReader["ine"] . $row));
                    $etudiant->setPrenomEtudiant($workSheet->getCell($tabReader["prenom"] . $row));
                    $etudiant->setNomEtudiant($workSheet->getCell($tabReader["nom"] . $row));
                    $etudiant->setEmailEtudiant($workSheet->getCell($tabReader["e-mail"] . $row));
                    $etudiant->setDateNaissance(str_replace("#","",$workSheet->getCell($tabReader["dateNaissance"] . $row)->getValue()));
                    $etudiant->addGroupe($groupe);
                    $manager->persist($etudiant);
                    $manager->flush();
                }
                $row++;
            }
            else{ $verifEnd = true; }
        } while(!$verifEnd);
        $filesystem = new Filesystem();
        $filesystem->remove(__DIR__ . "/../../Tables/" . $filename);
    }
}