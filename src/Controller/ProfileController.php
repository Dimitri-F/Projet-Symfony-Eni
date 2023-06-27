<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Entity\User;
use App\Form\ProfileManagerType;
use App\Repository\ParticipantRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class ProfileController extends AbstractController
{

    #[Route('/profile', name: 'app_profile')]
    public function manageProfile(EntityManagerInterface $entityManager,
                                  ParticipantRepository $participantRepository,
                                    Request $request
    ): Response
    {
        $participant = new Participant();
        $user = new User();
        $profileForm = $this->createForm(ProfileManagerType::class, $participant);
        $profileForm->handleRequest($request);
//        $participant = $participantRepository->find($id);

        return $this->render('profile/manageProfile.html.twig', [
            "profileForm" => $profileForm->createView(),
            "participant" => $participant,
        ]);
    }
}
