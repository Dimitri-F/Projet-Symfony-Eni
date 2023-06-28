<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Form\FilterHomeType;
use App\Repository\EtatRepository;
use App\Repository\InscriptionRepository;
use App\Repository\ParticipantRepository;
use App\Repository\SiteRepository;
use App\Repository\SortieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\HttpFoundation\Request;


class MainController extends AbstractController
{
    #[Route('/home', name: 'app_home')]
    public function home(Request $request, SiteRepository $siteRepository, SortieRepository $sortieRepository,
                         EtatRepository $etatRepository, ParticipantRepository $participantRepository, InscriptionRepository $inscriptionRepository): Response
    {
        $sites = $siteRepository->findAll();
        $sorties = $sortieRepository->findAll();
        $etats = $etatRepository->findAll();
        $participants = $participantRepository->findAll();

        $userId = $this->getUser()->getId();
        $userData = $participantRepository->find($userId);
        if($userData){
            $user = [
                'id' => $userId,
                'nom' => $userData->getNom(),
                'prenom' => $userData->getPrenom(),
            ];
        }

        // quelle sortie participe user
        $participant = $participantRepository->find($userId);
        $inscriptions = $participant->getInscriptions();
        $inscriptionsUser = [];
        foreach ($inscriptions as $inscription) {
            foreach($inscription->getSorties() as $sortie) {
                $sortieIds = $sortie->getId();
            }
            $inscriptionsUser[] = $sortieIds;
        }


        // nombre d'inscription total
        $inscriptionsTotals = []; // tableau pour stocker le nombre de participants pour chaque sortie

        foreach ($sorties as $sortie) {
            $sortieId = $sortie->getId(); // récupère l'id de la sortie
            $inscriptions = $sortie->getInscriptions(); // récupère toutes les inscriptions pour la sortie

            $inscriptionsTotals[$sortieId] = count($inscriptions); // stocke le nombre total de participants pour la sortie
        }

        dump($inscriptionsUser);
        dump($inscriptions);

        $form = $this->createForm(FilterHomeType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $data['userId'] = $userId;
            $data['inscrit'] = $inscriptionsUser;
            $isOrganisateur = $data['isOrganisateur'];
            $isInscrit = $data['isInscrit'];
            $isNotInscrit = $data['isNotInscrit'];
            $isPassed = $data['isPassed'];


            $sorties = $sortieRepository->findFilteredSorties($data, $isOrganisateur, $isInscrit, $isNotInscrit, $isPassed);
        }



        return $this->render('main/home.html.twig', [
            'filterForm' => $form->createView(),
            'sites' => $sites,
            'sorties' => $sorties,
            'etats' => $etats,
            'participants' => $participants,
            'user' => $user,
            'inscriptions' => $inscriptionsUser,
            'inscriptionsTotals' => $inscriptionsTotals,
        ]);
    }

}
