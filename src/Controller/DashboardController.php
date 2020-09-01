<?php

namespace App\Controller;

use App\Entity\Mandat;
use App\Entity\MandatHistoric;
use App\Entity\Vente;
use App\Entity\VenteHistorique;
use App\Form\MandatType;
use App\Form\VenteType;
use App\Repository\ChallengeRepository;
use App\Repository\MandatRepository;
use App\Repository\UserRepository;
use App\Repository\VenteHistoriqueRepository;
use App\Repository\VenteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security as Access;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use App\Services\WeekFormat;


/**
 * @Access("has_role('ROLE_USER') or has_role('ROLE_ADMIN')")
 */
class DashboardController extends AbstractController
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var VenteRepository;
     */
    private $venteRepository;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var ChallengeRepository
     */
    private $challengeRepository;

    /**
     * @var Security
     */
    private $security;

    /**
     * @var MandatRepository
     */
    private $mandatRepository;

    /**
     * @var VenteHistoriqueRepository
     */
    private $venteHistorique;

    public function __construct(UserRepository $userRepository,
                                VenteRepository $venteRepository,
                                EntityManagerInterface $em,
                                Security $security,
                                ChallengeRepository $challengeRepository,
                                MandatRepository $mandatRepository,
                                VenteHistoriqueRepository $venteHistorique)
    {
        $this->userRepository = $userRepository;
        $this->venteRepository = $venteRepository;
        $this->em = $em;
        $this->security = $security;
        $this->challengeRepository = $challengeRepository;
        $this->mandatRepository = $mandatRepository;
        $this->venteHistorique = $venteHistorique;
    }

    /**
     * @Route("/accueil", name="accueil")
     */
    public function accueil()
    {
        $challenge = $this->challengeRepository
            ->findOneBy(['en_cours' => '1']);

        if($challenge->getImageAccueil() == null)
        {
            $challenge->setImageAccueil("fond.png");
        }

        return $this->render('pages/accueil.html.twig', [
            'challenge' => $challenge
        ]);
    }

    /**
     * @Route("/tableau-de-bord/add-mandat", name="add-mandat")
     * @param Request $request
     * @return JsonResponse
     */
    public function addMandat(Request $request)
    {
        $user = $this->getUser();

        if($user == null)
        {
            return $this->json(['code' => 403, 'message' => 'Unauthorized'], 403);
        }

        if($request->getMethod() == 'POST')
        {
            $data = $request->request->all();

        }

        $mandat = $this->userRepository
            ->findOneBy(array('email' => $user->getUsername()))
            ->getMandat();

        if($mandat == null)
        {
            $currentNombre = 0;
        }
        else
        {
            $currentNombre = $mandat->getNombre();
        }

        $nombre = $data['mandat'];

        if(!is_numeric($nombre))
        {
            return $this->json(['code' => 200,
                'message' => 'Erreur dans l\'ajout de mandat',
            ], 200);
        }

        $currentNombre += $nombre;

        $mandatHistoric = new MandatHistoric();
        $mandatHistoric->setNombre($nombre);
        $mandatHistoric->setUsers($user);
        $mandatHistoric->setDateMandat(new \DateTime());
        $this->em->persist($mandatHistoric);
        $this->em->flush();

        if($mandat == null)
        {
            $mandat = new Mandat();
            $mandat->setNombre($nombre);
            $mandat->setUsers($user);
            $this->em->persist($mandat);
            $this->em->flush();

            return $this->json(['code' => 200,
                'message' => 'Mandat ajouté avec succès',
                'response' => $nombre
            ], 200);
        }
        else
        {
            $mandat->setNombre($currentNombre);
            $this->em->flush();
        }

        return $this->json(['code' => 200,
            'message' => 'Mandat ajouté avec succès',
            'response' => $currentNombre
        ], 200);
    }

    /**
     * @param Request $request
     * @Route("/tableau-de-bord/add-vente", name="add-vente")
     */
    public function addVente(Request $request)
    {
        $user = $this->getUser();
        $errors = [];

        if($user == null)
        {
            return $this->json(['code' => 403, 'message' => 'Unauthorized'], 403);
        }

        if($request->getMethod() == 'POST')
        {
            $data = $request->request->all();
        }

        if(!preg_match('#^([0-2][0-9]|(3)[0-1])(\/)(((0)[0-9])|((1)[0-2]))(\/)\d{4}$#',$data['vente']))
        {
            $errors['date_vente'] = "La date de la vente n'est pas valide";
        }

        if(!preg_match('#^[A-Z]{2}[0-9]{3}[A-Z]{2}$#', $data['immatriculation']))
        {
            $errors['immatriculation'] = "L'immatriculation du véhicule n'est pas au bon format";
        }

        $immatriculation = $this->venteHistorique
            ->findOneBy(['immatriculation' => $data['immatriculation']]);
        
        if($immatriculation != null)
        {
            $errors['immatriculation_vehicule'] = "L'immatriculation du véhicule existe déjà";
        }


        $vente = new Vente();
        $venteHistorique = new VenteHistorique();

        $vente
            ->setDateVente($data['vente'])
            ->setUsers($user)
            ->setImmatriculation($data['immatriculation'])
            ->setLivree($data['livree'])
            ->setFraisMer($data['fraisMER'])
            ->setGarantie($data['garantie'])
            ->setFinancement($data['financement'])
            ->setDateTime(new \DateTime())
            ->setUsers($user);

        $venteHistorique
            ->setDateVente(new \DateTime())
            ->setUsers($user)
            ->setImmatriculation($data['immatriculation'])
            ->setLivree($data['livree'])
            ->setFraisMer($data['fraisMER'])
            ->setGarantie($data['garantie'])
            ->setFinancement($data['financement']);

        if(!empty($errors))
        {
            return $this->json(['code' => 200,
                'errors' => $errors

            ], 200);
        }


        $this->em->persist($vente);
        $this->em->persist($venteHistorique);
        $this->em->flush();

        $countVente = $this->venteRepository
            ->getVenteByUser($user->getUsername());

        $countLivree = $this->venteRepository
            ->getVenteByLivree($user->getUsername());
        $countFraisMER = $this->venteRepository
            ->getVenteByFraisMER($user->getUsername());
        $countGarantie = $this->venteRepository
            ->getVenteByGarantie($user->getUsername());
        $countFinancement = $this->venteRepository
            ->getVenteByFinancement($user->getUsername());


        return $this->json(['code' => 200,
                            'message' => 'Vente ajoutée avec succès',
                            'vente' => $countVente['vente'],
                            'livree' => $countLivree['livree'],
                            'fraisMER' => $countFraisMER['fraisMER'],
                            'garanties' => $countGarantie['garantie'],
                            'financement' => $countFinancement['financement']
        ], 200);


    }


    /**
     * @Route("/tableau-de-bord", name="dashboard")
     * @param Request $request
     * @param WeekFormat $weekFormat
     * @return Response
     */
    public function index(Request $request, WeekFormat $weekFormat)
    {
        $retour = $weekFormat->weekToString(date('Y'), (date('W') - 1));
        date_default_timezone_set('Europe/Paris');
        //Remet les mandats et les ventes à 0 tous les premiers du mois
        if(date("j") == '01' && date("H:i") == '00:00')
        {
            $mandats = $this->mandatRepository
                        ->findAll();

            $ventes = $this->venteRepository
                ->findAll();

            foreach($mandats as $mandat)
            {
                $mandat->setNombre(0);
            }

            foreach($ventes as $vente)
            {
                $this->em->remove($vente);
            }

            $this->em->flush();
        }

        $user = $this->security->getUser();

        $mandat = $this->userRepository
                    ->findOneBy(array('email' => $user->getUsername()))
                    ->getMandat();

        $vente = new Vente();

        $countVente = $this->venteRepository
                        ->getVenteByUser($user->getUsername());

        $countLivree = $this->venteRepository
                        ->getVenteByLivree($user->getUsername());
        $countFraisMER = $this->venteRepository
            ->getVenteByFraisMER($user->getUsername());
        $countGarantie = $this->venteRepository
            ->getVenteByGarantie($user->getUsername());
        $countFinancement = $this->venteRepository
            ->getVenteByFinancement($user->getUsername());


        $formMandat = $this->createForm(MandatType::class, $mandat);
        $formVente = $this->createForm(VenteType::class, $vente);

        $formMandat->handleRequest($request);
        $formVente->handleRequest($request);

        return $this->render('dashboard/dashboard.html.twig', [
            'mandat' => $mandat,
            'form_mandat' => $formMandat->createView(),
            'form_vente' => $formVente->createView(),
            'count_vente' => $countVente,
            'count_livree' => $countLivree,
            'count_fraisMER' => $countFraisMER,
            'count_garantie' => $countGarantie,
            'count_financement' => $countFinancement,
            'semaine' => $retour
        ]);
    }
}
