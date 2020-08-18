<?php

namespace App\Controller;

use App\Entity\Mandat;
use App\Entity\MandatHistoric;
use App\Entity\Vente;
use App\Entity\VenteHistorique;
use App\Form\MandatType;
use App\Form\VenteType;
use App\Repository\ChallengeRepository;
use App\Repository\UserRepository;
use App\Repository\VenteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use App\Services\WeekFormat;

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

    public function __construct(UserRepository $userRepository, VenteRepository $venteRepository, EntityManagerInterface $em, Security $security, ChallengeRepository $challengeRepository)
    {
        $this->userRepository = $userRepository;
        $this->venteRepository = $venteRepository;
        $this->em = $em;
        $this->security = $security;
        $this->challengeRepository = $challengeRepository;
    }

    /**
     * @Route("/accueil", name="accueil")
     */
    public function accueil()
    {
        $user = $this->security->getUser();

        if($user == null)
        {
            return $this->redirectToRoute('home');
        }

        $challenge = $this->challengeRepository
            ->findOneBy(['en_cours' => '1']);

        return $this->render('pages/accueil.html.twig', [
            'challenge' => $challenge
        ]);
    }


    /**
     * @Route("/tableau-de-bord", name="dashboard")
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $weektostr = new WeekFormat();
        $retour = $weektostr->weekToString(date('Y'), (date('W') - 1));

        $user = $this->security->getUser();

        if($user == null)
        {
            return $this->redirectToRoute('home');
        }

        $mandat = $this->userRepository
                    ->findOneBy(array('email' => $user->getUsername()))
                    ->getMandat();

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

        if($mandat == null)
        {
            $currentNombre = 0;
        }
        else
        {
            $currentNombre = $mandat->getNombre();
        }

        $vente = new Vente();
        $venteHistorique = new VenteHistorique();

        $formMandat = $this->createForm(MandatType::class, $mandat);
        $formVente = $this->createForm(VenteType::class, $vente);

        $formMandat->handleRequest($request);
        $formVente->handleRequest($request);

        if($formMandat->isSubmitted() && $formMandat->isValid())
        {
            $nombre = $formMandat->getData()->getNombre();

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
            }
            else
            {
                $mandat->setNombre($currentNombre);
                $this->em->flush();
            }

            $this->addFlash('success', 'Mandat ajouté avec succès');
            return $this->redirectToRoute('dashboard');
        }

        if($formVente->isSubmitted() && $formVente->isValid())
        {
            $vente->setDateTime(new \DateTime());
            $vente->setUsers($user);

            $venteHistorique
                ->setDateVente(new \DateTime())
                ->setUsers($user)
                ->setImmatriculation($formVente->getData()->getImmatriculation())
                ->setLivree($formVente->getData()->getLivree())
                ->setFraisMer($formVente->getData()->getFraisMER())
                ->setGarantie($formVente->getData()->getGarantie())
                ->setFinancement($formVente->getData()->getFinancement());


            $this->em->persist($vente);
            $this->em->persist($venteHistorique);
            $this->em->flush();

            $this->addFlash('success', 'Vente ajoutée avec succès');
            return $this->redirectToRoute('dashboard');

        }




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
