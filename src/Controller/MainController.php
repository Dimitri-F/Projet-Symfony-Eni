<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Form\FilterHomeType;
use App\Repository\EtatRepository;
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
                         EtatRepository $etatRepository, ParticipantRepository $participantRepository): Response
    {

        $userId = $this->getUser()->getId();
        $userData = $participantRepository->find($userId);
        if($userData){
            $user = [
                'id' => $userId,
                'nom' => $userData->getNom(),
                'prenom' => $userData->getPrenom()
            ];
        }
        dump($user);

        $form = $this->createForm(FilterHomeType::class);
        $form->handleRequest($request);

        $sites = $siteRepository->findAll();
        $sorties = $sortieRepository->findAll();
        $etats = $etatRepository->findAll();
        $participants = $participantRepository->findAll();

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $sorties = $sortieRepository->findFilteredSorties($data);
        }


        return $this->render('main/home.html.twig', [
            'filterForm' => $form->createView(),
            'sites' => $sites,
            'sorties' => $sorties,
            'etats' => $etats,
            'participants' => $participants,
            'user' => $user,
        ]);
    }

}
