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
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
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
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function index(Request $request)
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

        if($request->isXmlHttpRequest())
        {
            $data = $request->request->all();

            if(isset($data['mois']))
            {
                if($data['mois'] == 'periode')
                {
                    $result = $resultByCollaborateur;
                    $resultMandat = $this->mandatHistoriqueRepository
                        ->getAllMandat();
                }
                else
                {
                    $result = $this->userRepository
                        ->getByCollaborateurByMonth($data['mois']);

                    $resultMandat = $this->userRepository
                        ->getMandatByMonth($data['mois']);
                }
            }
            else if(isset($data['trimestre']))
            {
                if($data['trimestre'] == 'periode')
                {
                    $result = $resultByCollaborateur;
                    $resultMandat = $this->mandatHistoriqueRepository
                        ->getAllMandat();
                }
                else
                {
                    $result = $this->userRepository
                        ->getByCollaborateurByTrimester($data['trimestre']);
                    $resultMandat = $this->userRepository
                        ->getMandatByTrimester($data['trimestre']);
                }
            }

            return $this->render('admin/ajax/_filter.html.twig', [
                'results' => $result,
                'mandats' => $resultMandat
            ]);

        }


        return $this->render('admin/challenges/index.html.twig', [
            'results' => $resultByCollaborateur,
            'result_site_mandat' => $resultBySiteForMandat,
            'result_site_vente' => $resultBySiteForVente,
            'result_consolidation' => $resultByConsolidation,
            'mandats' => $mandats
        ]);
    }

    /**
     * @param string $slug
     * @param string|null $mois
     * @param string|null $trimestre
     * @return StreamedResponse
     * @Route("/export-csv/{slug}", name="export-csv-slug")
     * @Route("/export-csv/{slug}/{mois}", name="export-csv-month")
     * @Route("/export-csv/{slug}/trimestre/{trimestre}", name="export-csv-trim")
     */
    public function exportCsv(string $slug, string $mois = null, string $trimestre = null)
    {
        $response = new StreamedResponse();

        if($slug == 'vente')
        {
            $header = [
                'Collaborateurs',
                'Site de rattachement',
                'Nombre de vehicules vendus',
                'Nombre de livraison',
                'Nombre de financement',
                'Nombre de garantie',
                'Nombre de frais de mise en route',
            ];

            if($mois == null && $trimestre == null)
            {
                $results = $this->userRepository
                    ->getByCollaborateur();
            }
            else if($mois != null && $trimestre == null)
            {
                $results = $this->userRepository
                    ->getByCollaborateurByMonth($mois);
            }
            else if($trimestre != null && $mois == null)
            {
                $results = $this->userRepository
                    ->getByCollaborateurByTrimester($trimestre);

            }

            $response->setCallback(function() use ($results, $header){

                $handle = fopen('php://output', 'w+');
                fputcsv($handle,$header, ';');

                $total_vente = 0;
                $total_livraison = 0;
                $total_financement = 0;
                $total_garantie = 0;
                $total_fme = 0;

                foreach($results as $result)
                {
                    $total_vente +=  $result['vente'];
                    $total_livraison +=  $result['livree'];
                    $total_financement +=  $result['financement'];
                    $total_garantie +=  $result['garantie'];
                    $total_fme +=  $result['fraisMER'];

                    $collaborateurs = $result['prenom'] . ' ' . $result['nom'];

                    fputcsv(
                        $handle,
                        [
                            $collaborateurs,
                            $result['site_rattachement'],
                            $result['vente']  ?? 0,
                            $result['livree'] ?? 0,
                            $result['financement']  ?? 0,
                            $result['garantie']  ?? 0,
                            $result['fraisMER']  ?? 0,
                        ],
                        ';'
                    );
                }

                echo "\n".'"TOTAL'.'"; ;"'.$total_vente.'";"'.$total_livraison.'";"'.$total_financement.'";"'.$total_garantie.'";"'.$total_fme.'"';

                fclose($handle);

            });

            $response->setStatusCode(200);
            $response->headers->set('Content-Type', 'text/csv; charset=utf-8');
            $response->headers->set('Content-Disposition','attachment; filename="export-vente.csv"');

        }

        if($slug == 'mandat')
        {
            $header = [
                'Collaborateurs',
                'Site de rattachement',
                'Nombre de mandats',
            ];

            if($mois == null && $trimestre == null)
            {
                $results = $this->mandatHistoriqueRepository
                    ->getAllMandat();
            }
            else if($mois != null && $trimestre == null)
            {
                $results = $this->userRepository
                    ->getMandatByMonth($mois);
            }
            else if($trimestre != null && $mois == null)
            {
                $results = $this->userRepository
                    ->getMandatByTrimester($trimestre);

            }

            $response->setCallback(function() use ($results, $header){

                $handle = fopen('php://output', 'w+');
                fputcsv($handle,$header, ';');

                $total = 0;

                foreach($results as $result)
                {
                    $total +=  $result['nombre'];
                    $collaborateurs = $result['prenom'] . ' ' . $result['nom'];

                    fputcsv(
                        $handle,
                        [
                            $collaborateurs,
                            $result['site_rattachement'],
                            $result['nombre']  ?? 0
                        ],
                        ';'
                    );
                }

                echo "\n".'"TOTAL'.'"; ;"'.$total.'"';

                fclose($handle);

            });

            $response->setStatusCode(200);
            $response->headers->set('Content-Type', 'text/csv; charset=utf-8');
            $response->headers->set('Content-Disposition','attachment; filename="export-mandat.csv"');
        }


        return $response;
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
