<?php

namespace App\DataFixtures;

use App\Entity\Absence;
use App\Entity\Classe;
use App\Entity\Etudiant;
use App\Entity\Groupe;
use App\Entity\Matiere;
use App\Entity\Professeur;
use App\Entity\Promotion;
use App\Entity\Seance;
use App\Entity\User;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        $this->loadPromotions($manager);
        $this->loadClasses($manager);
        $this->loadGroupes($manager);
        $this->loadMatieres($manager);
        $this->loadEtudiants($manager);
        $this->loadProfesseurs($manager);
        $this->loadSeancesAbsences($manager);
        $this->loadAdmin($manager);
    }

    public function loadPromotions(objectManager $manager){
        $promotions = [
            ['nomPromotion' => '2018-2019 S1-S3', 'annee' => '2018', 'periode' => 'S1-S3', 'actuelle' => false],
            ['nomPromotion' => '2018-2019 S2-S4', 'annee' => '2019', 'periode' => 'S2-S4', 'actuelle' => false],
            ['nomPromotion' => '2019-2020 S1-S3', 'annee' => '2019', 'periode' => 'S1-S3', 'actuelle' => false],
            ['nomPromotion' => '2019-2020 S2-S4', 'annee' => '2020', 'periode' => 'S2-S4', 'actuelle' => true]
        ];
        foreach ($promotions as $promotion)
        {
            $new_promotion = new Promotion();
            $new_promotion->setNomPromotion($promotion['nomPromotion']);
            $new_promotion->setAnnee($promotion['annee']);
            $new_promotion->setPeriode($promotion['periode']);
            $new_promotion->setActuelle($promotion['actuelle']);
            $manager->persist($new_promotion);
            $manager->flush();
        }
    }

    public function loadClasses(objectManager $manager){
        $classes = [
            ['nomClasse' => 'S1-A', 'annee' => '2018', 'nomPromotion' => '2018-2019 S1-S3'],
            ['nomClasse' => 'S1-B', 'annee' => '2018', 'nomPromotion' => '2018-2019 S1-S3'],
            ['nomClasse' => 'S1-C', 'annee' => '2018', 'nomPromotion' => '2018-2019 S1-S3'],
            ['nomClasse' => 'S1-D', 'annee' => '2018', 'nomPromotion' => '2018-2019 S1-S3'],
            ['nomClasse' => 'S2-A', 'annee' => '2019', 'nomPromotion' => '2018-2019 S2-S4'],
            ['nomClasse' => 'S2-B', 'annee' => '2019', 'nomPromotion' => '2018-2019 S2-S4'],
            ['nomClasse' => 'S2-C', 'annee' => '2019', 'nomPromotion' => '2018-2019 S2-S4'],
            ['nomClasse' => 'S2-D', 'annee' => '2019', 'nomPromotion' => '2018-2019 S2-S4'],
            ['nomClasse' => 'S3-A', 'annee' => '2018', 'nomPromotion' => '2018-2019 S1-S3'],
            ['nomClasse' => 'S3-B', 'annee' => '2018', 'nomPromotion' => '2018-2019 S1-S3'],
            ['nomClasse' => 'S3-C', 'annee' => '2018', 'nomPromotion' => '2018-2019 S1-S3'],
            ['nomClasse' => 'S4-A', 'annee' => '2019', 'nomPromotion' => '2018-2019 S2-S4'],
            ['nomClasse' => 'S4-B', 'annee' => '2019', 'nomPromotion' => '2018-2019 S2-S4'],
            ['nomClasse' => 'S4-C', 'annee' => '2019', 'nomPromotion' => '2018-2019 S2-S4'],

            ['nomClasse' => 'S1-A', 'annee' => '2019', 'nomPromotion' => '2019-2020 S1-S3'],
            ['nomClasse' => 'S1-B', 'annee' => '2019', 'nomPromotion' => '2019-2020 S1-S3'],
            ['nomClasse' => 'S1-C', 'annee' => '2019', 'nomPromotion' => '2019-2020 S1-S3'],
            ['nomClasse' => 'S1-D', 'annee' => '2019', 'nomPromotion' => '2019-2020 S1-S3'],
            ['nomClasse' => 'S2-A', 'annee' => '2020', 'nomPromotion' => '2019-2020 S2-S4'],
            ['nomClasse' => 'S2-B', 'annee' => '2020', 'nomPromotion' => '2019-2020 S2-S4'],
            ['nomClasse' => 'S2-C', 'annee' => '2020', 'nomPromotion' => '2019-2020 S2-S4'],
            ['nomClasse' => 'S2-D', 'annee' => '2020', 'nomPromotion' => '2019-2020 S2-S4'],
            ['nomClasse' => 'S3-A', 'annee' => '2019', 'nomPromotion' => '2019-2020 S1-S3'],
            ['nomClasse' => 'S3-B', 'annee' => '2019', 'nomPromotion' => '2019-2020 S1-S3'],
            ['nomClasse' => 'S3-C', 'annee' => '2019', 'nomPromotion' => '2019-2020 S1-S3'],
            ['nomClasse' => 'S4-A', 'annee' => '2020', 'nomPromotion' => '2019-2020 S2-S4'],
            ['nomClasse' => 'S4-B', 'annee' => '2020', 'nomPromotion' => '2019-2020 S2-S4'],
            ['nomClasse' => 'S4-C', 'annee' => '2020', 'nomPromotion' => '2019-2020 S2-S4']
        ];
        foreach($classes as $classe){
            $new_classe = new Classe();
            $new_classe->setNomClasse($classe['nomClasse']);
            $new_classe->setAnnee($classe['annee']);
            $promotion = $manager->getRepository(Promotion::class)->findOneBy(['nomPromotion'=>$classe['nomPromotion']]);
            $new_classe->setPromotion($promotion);
            $manager->persist($new_classe);
            $manager->flush();
        }
    }

    public function loadGroupes(objectManager $manager){
        $groupes = [
            ['nomGroupe' => 'S1-A1', 'annee' => '2018', 'nomClasse' => 'S1-A'],
            ['nomGroupe' => 'S1-A2', 'annee' => '2018', 'nomClasse' => 'S1-A'],
            ['nomGroupe' => 'S1-B1', 'annee' => '2018', 'nomClasse' => 'S1-B'],
            ['nomGroupe' => 'S1-B2', 'annee' => '2018', 'nomClasse' => 'S1-B'],
            ['nomGroupe' => 'S1-C1', 'annee' => '2018', 'nomClasse' => 'S1-C'],
            ['nomGroupe' => 'S1-C2', 'annee' => '2018', 'nomClasse' => 'S1-C'],
            ['nomGroupe' => 'S1-D1', 'annee' => '2018', 'nomClasse' => 'S1-D'],
            ['nomGroupe' => 'S1-D2', 'annee' => '2018', 'nomClasse' => 'S1-D'],

            ['nomGroupe' => 'S2-A1', 'annee' => '2019', 'nomClasse' => 'S2-A'],
            ['nomGroupe' => 'S2-A2', 'annee' => '2019', 'nomClasse' => 'S2-A'],
            ['nomGroupe' => 'S2-B1', 'annee' => '2019', 'nomClasse' => 'S2-B'],
            ['nomGroupe' => 'S2-B2', 'annee' => '2019', 'nomClasse' => 'S2-B'],
            ['nomGroupe' => 'S2-C1', 'annee' => '2019', 'nomClasse' => 'S2-C'],
            ['nomGroupe' => 'S2-C2', 'annee' => '2019', 'nomClasse' => 'S2-C'],
            ['nomGroupe' => 'S2-D1', 'annee' => '2019', 'nomClasse' => 'S2-D'],
            ['nomGroupe' => 'S2-D2', 'annee' => '2019', 'nomClasse' => 'S2-D'],

            ['nomGroupe' => 'S3-A1', 'annee' => '2018', 'nomClasse' => 'S3-A'],
            ['nomGroupe' => 'S3-A2', 'annee' => '2018', 'nomClasse' => 'S3-A'],
            ['nomGroupe' => 'S3-B1', 'annee' => '2018', 'nomClasse' => 'S3-B'],
            ['nomGroupe' => 'S3-B2', 'annee' => '2018', 'nomClasse' => 'S3-B'],
            ['nomGroupe' => 'S3-C1', 'annee' => '2018', 'nomClasse' => 'S3-C'],
            ['nomGroupe' => 'S3-C2', 'annee' => '2018', 'nomClasse' => 'S3-C'],

            ['nomGroupe' => 'S4-A1', 'annee' => '2019', 'nomClasse' => 'S4-A'],
            ['nomGroupe' => 'S4-A2', 'annee' => '2019', 'nomClasse' => 'S4-A'],
            ['nomGroupe' => 'S4-B1', 'annee' => '2019', 'nomClasse' => 'S4-B'],
            ['nomGroupe' => 'S4-B2', 'annee' => '2019', 'nomClasse' => 'S4-B'],
            ['nomGroupe' => 'S4-C1', 'annee' => '2019', 'nomClasse' => 'S4-C'],
            ['nomGroupe' => 'S4-C2', 'annee' => '2019', 'nomClasse' => 'S4-C'],

            /* ------------------------------------------------------------------------------ */

            ['nomGroupe' => 'S1-A1', 'annee' => '2019', 'nomClasse' => 'S1-A'],
            ['nomGroupe' => 'S1-A2', 'annee' => '2019', 'nomClasse' => 'S1-A'],
            ['nomGroupe' => 'S1-B1', 'annee' => '2019', 'nomClasse' => 'S1-B'],
            ['nomGroupe' => 'S1-B2', 'annee' => '2019', 'nomClasse' => 'S1-B'],
            ['nomGroupe' => 'S1-C1', 'annee' => '2019', 'nomClasse' => 'S1-C'],
            ['nomGroupe' => 'S1-C2', 'annee' => '2019', 'nomClasse' => 'S1-C'],
            ['nomGroupe' => 'S1-D1', 'annee' => '2019', 'nomClasse' => 'S1-D'],
            ['nomGroupe' => 'S1-D2', 'annee' => '2019', 'nomClasse' => 'S1-D'],

            ['nomGroupe' => 'S2-A1', 'annee' => '2020', 'nomClasse' => 'S2-A'],
            ['nomGroupe' => 'S2-A2', 'annee' => '2020', 'nomClasse' => 'S2-A'],
            ['nomGroupe' => 'S2-B1', 'annee' => '2020', 'nomClasse' => 'S2-B'],
            ['nomGroupe' => 'S2-B2', 'annee' => '2020', 'nomClasse' => 'S2-B'],
            ['nomGroupe' => 'S2-C1', 'annee' => '2020', 'nomClasse' => 'S2-C'],
            ['nomGroupe' => 'S2-C2', 'annee' => '2020', 'nomClasse' => 'S2-C'],
            ['nomGroupe' => 'S2-D1', 'annee' => '2020', 'nomClasse' => 'S2-D'],
            ['nomGroupe' => 'S2-D2', 'annee' => '2020', 'nomClasse' => 'S2-D'],

            ['nomGroupe' => 'S3-A1', 'annee' => '2019', 'nomClasse' => 'S3-A'],
            ['nomGroupe' => 'S3-A2', 'annee' => '2019', 'nomClasse' => 'S3-A'],
            ['nomGroupe' => 'S3-B1', 'annee' => '2019', 'nomClasse' => 'S3-B'],
            ['nomGroupe' => 'S3-B2', 'annee' => '2019', 'nomClasse' => 'S3-B'],
            ['nomGroupe' => 'S3-C1', 'annee' => '2019', 'nomClasse' => 'S3-C'],
            ['nomGroupe' => 'S3-C2', 'annee' => '2019', 'nomClasse' => 'S3-C'],

            ['nomGroupe' => 'S4-A1', 'annee' => '2020', 'nomClasse' => 'S4-A'],
            ['nomGroupe' => 'S4-A2', 'annee' => '2020', 'nomClasse' => 'S4-A'],
            ['nomGroupe' => 'S4-B1', 'annee' => '2020', 'nomClasse' => 'S4-B'],
            ['nomGroupe' => 'S4-B2', 'annee' => '2020', 'nomClasse' => 'S4-B'],
            ['nomGroupe' => 'S4-C1', 'annee' => '2020', 'nomClasse' => 'S4-C'],
            ['nomGroupe' => 'S4-C2', 'annee' => '2020', 'nomClasse' => 'S4-C']
        ];
        foreach($groupes as $groupe){
            $new_groupe = new Groupe();
            $new_groupe->setNomGroupe($groupe['nomGroupe']);
            $new_groupe->setAnnee($groupe['annee']);
            $classe = $manager->getRepository(Classe::class)->findOneBy(['nomClasse' => $groupe['nomClasse'], 'annee' => $groupe['annee']]);
            $new_groupe->addClasse($classe);
            $manager->persist($new_groupe);
            $manager->flush();
        }
    }

    public function loadMatieres(objectManager $manager){
        $matieres = [
            ['nomMatiere' => 'Algo'],
            ['nomMatiere' => 'CDIN Web'],
            ['nomMatiere' => 'Concept. Obj'],
            ['nomMatiere' => 'Réseau'],
            ['nomMatiere' => 'WEB'],
            ['nomMatiere' => 'Système'],
            ['nomMatiere' => 'Rech. opéra.'],
            ['nomMatiere' => 'Appli. Mobiles']
        ];
        foreach($matieres as $matiere){
            $new_matiere = new Matiere();
            $new_matiere->setNomMatiere($matiere['nomMatiere']);
            $manager->persist($new_matiere);
            $manager->flush();
        }
    }

    public function loadEtudiants(objectManager $manager){
        $etudiants = [
            ['username' => 'lmartin', 'ine' => '0000000001A', 'prenomEtudiant' => 'Louis', 'nomEtudiant' => 'Martin', 'dateNaissance' => '01-01-1999', 'email' => 'louis.martin@edu.univ-fcomte.fr', 'groupes' =>
                [
                    ['nomGroupe' => 'S3-A1', 'annee' => '2018'],
                    ['nomGroupe' => 'S4-A1', 'annee' => '2019']
                ]
            ],
            ['username' => 'cbernard', 'ine' => '0000000002B', 'prenomEtudiant' => 'Camille', 'nomEtudiant' => 'Bernard', 'dateNaissance' => '02-01-1999','email' => 'camille.bernard@edu.univ-fcomte.fr', 'groupes' =>
                [
                    ['nomGroupe' => 'S3-A2', 'annee' => '2018'],
                    ['nomGroupe' => 'S4-A2', 'annee' => '2019']
                ]
            ],
            ['username' => 'gthomas', 'ine' => '0000000003C', 'prenomEtudiant' => 'Gabriel', 'nomEtudiant' => 'Thomas', 'dateNaissance' => '03-01-1999','email' => 'gabriel.thomas@edu.univ-fcomte.fr', 'groupes' =>
                [
                    ['nomGroupe' => 'S3-B1', 'annee' => '2018'],
                    ['nomGroupe' => 'S4-B1', 'annee' => '2019']
                ]
            ],
            ['username' => 'lpetit', 'ine' => '0000000004C', 'prenomEtudiant' => 'Louise', 'nomEtudiant' => 'Petit', 'dateNaissance' => '04-01-1999','email' => 'louise.petit@edu.univ-fcomte.fr', 'groupes' =>
                [
                    ['nomGroupe' => 'S3-B2', 'annee' => '2018'],
                    ['nomGroupe' => 'S4-B2', 'annee' => '2019']
                ]
            ],
            ['username' => 'lrobert', 'ine' => '0000000005E', 'prenomEtudiant' => 'Léo', 'nomEtudiant' => 'Robert', 'dateNaissance' => '05-01-1999','email' => 'leo.robert@edu.univ-fcomte.fr', 'groupes' =>
                [
                    ['nomGroupe' => 'S3-C1', 'annee' => '2018'],
                    ['nomGroupe' => 'S4-C1', 'annee' => '2019']
                ]
            ],
            ['username' => 'lrichard', 'ine' => '0000000006F', 'prenomEtudiant' => 'Léa', 'nomEtudiant' => 'Richard', 'dateNaissance' => '06-01-1999','email' => 'lea.richard@edu.univ-fcomte.fr', 'groupes' =>
                [
                    ['nomGroupe' => 'S3-C2', 'annee' => '2018'],
                    ['nomGroupe' => 'S4-C2', 'annee' => '2019']
                ]
            ],

            /* --------------------------------------------------------------------------------------------------- */

            ['username' => 'mdurant', 'ine' => '0000000007G', 'prenomEtudiant' => 'Maël', 'nomEtudiant' => 'Durand', 'dateNaissance' => '01-01-2000', 'email' => 'mael.durant@edu.univ-fcomte.fr', 'groupes' =>
                [
                    ['nomGroupe' => 'S1-A1', 'annee' => '2018'],
                    ['nomGroupe' => 'S2-A1', 'annee' => '2019'],
                    ['nomGroupe' => 'S3-A1', 'annee' => '2019'],
                    ['nomGroupe' => 'S4-A1', 'annee' => '2020']
                ]
            ],
            ['username' => 'adubois', 'ine' => '0000000008H', 'prenomEtudiant' => 'Ambre', 'nomEtudiant' => 'Dubois', 'dateNaissance' => '02-01-2000', 'email' => 'ambre.dubois@edu.univ-fcomte.fr', 'groupes' =>
                [
                    ['nomGroupe' => 'S1-A2', 'annee' => '2018'],
                    ['nomGroupe' => 'S2-A2', 'annee' => '2019'],
                    ['nomGroupe' => 'S3-A2', 'annee' => '2019'],
                    ['nomGroupe' => 'S4-A2', 'annee' => '2020']
                ]
            ],
            ['username' => 'pmoreau', 'ine' => '0000000009I', 'prenomEtudiant' => 'Paul', 'nomEtudiant' => 'Moreau', 'dateNaissance' => '03-01-2000', 'email' => 'paul.moreau@edu.univ-fcomte.fr', 'groupes' =>
                [
                    ['nomGroupe' => 'S1-B1', 'annee' => '2018'],
                    ['nomGroupe' => 'S2-B1', 'annee' => '2019'],
                    ['nomGroupe' => 'S3-B1', 'annee' => '2019'],
                    ['nomGroupe' => 'S4-B1', 'annee' => '2020']
                ]
            ],
            ['username' => 'alaurent', 'ine' => '0000000010J', 'prenomEtudiant' => 'Agathe', 'nomEtudiant' => 'Laurent', 'dateNaissance' => '04-01-2000', 'email' => 'agathe.laurent@edu.univ-fcomte.fr', 'groupes' =>
                [
                    ['nomGroupe' => 'S1-B2', 'annee' => '2018'],
                    ['nomGroupe' => 'S2-B2', 'annee' => '2019'],
                    ['nomGroupe' => 'S3-B2', 'annee' => '2019'],
                    ['nomGroupe' => 'S4-B2', 'annee' => '2020']
                ]
            ],
            ['username' => 'hsimon', 'ine' => '0000000011K', 'prenomEtudiant' => 'Hugo', 'nomEtudiant' => 'Simon', 'dateNaissance' => '05-01-2000', 'email' => 'hugo.simon@edu.univ-fcomte.fr', 'groupes' =>
                [
                    ['nomGroupe' => 'S1-C1', 'annee' => '2018'],
                    ['nomGroupe' => 'S2-C1', 'annee' => '2019'],
                    ['nomGroupe' => 'S3-C1', 'annee' => '2019'],
                    ['nomGroupe' => 'S4-C1', 'annee' => '2020']
                ]
            ],
            ['username' => 'jmichel', 'ine' => '0000000012L', 'prenomEtudiant' => 'Jade', 'nomEtudiant' => 'Michel', 'dateNaissance' => '06-01-2000', 'email' => 'jade.michel@edu.univ-fcomte.fr', 'groupes' =>
                [
                    ['nomGroupe' => 'S1-C2', 'annee' => '2018'],
                    ['nomGroupe' => 'S2-C2', 'annee' => '2019'],
                    ['nomGroupe' => 'S3-C2', 'annee' => '2019'],
                    ['nomGroupe' => 'S4-C2', 'annee' => '2020']
                ]
            ],
            ['username' => 'vlefebvre', 'ine' => '0000000013M', 'prenomEtudiant' => 'Valentin', 'nomEtudiant' => 'Lefebvre', 'dateNaissance' => '07-01-2000', 'email' => 'valentin.lefebvre@edu.univ-fcomte.fr', 'groupes' =>
                [
                    ['nomGroupe' => 'S1-D1', 'annee' => '2018'],
                    ['nomGroupe' => 'S2-D1', 'annee' => '2019']
                ]
            ],
            ['username' => 'jleroy', 'ine' => '0000000014N', 'prenomEtudiant' => 'Julia', 'nomEtudiant' => 'Leroy', 'dateNaissance' => '08-01-2000', 'email' => 'julia.leroy@edu.univ-fcomte.fr', 'groupes' =>
                [
                    ['nomGroupe' => 'S1-D2', 'annee' => '2018'],
                    ['nomGroupe' => 'S2-D2', 'annee' => '2019']
                ]
            ],

            /* --------------------------------------------------------------------------------------------------- */

            ['username' => 'groux', 'ine' => '0000000015O', 'prenomEtudiant' => 'Gabin', 'nomEtudiant' => 'Roux', 'dateNaissance' => '01-01-2001', 'email' => 'gabin.roux@edu.univ-fcomte.fr', 'groupes' =>
                [
                    ['nomGroupe' => 'S1-A1', 'annee' => '2019'],
                    ['nomGroupe' => 'S2-A1', 'annee' => '2020']
                ]
            ],
            ['username' => 'mdavid', 'ine' => '0000000016P', 'prenomEtudiant' => 'Mila', 'nomEtudiant' => 'David', 'dateNaissance' => '02-01-2001', 'email' => 'mila.david@edu.univ-fcomte.fr', 'groupes' =>
                [
                    ['nomGroupe' => 'S1-A2', 'annee' => '2019'],
                    ['nomGroupe' => 'S2-A2', 'annee' => '2020']
                ]
            ],
            ['username' => 'abertrand', 'ine' => '0000000017Q', 'prenomEtudiant' => 'Arthur', 'nomEtudiant' => 'Bertrand', 'dateNaissance' => '03-01-2001', 'email' => 'arthur.bertrand@edu.univ-fcomte.fr', 'groupes' =>
                [
                    ['nomGroupe' => 'S1-B1', 'annee' => '2019'],
                    ['nomGroupe' => 'S2-B1', 'annee' => '2020']
                ]
            ],
            ['username' => 'amorel', 'ine' => '0000000018R', 'prenomEtudiant' => 'Alice', 'nomEtudiant' => 'Morel', 'dateNaissance' => '04-01-2001', 'email' => 'alice.morel@edu.univ-fcomte.fr', 'groupes' =>
                [
                    ['nomGroupe' => 'S1-B2', 'annee' => '2019'],
                    ['nomGroupe' => 'S2-B2', 'annee' => '2020']
                ]
            ],
            ['username' => 'tfournier', 'ine' => '0000000019S', 'prenomEtudiant' => 'Théo', 'nomEtudiant' => 'Fournier', 'dateNaissance' => '05-01-2001', 'email' => 'theo.fournier@edu.univ-fcomte.fr', 'groupes' =>
                [
                    ['nomGroupe' => 'S1-C1', 'annee' => '2019'],
                    ['nomGroupe' => 'S2-C1', 'annee' => '2020']
                ]
            ],
            ['username' => 'cgirard', 'ine' => '0000000020T', 'prenomEtudiant' => 'Chloé', 'nomEtudiant' => 'Girard', 'dateNaissance' => '06-01-2001', 'email' => 'chloe.girard@edu.univ-fcomte.fr', 'groupes' =>
                [
                    ['nomGroupe' => 'S1-C2', 'annee' => '2019'],
                    ['nomGroupe' => 'S2-C2', 'annee' => '2020']
                ]
            ],
            ['username' => 'jperrot', 'ine' => '0000000021U', 'prenomEtudiant' => 'Jules', 'nomEtudiant' => 'Perrot', 'dateNaissance' => '07-01-2001', 'email' => 'jules.perrot@edu.univ-fcomte.fr', 'groupes' =>
                [
                    ['nomGroupe' => 'S1-D1', 'annee' => '2019'],
                    ['nomGroupe' => 'S2-D1', 'annee' => '2020']
                ]
            ],
            ['username' => 'eestavoyer', 'ine' => '0000000022V', 'prenomEtudiant' => 'Emma', 'nomEtudiant' => 'Estavoyer', 'dateNaissance' => '08-01-2001', 'email' => 'emma.estavoyer@edu.univ-fcomte.fr', 'groupes' =>
                [
                    ['nomGroupe' => 'S1-D2', 'annee' => '2019'],
                    ['nomGroupe' => 'S2-D2', 'annee' => '2020']
                ]
            ]
        ];
        foreach($etudiants as $etudiant){
            $new_etudiant = new Etudiant();
            $new_etudiant->setIne($etudiant['ine']);
            $new_etudiant->setPrenomEtudiant($etudiant['prenomEtudiant']);
            $new_etudiant->setNomEtudiant($etudiant['nomEtudiant']);
            $new_etudiant->setEmailEtudiant($etudiant['email']);
            $new_etudiant->setDateNaissance($etudiant['dateNaissance']);
            foreach($etudiant['groupes'] as $groupe){
                $groupeEtu = $manager->getRepository(Groupe::class)->findOneBy(['nomGroupe' => $groupe['nomGroupe'], 'annee' => $groupe['annee']]);
                $new_etudiant->addGroupe($groupeEtu);
            }
            $manager->persist($new_etudiant);
            $manager->flush();
            $newUser = new User();
            $newUser->setUsername($etudiant['username']);
            $newUser->setPassword($this->passwordEncoder->encodePassword($newUser, $etudiant['ine']));
            $newUser->setRoles(["ROLE_STUDENT"]);
            $newUser->setType("Etudiant");
            $newUser->setTypeId($new_etudiant->getId());
            $manager->persist($newUser);
            $manager->flush();
        }
    }

    public function loadProfesseurs(objectManager $manager){
        $professeurs = [
            ['username' => 'aneville', 'arpege' => '000000001ABCD', 'prenomProfesseur' => 'Arwen', 'nomProfesseur' => 'Neville', 'dateNaissance' => '01-01-1970', 'email' => 'arwenn.neville@test.test', 'matieres' => ['Algo']],
            ['username' => 'bperkins', 'arpege' => '000000002ABCD', 'prenomProfesseur' => 'Brenna', 'nomProfesseur' => 'Perkins',  'dateNaissance' => '02-01-1970', 'email' => 'brenna.perkins@test.test', 'matieres' => ['CDIN Web']],
            ['username' => 'hhulme', 'arpege' => '000000003ABCD', 'prenomProfesseur' => 'Husnain', 'nomProfesseur' => 'Hulme', 'dateNaissance' => '03-01-1970', 'email' => 'husnain.hulme@test.test', 'matieres' => ['Concept. Obj']],
            ['username' => 'hcrouch', 'arpege' => '000000004ABCD', 'prenomProfesseur' => 'Herbie', 'nomProfesseur' => 'Crouch', 'dateNaissance' => '04-01-1970', 'email' => 'herbie.crouch@test.test', 'matieres' => ['Réseau']],
            ['username' => 'kdavidson', 'arpege' => '000000005ABCD', 'prenomProfesseur' => 'Kody', 'nomProfesseur' => 'Davidson', 'dateNaissance' => '05-01-1970', 'email' => 'kody.davidson@test.test', 'matieres' => ['WEB']],
            ['username' => 'vchapman', 'arpege' => '000000006ABCD', 'prenomProfesseur' => 'Viola', 'nomProfesseur' => 'Chapman', 'dateNaissance' => '06-01-1970', 'email' => 'viola.chapman@test.test', 'matieres' => ['Système']],
            ['username' => 'mowen', 'arpege' => '000000007ABCD', 'prenomProfesseur' => 'Merryn', 'nomProfesseur' => 'Owen', 'dateNaissance' => '07-01-1970', 'email' => 'merryn.owen@test.test', 'matieres' => ['Rech. opéra.']],
            ['username' => 'cwong', 'arpege' => '000000008ABCD', 'prenomProfesseur' => 'Casper', 'nomProfesseur' => 'Wong', 'dateNaissance' => '08-01-1970', 'email' => 'casper.wong@test.test', 'matieres' => ['Appli. Mobiles']]
        ];
        foreach($professeurs as $professeur){
            $new_professeur = new Professeur();
            $new_professeur->setArpege($professeur['arpege']);
            $new_professeur->setPrenomProfesseur($professeur['prenomProfesseur']);
            $new_professeur->setNomProfesseur($professeur['nomProfesseur']);
            $new_professeur->setEmailProfesseur($professeur['email']);
            $new_professeur->setDateNaissance($professeur['dateNaissance']);
            foreach($professeur['matieres'] as $matiere){
                $matiereEnseignee = $manager->getRepository(Matiere::class)->findOneBy(['nomMatiere' => $matiere]);
                $new_professeur->addMatiere($matiereEnseignee);
            }
            $manager->persist($new_professeur);
            $manager->flush();
            $newUser = new User();
            $newUser->setUsername($professeur['username']);
            $newUser->setPassword($this->passwordEncoder->encodePassword($newUser, $professeur['arpege']));
            $newUser->setRoles(["ROLE_TEACHER"]);
            $newUser->setType("Professeur");
            $newUser->setTypeId($new_professeur->getId());
            $manager->persist($newUser);
            $manager->flush();
        }
    }

    public function loadSeancesAbsences(objectManager $manager){
        $seances = [
            [
                'professeur' => $manager->getRepository(Professeur::class)->findOneBy(['arpege' => '000000001ABCD']),
                'groups' => [
                    $manager->getRepository(Groupe::class)->findOneBy(['annee' => '2019', 'nomGroupe' => 'S3-A1']),
                    $manager->getRepository(Groupe::class)->findOneBy(['annee' => '2019', 'nomGroupe' => 'S3-A2']),
                ],
                'matiere' => $manager->getRepository(Matiere::class)->findOneBy(['nomMatiere' => 'Algo']),
                'presences' => [
                    ['etudiant' => $manager->getRepository(Etudiant::class)->findOneBy(['nomEtudiant' => 'Durand']), 'presence' => true],
                    ['etudiant' => $manager->getRepository(Etudiant::class)->findOneBy(['nomEtudiant' => 'Dubois']), 'presence' => false],
                ]
            ],
            [
                'professeur' => $manager->getRepository(Professeur::class)->findOneBy(['arpege' => '000000002ABCD']),
                'groups' => [
                    $manager->getRepository(Groupe::class)->findOneBy(['annee' => '2019', 'nomGroupe' => 'S3-A1']),
                    $manager->getRepository(Groupe::class)->findOneBy(['annee' => '2019', 'nomGroupe' => 'S3-A2']),
                ],
                'matiere' => $manager->getRepository(Matiere::class)->findOneBy(['nomMatiere' => 'CDIN Web']),
                'presences' => [
                    ['etudiant' => $manager->getRepository(Etudiant::class)->findOneBy(['nomEtudiant' => 'Durand']), 'presence' => true],
                    ['etudiant' => $manager->getRepository(Etudiant::class)->findOneBy(['nomEtudiant' => 'Dubois']), 'presence' => false],
                ]
            ],
            [
                'professeur' => $manager->getRepository(Professeur::class)->findOneBy(['arpege' => '000000002ABCD']),
                'groups' => [
                    $manager->getRepository(Groupe::class)->findOneBy(['annee' => '2019', 'nomGroupe' => 'S3-A1']),
                    $manager->getRepository(Groupe::class)->findOneBy(['annee' => '2019', 'nomGroupe' => 'S3-A2']),
                ],
                'matiere' => $manager->getRepository(Matiere::class)->findOneBy(['nomMatiere' => 'CDIN Web']),
                'presences' => [
                    ['etudiant' => $manager->getRepository(Etudiant::class)->findOneBy(['nomEtudiant' => 'Durand']), 'presence' => true],
                    ['etudiant' => $manager->getRepository(Etudiant::class)->findOneBy(['nomEtudiant' => 'Dubois']), 'presence' => false],
                ]
            ],
            [
                'professeur' => $manager->getRepository(Professeur::class)->findOneBy(['arpege' => '000000008ABCD']),
                'groups' => [
                    $manager->getRepository(Groupe::class)->findOneBy(['annee' => '2020', 'nomGroupe' => 'S4-A1']),
                    $manager->getRepository(Groupe::class)->findOneBy(['annee' => '2020', 'nomGroupe' => 'S4-A2']),
                ],
                'matiere' => $manager->getRepository(Matiere::class)->findOneBy(['nomMatiere' => 'Appli. Mobiles']),
                'presences' => [
                    ['etudiant' => $manager->getRepository(Etudiant::class)->findOneBy(['nomEtudiant' => 'Durand']), 'presence' => false],
                    ['etudiant' => $manager->getRepository(Etudiant::class)->findOneBy(['nomEtudiant' => 'Dubois']), 'presence' => false],
                ]
            ],
            [
                'professeur' => $manager->getRepository(Professeur::class)->findOneBy(['arpege' => '000000008ABCD']),
                'groups' => [
                    $manager->getRepository(Groupe::class)->findOneBy(['annee' => '2020', 'nomGroupe' => 'S4-A1']),
                    $manager->getRepository(Groupe::class)->findOneBy(['annee' => '2020', 'nomGroupe' => 'S4-A2']),
                ],
                'matiere' => $manager->getRepository(Matiere::class)->findOneBy(['nomMatiere' => 'Appli. Mobiles']),
                'presences' => [
                    ['etudiant' => $manager->getRepository(Etudiant::class)->findOneBy(['nomEtudiant' => 'Durand']), 'presence' => true],
                    ['etudiant' => $manager->getRepository(Etudiant::class)->findOneBy(['nomEtudiant' => 'Dubois']), 'presence' => false],
                ]
            ],
        ];
        foreach($seances as $seance){
            $new_seance = new Seance();
            $date = date("d-m-Y H:i:s");
            $new_seance->setDate($date);
            $new_seance->setMatiere($seance['matiere']);
            $new_seance->setProfesseur($seance['professeur']);
            foreach($seance['groups'] as $group){
                $new_seance->addGroupe($group);
            }
            foreach($seance['presences'] as $presence){
                if(!$presence['presence']){
                    $new_absence = new Absence();
                    $new_absence->setEtudiant($presence['etudiant']);
                    $new_absence->setJustifiee(false);
                    $new_absence->setJustification("");
                    $manager->persist($new_absence);
                    $new_seance->addAbsence($new_absence);
                }
            }
            $manager->persist($new_seance);
        }
        $manager->flush();
        $seances = $manager->getRepository(Seance::class)->findAll();
        $etudiant = $manager->getRepository(Etudiant::class)->findOneBy(['nomEtudiant' => 'Dubois']);
        $absence = $manager->getRepository(Absence::class)->findOneBy(['seance' => $seances[3], 'etudiant' => $etudiant]);
        $absence->setJustifiee(true);
        $absence->setJustification("Panne de réveil");
        $manager->persist($absence);
        $manager->flush();
    }

    public function loadAdmin(objectManager $manager){
        $newUser = new User();
        $newUser->setUsername('admin');
        $newUser->setPassword($this->passwordEncoder->encodePassword($newUser, 'saucisson'));
        $newUser->setRoles(["ROLE_ADMIN"]);
        $newUser->setType("Admin");
        $newUser->setTypeId(-1);
        $manager->persist($newUser);
        $manager->flush();
    }
}
