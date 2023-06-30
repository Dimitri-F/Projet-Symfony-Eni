<?php

namespace App\Controller;

use App\Form\ProfileManagerType;
use App\Repository\ParticipantRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ProfileController extends AbstractController
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    #[Route('/profile/details/{id}',
        name: 'details_profile',
        requirements: ["id" => "\d+"]
    )]
    public function profileDetails($id, ParticipantRepository $participantRepository, ): Response
    {
        $participant = $participantRepository->find($id);

        if ($participant === null) {
            // Redirige ou gère le cas où le participant n'est pas trouvé
            return $this->redirectToRoute('app_home');
        }

        return $this->render('profile/detailsProfile.html.twig', [
            "participant" => $participant
        ]);
    }

    #[Route('/profile/{id}',
        name: 'manage_profile',
        requirements: ["id" => "\d+"]
    )]
    public function manageProfile($id, EntityManagerInterface $entityManager, ParticipantRepository $participantRepository, UserRepository $userRepository, Request $request): Response
    {
        $user = $this->getUser();
        $userId = $user->getId();
        // Récupère la liste des utilisateurs existants
        $pseudoList = array_map(function($user) {
            return $user->getPseudo();
        }, $userRepository->findAll());

        // Vérifie si l'utilisateur a un rôle d'administrateur
        $isAdmin = in_array('ROLE_ADMIN', $user->getRoles());

        if ($userId != $id && !$isAdmin) {
            // Redirige l'utilisateur non autorisé vers une page d'erreur ou une autre action appropriée
            return $this->redirectToRoute('app_home');
        }

        $participant = $participantRepository->find($id);

        if ($participant === null) {
            // Redirige ou gère le cas où le participant n'est pas trouvé
            return $this->redirectToRoute('app_home');
        }

        if ($participant->getSite() !== null) {
            $participantSite = $participant->getSite()->getNom();
            $participant->getSite()->setNom($participantSite);
        }

        $profileForm = $this->createForm(ProfileManagerType::class, $participant);
        $profileForm->handleRequest($request);

        //Validation du formulaire
        if ($profileForm->isSubmitted() && $profileForm->isValid()){
            $pseudo = $request->request->get("pseudo");
            $email = $request->request->get("email");
            $password = $request->request->get("password2");

            // Vérifie si le pseudo existe déjà
            if (!in_array($pseudo, $pseudoList)) {
                // Hacher le mot de passe
                $hashedPassword =  $this->passwordHasher->hashPassword($user, $password);

                // Définir le mot de passe haché sur le participant
                $user->setPseudo($pseudo);
                $user->setPassword($hashedPassword);

                $user->setEmail($email);

                $entityManager->persist($user);
                $entityManager->persist($participant);
                $entityManager->flush();

                $this->addFlash('success', 'Profil modifié avec succès!');

                return $this->redirectToRoute('details_profile', ['id'=>$participant->getId()]);
            } else {
                $this->addFlash('error', 'Le pseudo existe déjà. Veuillez en choisir un autre.');
            }
        }

        return $this->render('profile/manageProfile.html.twig', [
            "profileForm" => $profileForm->createView(),
            "participant" => $participant,
        ]);
    }
}
