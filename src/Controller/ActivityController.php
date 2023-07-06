<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Form\CreateActivityType;
use App\Form\UpdateActivityType;
use App\Repository\EtatRepository;
use App\Repository\ParticipantRepository;
use App\Repository\SiteRepository;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;

class ActivityController extends AbstractController
{

    private RequestStack $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    #[Route('/create', name: 'app_create')]
    public function index(EtatRepository $etatRepository,EntityManagerInterface $entityManager, Request $request,ParticipantRepository $participantRepository, SiteRepository $siteRepository): Response
    {
        $currentRequest = $this->requestStack->getCurrentRequest();

        if($currentRequest->cookies->has('screen_width')) {
            $screenWidth = $currentRequest->cookies->get('screen_width');
        }
        if ($screenWidth < 600){
            return $this->redirectToRoute('app_home');
        }

        $activity = new Sortie();
        $activityForm = $this->createForm(CreateActivityType::class, $activity);
        $activityForm->handleRequest($request);

        try {
            $userId = $this->getUser()->getId();
            $participant = $participantRepository->find($userId);
            if($sites = $participant->getSite()){
                $activity->setSite($sites);
            }

            if ($activityForm->isSubmitted() && $activityForm->isValid()) {
                $activity->setOrganisateur($userId);
                    if ($request->request->has('save')) {
                        $activity->setEtat($etatRepository->find(1));
                    } elseif ($request->request->has('publish')) {
                        $activity->setEtat($etatRepository->find(2));
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
            "activityForm" => $activityForm->createView()
        ]);
    }

    #[Route('remove/{id}','app_remove')]
    public function remove(EtatRepository $etatRepository,$id, SortieRepository $sortieRepository, EntityManagerInterface $entityManager, Request $request): Response
    {
        $activity = $sortieRepository->find($id);

        if ($request->request->has('save')) {
            try {
                $activity->setEtat($etatRepository->find(6));
                $entityManager->persist($activity);
                $entityManager->flush();
                $this->addFlash('success', 'La sortie a bien été annulée');
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

    #[Route('/update/{id}','app_update')]
    public function update($id,Request $request, EntityManagerInterface $entityManager,SortieRepository $sortieRepository,EtatRepository $etatRepository): Response
    {
        $activity = $sortieRepository->find($id);
        $activityForm = $this->createForm(UpdateActivityType::class,$activity);
        $activityForm->handleRequest($request);
        try {
            if($activityForm->isSubmitted() && $activityForm->isValid()){
                $activity->setLieu($activity->getLieu());
                if($request->request->has('save')){
                    $activity->setEtat($etatRepository->find(1));
                }elseif($request->request->has('publish')) {
                    $activity->setEtat($etatRepository->find(2));
                }
                $entityManager->persist($activity);
                $entityManager->flush();
                $this->addFlash('success', 'La sortie a bien été modifiée dans la base de données');
                return $this->redirectToRoute('app_home');
        }
        }catch (Exception $exception){
            $this->addFlash('danger', 'Erreur lors de la modification de la sortie');
        }

        return $this->render('activity/update.html.twig',[
            "activityForm" => $activityForm->createView(),
            "activity" => $activity
        ]);
    }
}
