<?php

namespace App\Controller;

use App\Form\ProfileManagerType;
use App\Repository\ParticipantRepository;
use App\Repository\UserRepository;
use App\Service\FileUploaderService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
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
    public function manageProfile($id, EntityManagerInterface $entityManager,
                                  ParticipantRepository $participantRepository,
                                  UserRepository $userRepository,
                                  Request $request,
                                  FileUploaderService $fileUploaderService,
    ): Response
    {
        $user = $this->getUser();
        if (!$user) {
            // Redirection vers la page de connexion
            return $this->redirectToRoute('app_login');
        }
        $userId = $user->getId();

        // Récupère la liste des pseudos des utilisateurs existants
        $pseudoList = array_map(function($user) {
            return $user->getPseudo();
        }, $userRepository->findAll());
        $emailList = array_map(function ($user) {
            return $user->getEmail();
        }, $userRepository->findAll());
        $uploadFailed = false;

        // Vérifie si l'utilisateur a un rôle d'administrateur
        $isAdmin = in_array('ROLE_ADMIN', $user->getRoles());

        if ($userId != $id && !$isAdmin) {
            // Redirige l'utilisateur non autorisé
            return $this->redirectToRoute('app_home');
        }else{
            $participant = $participantRepository->find($id);
            $participantId = $participant->getId();
            $userToModify = $userRepository->find($participantId);

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
                $password = $request->request->get("password");
                $password2 = $request->request->get("password2");

                // Vérifie si le pseudo existe déjà
                if (!in_array($pseudo, $pseudoList) && !in_array($email, $emailList)) {
                    if($password===$password2){
                        // Hacher le mot de passe
                        $hashedPassword =  $this->passwordHasher->hashPassword($user, $password);

                        $userToModify->setPseudo($pseudo);
                        // Définis le mot de passe hashé sur le participant
                        $userToModify->setPassword($hashedPassword);

                        $userToModify->setEmail($email);

                        // Upload de la photo de profil
                        $photoFile = $profileForm->get('urlPhoto')->getData();
                        if ($photoFile) {
                            try{
                                $uploadedFileName = $fileUploaderService->upload($photoFile);
                                // Mets à jour le chemin de la photo de profil dans l'entité Participant
                                $participant->setUrlPhoto($uploadedFileName);
                            }catch(FileException $e){
                                $uploadFailed = true;
                            }
                        }

                        if ($uploadFailed) {
                            $this->addFlash('error', 'Une erreur s\'est produite lors de l\'upload de la photo de profil.');
                            // Annuler d'autres opérations liées à la modification du profil, si nécessaire
                            return $this->redirectToRoute('details_profile', ['id' => $participant->getId()]);
                        }else{
                            try{
                                $entityManager->persist($user);
                                $entityManager->persist($participant);
                                $entityManager->flush();

                                $this->addFlash('success', 'Profil modifié avec succès!');
                                return $this->redirectToRoute('details_profile', ['id'=>$participant->getId()]);
                            }catch(\Exception $e){
                                // En cas d'erreur lors de la persistance, affiche un message d'erreur
                                $errorMessage = "Une erreur s'est produite, l'utilisateur n'a pas été crée.";
                                $this->addFlash('error', $errorMessage);

                                return $this->redirectToRoute('manage_profile', ['id'=>$participant->getId()] );
                            }
                        }
                    }else{
                        $this->addFlash('error', 'Les mot de passe ne sont pas identiques.');
                    }
                } else {
                    $this->addFlash('error', 'Pseudo ou email déjà existants.');
                }
            }
        }

        return $this->render('profile/manageProfile.html.twig', [
            "profileForm" => $profileForm->createView(),
            "participant" => $participant,
        ]);
    }
}
