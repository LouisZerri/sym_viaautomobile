<?php

namespace App\Controller;

use App\Entity\MandatHistoric;
use App\Entity\VenteHistorique;
use App\Repository\MandatHistoricRepository;
use App\Repository\UserRepository;
use App\Repository\VenteHistoriqueRepository;
use App\Repository\VenteRepository;
use App\Services\WeekFormat;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security as Access;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(MandatHistoricRepository $mandatHistoriqueRepository,
                                VenteHistoriqueRepository $venteHistoriqueRepository,
                                EntityManagerInterface $em,
                                UserRepository $userRepository,
                                VenteRepository $venteRepository)
    {
        $this->mandatHistoriqueRepository = $mandatHistoriqueRepository;
        $this->venteHistoriqueRepository = $venteHistoriqueRepository;
        $this->userRepository = $userRepository;
        $this->em = $em;
        $this->venteRepository = $venteRepository;
    }

    /**
     * @Route("/historique", name="historique")
     * @return Response
     */
    public function index(WeekFormat $weekFormat)
    {
        $retour = $weekFormat->weekToString(date('Y'), (date('W') - 1));

        $user = $this->getUser();

        $mandats = $this->mandatHistoriqueRepository->myFindAll($user);
        $ventes = $this->venteHistoriqueRepository->myFindAll($user);

        return $this->render('historic/historique.html.twig', [
            'mandats' => $mandats,
            'ventes' => $ventes,
            'semaine' => $retour
        ]);
    }

    /**
     * @param MandatHistoric $mandatHistoric
     * @return JsonResponse
     * @Route("/historique-mandat/{id}", name="historique_mandat")
     */
    public function deleteMandatFromHistorique(MandatHistoric $mandatHistoric)
    {
        $user = $this->getUser();

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
        $user = $this->getUser();

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
     * @Route("/historique-vente", name="filtre-vente")
     * @param Request $request
     * @param MonthToNumber $monthToNumber
     * @return Response
     */
    public function filtreVente(Request $request, MonthToNumber $monthToNumber)
    {
        $user = $this->getUser();

        if($request->isXmlHttpRequest())
        {
            $data = $request->request->all();

            $new_data = $monthToNumber->month_to_number($data['mois']);

            if($new_data == 'periode')
            {
                $venteFromHistorique = $this->venteHistoriqueRepository
                    ->myFindAll($user);
            }
            else
            {
                $venteFromHistorique = $this->venteHistoriqueRepository
                    ->getMonthFromVente($new_data, $user->getUsername());

            }
        }

        return $this->render('historic/ajax/_filtervente.html.twig', [
            'ventes' => $venteFromHistorique,
        ]);
    }

    /**
     * @param Request $request
     * @param MonthToNumber $monthToNumber
     * @return Response
     * @Route("/historique-mandat", name="filtre-mandat")
     */
    public function filtreMandat(Request $request, MonthToNumber $monthToNumber)
    {
        $user = $this->getUser();

        if($request->isXmlHttpRequest())
        {
            $data = $request->request->all();

            $new_data = $monthToNumber->month_to_number($data['mois']);

            if($new_data == 'periode')
            {
                $mandatFromHistorique = $this->mandatHistoriqueRepository
                    ->myFindAll($user);
            }
            else
            {
                $mandatFromHistorique = $this->mandatHistoriqueRepository
                    ->getMonthFromMandat($new_data, $user->getUsername());

            }
        }

        return $this->render('historic/ajax/_filtermandat.html.twig', [
            'mandats' => $mandatFromHistorique,
        ]);
    }



}
