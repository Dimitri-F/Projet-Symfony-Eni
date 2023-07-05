<?php

namespace App\Controller;

use App\Repository\InscriptionRepository;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Repository\ParticipantRepository;
use App\Repository\UserRepository;
use App\Form\UserManagementType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted("ROLE_ADMIN")]
#[Route('/admin', name: 'app_')]
class AdminController extends AbstractController
{
    #[Route('', name: 'admin')]
    public function admin(UserRepository $userRepository, ParticipantRepository $participantRepository): Response
    {

        $participants = $participantRepository->findAll();

        $form = $this->createForm(UserManagementType::class);

        return $this->render('admin/admin.html.twig', [
            'participants' => $participants,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/delete/{id}', name: 'delete', requirements: ["id" => "\d+"])]
    public function delete(ParticipantRepository $participantRepository, UserRepository $userRepository, InscriptionRepository $inscriptionRepository,
                           EntityManagerInterface $entityManager,SortieRepository $sortieRepository, $id): Response
    {
        // Récupérer le participant à partir de l'ID
        $participant = $participantRepository->find($id);

        if ($participant) {
            // Obtenez toutes les inscriptions pour ce participant
            $inscriptions = $participant->getInscriptions();

            // Parcourez les inscriptions et supprimez-les
            foreach ($inscriptions as $inscription) {
                $entityManager->remove($inscription);
            }

            // Obtenez toutes les sorties pour ce participant
            $sorties = $sortieRepository->findBy(['organisateur' => $participant]);

            // Parcourez les sorties et supprimez-les
            foreach ($sorties as $sortie) {
                $entityManager->remove($sortie);
            }

            // Supprimer le participant et l'utilisateur
            $entityManager->remove($participant);
            $entityManager->remove($participant->getCompte());

            $entityManager->flush();

            // Message flash pour indiquer que le participant, l'utilisateur et les inscriptions ont été supprimés
            $this->addFlash('success', 'Participant, utilisateur, sorties et inscriptions supprimés avec succès.');
        } else {
            // Message flash pour indiquer que le participant n'a pas été trouvé
            $this->addFlash('error', 'Participant non trouvé.');
        }

        return $this->redirectToRoute('app_admin');
    }


}
