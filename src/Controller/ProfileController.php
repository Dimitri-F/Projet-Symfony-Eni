<?php

namespace App\Controller;

use App\Repository\ParticipantRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class ProfileController extends AbstractController
{

    #[Route('/profile', name: 'app_profile')]
    public function manageProfile(ParticipantRepository $participantRepository): Response
    {
//        $participant = $participantRepository->find($id);

        return $this->render('profile/manageProfile.html.twig');
    }
}
