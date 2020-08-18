<?php

namespace App\Controller;

use App\Repository\ChallengeRepository;
use App\Repository\MandatRepository;
use App\Repository\VenteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ChallengeController extends AbstractController
{
    /**
     * @var ChallengeRepository
     */
    private $challengeRepository;

    /**
     * @var VenteRepository
     */
    private $venteRepository;

    /**
     * @var MandatRepository
     */
    private $mandatRepository;

    public function __construct(ChallengeRepository $challengeRepository, VenteRepository $venteRepository, MandatRepository $mandatRepository)
    {
        $this->challengeRepository = $challengeRepository;
        $this->venteRepository = $venteRepository;
        $this->mandatRepository = $mandatRepository;
    }

    /**
     * @Route("/challenges", name="challenges")
     */
    public function index()
    {
        $user = $this->getUser();

        if($user == null)
        {
            return $this->redirectToRoute('home');
        }

        $challengeEnCours = $this->challengeRepository
            ->findOneBy(['en_cours' => '1']);

        $challengesPasses = $this->challengeRepository
            ->findBy(['en_cours' => '0'], null, 2);

        $enTeteVente = $this->venteRepository
            ->enTeteChallengeVente();

        $enTeteMandat = $this->mandatRepository
            ->enTeteChallengeMandat();


        if(count($enTeteMandat) == 0)
        {
            $enTeteMandat[0] = [
                'nom' => 'Inconnu',
                'prenom' => '',
                'nombre' => 0
            ];
            $enTeteMandat[1] = [
                'nom' => 'Inconnu',
                'prenom' => '',
                'nombre' => 0
            ];
            $enTeteMandat[2] = [
                'nom' => 'Inconnu',
                'prenom' => '',
                'nombre' => 0
            ];
        }
        else if(count($enTeteMandat) == 1)
        {
            $enTeteMandat[1] = [
                'nom' => 'Inconnu',
                'prenom' => '',
                'nombre' => 0
            ];
            $enTeteMandat[2] = [
                'nom' => 'Inconnu',
                'prenom' => '',
                'nombre' => 0
            ];;
        }
        else if(count($enTeteMandat) == 2)
        {
            $enTeteMandat[2] =[
                'nom' => '',
                'prenom' => '',
                'nombre' => 0
            ];

        }

        if(count($enTeteVente) == 0)
        {
            $enTeteVente[0] = [
                'nom' => 'Inconnu',
                'prenom' => '',
                'vente' => 0
            ];
            $enTeteVente[1] = [
                'nom' => 'Inconnu',
                'prenom' => '',
                'vente' => 0
            ];
            $enTeteVente[2] = [
                'nom' => 'Inconnu',
                'prenom' => '',
                'vente' => 0
            ];
        }
        else if(count($enTeteVente) == 1)
        {
            $enTeteVente[1] = [
                'nom' => 'Inconnu',
                'prenom' => '',
                'vente' => 0
            ];
            $enTeteVente[2] = [
                'nom' => 'Inconnu',
                'prenom' => '',
                'vente' => 0
            ];;
        }
        else if(count($enTeteVente) == 2)
        {
            $enTeteVente[2] =[
                'nom' => '',
                'prenom' => '',
                'vente' => 0
            ];
        }


        return $this->render('challenge/challenge.html.twig', [
            'challenge_en_cours' => $challengeEnCours,
            'challenges_passes' => $challengesPasses,
            'first_vente' => $enTeteVente[0],
            'second_vente' => $enTeteVente[1],
            'third_vente' => $enTeteVente[2],
            'first_mandat' => $enTeteMandat[0],
            'second_mandat' => $enTeteMandat[1],
            'third_mandat' => $enTeteMandat[2]
        ]);
    }
}
