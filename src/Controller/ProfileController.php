<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Entity\User;
use App\Form\ProfileManagerType;
use App\Repository\ParticipantRepository;
use App\Repository\SiteRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class ProfileController extends AbstractController
{

    #[Route('/profile/details/{id}',
        name: 'details_profile',
        requirements: ["id" => "\d+"]
    )]
    public function profileDetails($id, ParticipantRepository $participantRepository): Response
    {
        $participant = $participantRepository->find($id);

        return $this->render('profile/detailsProfile.html.twig', [
            "participant" => $participant
        ]);
    }

    #[Route('/profile/{id}',
        name: 'manage_profile',
        requirements: ["id" => "\d+"]
    )]
    public function manageProfile(EntityManagerInterface $entityManager,
                                  ParticipantRepository $participantRepository,
                                  SiteRepository $siteRepository,
                                    Request $request
    ): Response
    {
        $user = $this->getUser();

        $userId = $user->getId();

        $participant = $participantRepository->find($userId);

        $profileForm = $this->createForm(ProfileManagerType::class, $participant);
        $profileForm->handleRequest($request);

        if ($profileForm->isSubmitted() && $profileForm->isValid()){
            $pseudo = $request->request->get("pseudo");
            $email = $request->request->get("email");
            $password = $request->request->get("password2");
//          $site = $request->request->get("");

//            $formData = $profileForm->getData();

//            $participant->setPrenom($formData->getPrenom());
//            $participant->setNom($formData->getNom());
//            $participant->setTelephone($formData->getTelephone());
//            $participant->setSite($formData->getSite()->getNom());
            $user->setPseudo($pseudo);
            $user->setEmail($email);
            $user->setPassword($password);

//            $site = $participant->getSite();
//            $site->setNom($formData->getSite()->getNom());

            $entityManager->persist($user);
            $entityManager->persist($participant);
            $entityManager->flush();

            return $this->redirectToRoute('details_profile', ['id'=>$participant->getId()]);
        }

        return $this->render('profile/manageProfile.html.twig', [
            "profileForm" => $profileForm->createView(),
            "participant" => $participant,
        ]);
    }

}
