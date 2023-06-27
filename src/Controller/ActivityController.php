<?php

namespace App\Controller;


use App\Entity\Sortie;
use App\Entity\Lieu;
use App\Form\CreateActivityType;
use App\Form\LieuType;
use App\Form\VilleType;
use App\Repository\VilleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ActivityController extends AbstractController
{
    #[Route('/create', name: 'app_create')]
    public function index(EntityManagerInterface $entityManager, Request $request): Response
    {
        try {
            $activity = new Sortie();

            $activityForm = $this->createForm(CreateActivityType::class, $activity);
            $activityForm->handleRequest($request);
            if ($activityForm->isSubmitted() && $activityForm->isSubmitted()) {
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
}
