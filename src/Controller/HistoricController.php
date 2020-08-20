<?php

namespace App\Controller;

use App\Entity\MandatHistoric;
use App\Entity\VenteHistorique;
use App\Repository\MandatHistoricRepository;
use App\Repository\UserRepository;
use App\Repository\VenteHistoriqueRepository;
use App\Repository\VenteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security as Access;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use App\Services\MonthToNumber;

/**
 * @Access("has_role('ROLE_USER') or has_role('ROLE_ADMIN')")
 */
class HistoricController extends AbstractController
{
    /**
     * @var MandatHistoricRepository
     */
    private $mandatHistoriqueRepository;

    /**
     * @var UserRepository
     */
    private $userRepository;


    /**
     * @var VenteRepository
     */
    private $venteRepository;

    /**
     * @var VenteHistoriqueRepository
     */
    private $venteHistoriqueRepository;

    /**
     * @var Security
     */
    private $security;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(MandatHistoricRepository $mandatHistoriqueRepository,
                                VenteHistoriqueRepository $venteHistoriqueRepository,
                                Security $security, EntityManagerInterface $em,
                                UserRepository $userRepository,
                                VenteRepository $venteRepository)
    {
        $this->mandatHistoriqueRepository = $mandatHistoriqueRepository;
        $this->venteHistoriqueRepository = $venteHistoriqueRepository;
        $this->userRepository = $userRepository;
        $this->security = $security;
        $this->em = $em;
        $this->venteRepository = $venteRepository;
    }

    /**
     * @Route("/historique", name="historique")
     */
    public function index()
    {
        $user = $this->security->getUser();

        $mandats = $this->mandatHistoriqueRepository->findBy(['users' => $user]);
        $ventes = $this->venteHistoriqueRepository->findBy(['users' => $user]);

        return $this->render('historic/historique.html.twig', [
            'mandats' => $mandats,
            'ventes' => $ventes
        ]);
    }

    /**
     * @param MandatHistoric $mandatHistoric
     * @return JsonResponse
     * @Route("/historique-mandat/{id}", name="historique_mandat")
     */
    public function deleteMandatFromHistorique(MandatHistoric $mandatHistoric)
    {
        $user = $this->security->getUser();

        if($user == null)
        {
            return $this->json(['code' => 403, 'message' => 'Unauthorized'], 403);
        }

        $mandat = $this->mandatHistoriqueRepository
            ->findOneBy([
                'id' => $mandatHistoric,
                'users' => $user
            ]);

        $currentMandat = $this->userRepository
            ->findOneBy(array('email' => $user->getUsername()))
            ->getMandat();

        $newNumber = $currentMandat->getNombre() - $mandat->getNombre();
        $currentMandat->setNombre($newNumber);

        $this->em->remove($mandat);
        $this->em->flush();

        return $this->json(['code' => 200, 'message' => 'Ligne supprimée'], 200);
    }

    /**
     * @param VenteHistorique $venteHistorique
     * @return JsonResponse
     * @Route("/historique-vente/{id}", name="historique_vente")
     */
    public function deleteVenteFromHistorique(VenteHistorique $venteHistorique)
    {
        $user = $this->security->getUser();

        if($user == null)
        {
            return $this->json(['code' => 403, 'message' => 'Unauthorized'], 403);
        }

        $venteFromHistorique = $this->venteHistoriqueRepository
            ->findOneBy([
                'id' => $venteHistorique,
                'users' => $user
            ]);

        $vente = $this->venteRepository
            ->findOneBy(['immatriculation' => $venteFromHistorique->getImmatriculation()]);



        $this->em->remove($venteFromHistorique);
        $this->em->remove($vente);
        $this->em->flush();

        return $this->json(['code' => 200, 'message' => 'Ligne supprimée'], 200);
    }


    /**
     * @param Request $request
     * @return JsonResponse
     * @Route("/historique-filtre", name="filter")
     */
    public function filterVente(Request $request)
    {
        $user = $this->security->getUser();

        if($user == null)
        {
            return $this->json(['code' => 403, 'message' => 'Unauthorized'], 403);
        }

        $monthToNumber = new MonthToNumber();

        if($request->getMethod() == 'POST')
        {
            $data = $request->request->all();
            $new_data = $monthToNumber->month_to_number($data['mois']);

        }


        if($new_data == 'Période')
        {
            $venteFromHistorique = $this->venteHistoriqueRepository->findAll();
            $mandatFromHistorique = $this->mandatHistoriqueRepository->findAll();

        }
        else
        {
            $venteFromHistorique = $this->venteHistoriqueRepository
                ->getMonthFromVente($new_data, $user->getUsername());

            $mandatFromHistorique = $this->mandatHistoriqueRepository
                ->getMonthFromMandat($new_data, $user->getUsername());
        }


        return $this->json(['code' => 200,
                            'message' => 'Données récupérées',
                            'ventes' => $venteFromHistorique,
                            'mandats' => $mandatFromHistorique
        ], 200);

    }


}
