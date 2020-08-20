<?php

namespace App\Controller;

use App\Entity\Challenge;
use App\Form\ChallengeType;
use App\Repository\ChallengeRepository;
use App\Repository\MandatHistoricRepository;
use App\Repository\MandatRepository;
use App\Repository\UserRepository;
use App\Repository\VenteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @IsGranted("ROLE_ADMIN")
 */
class AdminController extends AbstractController
{

    /**
     * @var UserRepository
     */
    private $userRepository;


    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var ChallengeRepository
     */
    private $challengeRepository;


    /**
     * @var MandatHistoricRepository
     */
    private $mandatHistoriqueRepository;

    public function __construct(UserRepository $userRepository, MandatHistoricRepository $mandatHistoriqueRepository, EntityManagerInterface $em, ChallengeRepository $challengeRepository)
    {
        $this->userRepository = $userRepository;
        $this->mandatHistoriqueRepository = $mandatHistoriqueRepository;
        $this->challengeRepository = $challengeRepository;
        $this->em = $em;
    }


    /**
     * @Route("/admin", name="admin")
     */
    public function index()
    {
        $user = $this->getUser();

        if($user == null)
        {
            return $this->redirectToRoute('home');
        }

        $resultByCollaborateur = $this->userRepository
            ->getByCollaborateur();

        $resultBySiteForMandat = $this->userRepository
            ->getBySiteForMandat();

        $resultBySiteForVente = $this->userRepository
            ->getBySiteForVente();

        $resultByConsolidation = $this->userRepository
            ->getByConsolidation();

        $mandats = $this->mandatHistoriqueRepository
            ->getAllMandat();


        return $this->render('admin/challenges/index.html.twig', [
            'result_collaborateur' => $resultByCollaborateur,
            'result_site_mandat' => $resultBySiteForMandat,
            'result_site_vente' => $resultBySiteForVente,
            'result_consolidation' => $resultByConsolidation,
            'mandats' => $mandats
        ]);
    }

    /**
     * @Route("/admin/challenges", name="admin-challenges")
     */
    public function viewChallenge()
    {
        $challenges = $this->challengeRepository
            ->findAll();

        return $this->render('admin/challenges/challenges.html.twig', [
            'challenges' => $challenges
        ]);

    }

    /**
     * @Route("/admin/challenge/create", name="admin-challenge-new")
     * @param Request $request
     */
    public function createChallenge(Request $request)
    {
        $user = $this->getUser();

        if($user == null)
        {
            return $this->redirectToRoute('home');
        }

        $challenge = new Challenge();

        $form = $this->createForm(ChallengeType::class, $challenge);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $this->em->persist($challenge);
            $this->em->flush();
            $this->addFlash('success', 'Challenge crée avec succès');
            return $this->redirectToRoute('admin-challenges');
        }

        return $this->render('admin/challenges/new.html.twig', [
            'challenge' => $challenge,
            'form' => $form->createView()
        ]);


    }

    /**
     * @Route("/admin/challenge/{id}", name="admin-challenge-edit", methods="GET|POST")
     * @param Challenge $challenge
     * @param Request $request
     */
    public function editChallenge(Challenge $challenge, Request $request)
    {
        $user = $this->getUser();

        if($user == null)
        {
            return $this->redirectToRoute('home');
        }

        $form = $this->createForm(ChallengeType::class, $challenge);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $this->em->flush();
            $this->addFlash('success','Challenge modifié avec succès');
            return $this->redirectToRoute('admin-challenges');
        }

        return $this->render('admin/challenges/edit.html.twig',[
            'challenge' => $challenge,
            'form' => $form->createView()
        ]);

    }

    /**
     * @Route("/admin/challenge/{id}", name="admin-challenge-delete", methods="DELETE")
     * @param Challenge $challenge
     * @param Request $request
     * @return RedirectResponse
     */
    public function deleteChallenge(Challenge $challenge, Request $request)
    {
        if($this->isCsrfTokenValid('delete'.$challenge->getId(), $request->get('_token')))
        {
            $this->em->remove($challenge);
            $this->em->flush();
            $this->addFlash('success','Challenge supprimé avec succès');
        }

        return $this->redirectToRoute('admin-challenges');
    }

    /**
     * @Route("/admin/miscellaneous", name="admin-miscellaneous")
     */
    public function miscellaneousOperation()
    {
        $users = $this->userRepository
            ->findAll();

        return $this->render('admin/miscellaneous.html.twig',[
            'users' => $users
        ]);
    }

    /**
     * @Route("/admin/miscellaneous/add", name="admin-miscellaneous-add")
     * @param Request $request
     * @param MandatRepository $mandatRepository
     * @param VenteRepository $venteRepository
     * @return JsonResponse
     */
    public function addElement(Request $request, MandatRepository $mandatRepository, VenteRepository $venteRepository)
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

        $mandats = $mandatRepository
            ->findAll();

        $ventes = $venteRepository
            ->findAll();

        if($data['mandat'] != "")
        {
            foreach($mandats as $mandat)
            {
                $mandat->setNombre(0);
            }
        }

        if($data['vente'] != "")
        {
            foreach($ventes as $vente)
            {
                $this->em->remove($vente);
            }

            $this->em->flush();
        }

        if($data['name'] != 'Collaborateurs')
        {
            $newData = explode(" ", $data['name']);
            $newAdmin = $this->userRepository
                ->findOneBy(['prenom' => $newData['0']]);

            $newAdmin->setRoles('ROLE_ADMIN');
            $this->em->flush();
        }


        return $this->json(['code' => 200, 'message' => 'Données postées'], 200);
    }


}
