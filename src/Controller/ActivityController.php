<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Entity\Etat;
use App\Form\CreateActivityType;
use App\Repository\ParticipantRepository;
use App\Repository\SiteRepository;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ActivityController extends AbstractController
{

    #[Route('/create', name: 'app_create')]
    public function index(EntityManagerInterface $entityManager, Request $request,ParticipantRepository $participantRepository, SiteRepository $siteRepository): Response
    {
        $userId = $this->getUser()->getId();
        $participant = $participantRepository->find($userId);
        $sites = $siteRepository->findAll();
        try {
            $activity = new Sortie();
            if($sites = $participant->getSite()){
                $activity->setSite($sites);
            }
            $activityForm = $this->createForm(CreateActivityType::class, $activity);
            $activityForm->handleRequest($request);
            if ($activityForm->isSubmitted() && $activityForm->isSubmitted()) {
                $activity->setOrganisateur($userId);
                if($request->request->has('save')){
                    $etatId = 1;
                    $etat = $entityManager->getReference(Etat::class, $etatId);
                    $activity->setEtat($etat);
                    $activity->setEtatSortie($etatId);
                }elseif ($request->request->has('publish')){
                    $etatId = 2;
                    $etat = $entityManager->getReference(Etat::class, $etatId);
                    $activity->setEtat($etat);
                    $activity->setEtatSortie($etatId);
                }
                $entityManager->persist($activity);
                $entityManager->flush();
                $this->addFlash('success', 'La sortie a bien été créee dans la base de données');
                return $this->redirectToRoute('app_home');
            }
        }catch (Exception $exception){
            $this->addFlash('danger','Erreur lors de la création de la sortie');
        }
        return $this->render('activity/create.html.twig', [
            "activityForm" => $activityForm->createView(),
        ]);
    }

    #[Route('remove/{id}','app_remove')]
    public function remove($id, SortieRepository $sortieRepository, EntityManagerInterface $entityManager, Request $request): Response
    {
        $activity = $sortieRepository->find($id);

        if ($request->request->has('save')) {
            try {
                $etatId = 6;
                $etat = $entityManager->getReference(Etat::class, $etatId);
                $activity->setEtat($etat);
                $activity->setEtatSortie($etatId);
                $entityManager->persist($activity);
                $entityManager->flush();
                $this->addFlash('success', 'La sortie a bien été supprimée');
                return $this->redirectToRoute('app_home');
            } catch (Exception $exception) {
                $this->addFlash('danger', 'Erreur lors de la suppression de la sortie');
            }
        }

        return $this->render('activity/remove-activity.html.twig', [
            "activity" => $activity,

        ]);
    }
    #[Route('/details/{id}','app_details')]
    public function details($id,SortieRepository $sortieRepository,ParticipantRepository $participantRepository): Response
    {
        $activity = $sortieRepository->find($id);
        $participantsInscrits = $participantRepository->findParticipantsInscrits($id);

        return $this->render('activity/details.html.twig',[
            "activity" => $activity,
            "participants" => $participantsInscrits,
        ]);
    }
}
