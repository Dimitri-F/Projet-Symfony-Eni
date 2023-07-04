<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Entity\Site;
use App\Entity\User;
use App\Form\RegistrationCSVType;
use App\Form\RegistrationFormType;
use App\Repository\SiteRepository;
use App\Repository\UserRepository;
use App\Security\AppAuthenticator;
use App\Security\EmailVerifier;
use App\Service\CsvImporterService;
use Doctrine\ORM\EntityManagerInterface;
use mysql_xdevapi\Exception;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

class RegistrationController extends AbstractController
{
    private EmailVerifier $emailVerifier;

    public function __construct(EmailVerifier $emailVerifier)
    {
        $this->emailVerifier = $emailVerifier;
    }
    #[IsGranted("ROLE_ADMIN")]
    #[Route('/register', name: 'app_register')]
    public function register(Request $request,
                             UserPasswordHasherInterface $userPasswordHasher,
                             EntityManagerInterface $entityManager,
                             SiteRepository $siteRepository,
                             CsvImporterService $csvImporterService,
                             UserRepository $userRepository,
    ): Response
    {
        $user = new User();
        $participant = new Participant();
        // Récupère la liste des pseudos des utilisateurs existants
        $pseudoList = array_map(function($user) {
            return $user->getPseudo();
        }, $userRepository->findAll());
        $emailList = array_map(function ($user) {
            return $user->getEmail();
        }, $userRepository->findAll());

        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        $formCSV = $this->createForm(RegistrationCSVType::class);
        $formCSV->handleRequest($request);

        //enregistrement d'un utilisateur par soumission et validation du formulaire
        if ($form->isSubmitted() && $form->isValid()) {
            $pseudo = $form->get('pseudo')->getData();


            if (in_array($pseudo, $pseudoList)) {
                // En cas de pseudo déjà utilisé, affiche un message d'erreur
                $this->addFlash('error', 'Le pseudo  déjà utilisé, veuillez en choisir un autre.');

                // Redirige vers la page de formulaire d'inscription avec les erreurs affichées
                return $this->redirectToRoute('app_register');
            }else{
                // hashe le mot de passe
                $user->setPassword(
                    $userPasswordHasher->hashPassword(
                        $user,
                        $form->get('plainPassword')->getData()
                    )
                );
                $user->setRoles(["ROLE_USER"]);
                $user->setIsVerified(1);

                $lastName = $request->request->get("lastName");
                $firstName = $request->request->get("firstName");
                $tel = $request->request->get("telephone");
                $siteParticipantId = $request->request->get("site");

                $participant->setNom($lastName);
                $participant->setPrenom($firstName);
                $participant->setTelephone($tel);
                $participant->setSite($siteRepository->find($siteParticipantId));

                $participant->setActif(true);

                try {
                    $entityManager->persist($user);
                    $participant->setCompte($user);
                    $entityManager->persist($participant);
                    $entityManager->flush();
                    $this->addFlash('success', "L'utilisateur a été créé avec succès.");

                } catch (\Exception $e) {
                    // En cas d'erreur lors de la persistance, affiche un message d'erreur
                    $errorMessage = "Une erreur s'est produite, l'utilisateur n'a pas été crée.";
                    $this->addFlash('error', $errorMessage);

                    //redirige vers la page de formulaire d'inscription avec les erreurs affichées
                    return $this->redirectToRoute('app_register');
                }

//            // generate a signed url and email it to the user
//            $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
//                (new TemplatedEmail())
//                    ->from(new Address('contact@sorties.com', 'Sorties Bot'))
//                    ->to($user->getEmail())
//                    ->subject('Please Confirm your Email')
//                    ->htmlTemplate('registration/confirmation_email.html.twig')
//            );
//            // do anything else you need here, like send an email
            }

        }

        //enregistrement d'un utilisateur par upload d'un fichier CSV
        if ($formCSV->isSubmitted() && $formCSV->isValid()) {

            $csvFile = $formCSV->get('csvFile')->getData();

            // Vérifie si un fichier a été uploadé
            if ($csvFile !== null) {

                // Utiliser le service CsvImporterService pour importer le fichier CSV
                $csvFileData = $csvImporterService->importCsv($csvFile->getPathname());

                foreach ($csvFileData as $rowData) {

                    $pseudo = $rowData["pseudo"];
                    $email = $rowData["email"];

                    if (!in_array($pseudo, $pseudoList) && !in_array($email, $emailList)) {

                        $siteName = $rowData["Site"];
                        $site = $siteRepository->findOneBy(['nom' => $siteName]);

                        $user = new User();
                        $user->setRoles(["ROLE_USER"]);
                        $user->setIsVerified(1);
                        $user->setPassword(
                            $userPasswordHasher->hashPassword(
                                $user,
                                $rowData["password"]
                            )
                        );
                        $user->setPseudo($rowData["pseudo"]);
                        $user->setEmail($rowData["email"]);

                        $participant = new Participant();
                        $participant->setNom($rowData["lastname"]);
                        $participant->setPrenom($rowData["firstname"]);
                        $participant->setTelephone($rowData["telephone"]);
                        $participant->setSite($site);
                        $participant->setActif(true);
                        $participant->setCompte($user);

                        try {
                            $entityManager->persist($user);
                            $entityManager->persist($participant);
                            $entityManager->flush();
                        } catch (\Exception $e) {
                            $errorMessage = "Une erreur s'est produite lors de la création de l'utilisateur.";
                            $this->addFlash('error', $errorMessage);
                            return $this->redirectToRoute('app_register');
                        }
                    }
                }
            } else {
                // En cas de fichier non téléchargé, affiche un message d'erreur
                $errorMessage = "Veuillez télécharger un fichier CSV pour l'enregistrement.";
                $this->addFlash('error', $errorMessage);

                // Redirige vers la page de formulaire d'inscription avec les erreurs affichées
                return $this->redirectToRoute('app_register');
            }
            $this->addFlash('success', 'Les utilisateurs ont été créés avec succès.');

        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
            'registrationCSV' => $formCSV->createView()
        ]);
    }

    #[Route('/verify/email', name: 'app_verify_email')]
    public function verifyUserEmail(Request $request, TranslatorInterface $translator): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        // validate email confirmation link, sets User::isVerified=true and persists
        try {
            $this->emailVerifier->handleEmailConfirmation($request, $this->getUser());
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('verify_email_error', $translator->trans($exception->getReason(), [], 'VerifyEmailBundle'));

            return $this->redirectToRoute('app_register');
        }

        // @TODO Change the redirect on success and handle or remove the flash message in your templates
        $this->addFlash('success', 'Your email address has been verified.');

        return $this->redirectToRoute('app_register');
    }
}
