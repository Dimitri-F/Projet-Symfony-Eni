<?php

namespace App\Controller;

// Importation des classes nécessaires
use App\Entity\Inscription;
use App\Entity\Participant;
use App\Form\FilterHomeType;
use App\Form\UpdateInscriptionType;
use App\Repository\EtatRepository;
use App\Repository\InscriptionRepository;
use App\Repository\ParticipantRepository;
use App\Repository\SiteRepository;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;
use mysql_xdevapi\Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\HttpFoundation\Request;

// Classe MainController héritant de AbstractController
class MainController extends AbstractController
{
    #[Route('/home', name: 'app_home')]
    // Cette méthode est utilisée pour le rendu de la page d'accueil
    public function home(Request $request, SiteRepository $siteRepository, SortieRepository $sortieRepository,
                         EtatRepository $etatRepository, ParticipantRepository $participantRepository, InscriptionRepository $inscriptionRepository, EntityManagerInterface $entityManager): Response
    {
        // Récupération des données de requête
        $inscrie = $request->query->get('inscrie');
        $desister = $request->query->get('desister');
        $userId = $this->getUser()->getId();

        // tableau id de sortie si user est inscrit
        $inscriptionsUser = [];

        // Définition de l'heure en France
        $heureFrance = new \DateTime('now', new \DateTimeZone('Europe/Paris'));

        $heureFrance = \DateTime::createFromFormat(
            'Y-m-d H:i:s',
            $heureFrance->format('Y-m-d H:i:s'),
            new \DateTimeZone('UTC')
        );

        // Bloc d'inscription à une sortie
        if ($inscrie !== null) {
            $sortie = $sortieRepository->find($inscrie);
            $participantInscrie = $participantRepository->find($this->getUser()->getId());

            if ($sortie && $participantInscrie) {
                $inscription = $participantInscrie->getInscriptions()->filter(
                    function (Inscription $inscription) use ($sortie) {
                        return $inscription->getSorties()->contains($sortie);
                    }
                )->first();

                if ($inscription === false && count($sortie->getInscriptions()) < $sortie->getNbInscriptionsMax()) { // Si l'utilisateur n'est pas déjà inscrit à la sortie et que la limite n'est pas atteinte
                    // Création de l'inscription
                    $inscription = new Inscription();
                    $inscription->setDateInscription($heureFrance);
                    $inscription->addSorty($sortie);
                    $inscription->addParticipant($participantInscrie);
                    $entityManager->persist($inscription);
                    $entityManager->flush();
                    $test = $inscription->getSorties()->first();
                    if ($test !== false) { // Vérifie que la sortie existe
                        $inscriptionsUser[] = $test->getId(); // Ajoute l'ID de la sortie au tableau
                    }
                    $this->addFlash('success', 'Vous êtes bien inscrit à la sortie');
                }
            }
        }


        // Bloc de désinscription d'une sortie
        if($desister != null){
            $sortie = $sortieRepository->find($desister);
            $participantDesinscrie = $participantRepository->find($this->getUser()->getId());

            if($sortie && $participantDesinscrie){
                $inscription = $participantDesinscrie->getInscriptions()->filter(
                    function(Inscription $inscription) use ($sortie) {
                        return $inscription->getSorties()->contains($sortie);
                    }
                )->first();

                if ($inscription instanceof Inscription) {
                    // Suppression de l'inscription si elle existe
                    $entityManager->remove($inscription);
                    $entityManager->flush();
                    $this->addFlash('success', 'Vous vous êtes bien désisté de la sortie');
                }
            }
        }


        // Récupération de toutes les sorties, états et participants
        $sorties = $sortieRepository->findAll();
        $etats = $etatRepository->findAll();
        $participants = $participantRepository->findAll();


        //Vérification des Etats
        foreach ($sorties as $sortie){
            $dateDebut = clone $sortie->getDateDebut();
            $dateDebutAddTime = clone $sortie->getDateDebut();

            $dateDebutAddTime->add(new \DateInterval('PT'.$sortie->getDuree().'S'));

            $dateCloture = clone $sortie->getDateCloture();
            $dateCloture->setTime(0, 0, 0);

            $heureFranceSansHeure = clone $heureFrance;
            $heureFranceSansHeure->setTime(0, 0, 0);


            if ($dateCloture <= $heureFranceSansHeure){
                $sortie->setEtat($etatRepository->find(3));
                $entityManager->persist($sortie);
            }
            if ($dateDebut <= $heureFrance && $dateDebutAddTime >= $heureFrance){
                $sortie->setEtat($etatRepository->find(4));
                $entityManager->persist($sortie);
            }
            if ($dateDebutAddTime < $heureFrance){
                $sortie->setEtat($etatRepository->find(5));
                $entityManager->persist($sortie);
            }
        }
        //envois bdd si new etat
        if ($entityManager){
            $entityManager->flush();
            $etats = $etatRepository->findAll();
        }



        // Quelle sortie participe l'utilisateur
        $participant = $participantRepository->find($userId);
        if ($participant !== null) {
            $inscriptions = $participant->getInscriptions();
            foreach ($inscriptions as $inscription) {
                $sortie = $inscription->getSorties()->first(); // Récupère la première sortie associée à l'inscription
                if ($sortie !== false) { // Vérifie que la sortie existe
                    $inscriptionsUser[] = $sortie->getId(); // Ajoute l'ID de la sortie au tableau
                }
            }
            // Récupération des données de l'utilisateur
            $userData = $participantRepository->find($userId);
            if($userData){
                $user = [
                    'id' => $userId,
                    'nom' => $userData->getNom(),
                    'prenom' => $userData->getPrenom(),
                ];
            }
        }else{
            $user = [
                'id' => 0,
                'nom' => "NULE",
                'prenom' => "BITCH",
            ];
        }

        // Nombre d'inscription total
        $inscriptionsTotals = [];
        foreach ($sorties as $sortie) {
            $sortieId = $sortie->getId();
            $inscriptions = $sortie->getInscriptions();
            $inscriptionsTotals[$sortieId] = count($inscriptions);
        }

        // Création et gestion du formulaire
        $form = $this->createForm(FilterHomeType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Récupération des données filtrées et recherche des sorties correspondantes
            $data = $form->getData();
            $data['userId'] = $userId;
            $data['inscrit'] = $inscriptionsUser;
            if($data['dateEnd'] != null){
                $data['dateEnd']->setTime(23, 59, 59);
            }
            $sorties = $sortieRepository->findFilteredSorties($data, $data['isOrganisateur'], $data['isInscrit'], $data['isNotInscrit'], $data['isPassed']);
        }


        // Rendu de la vue avec les données nécessaires
        return $this->render('main/home.html.twig', [
            'filterForm' => $form->createView(),
            'sorties' => $sorties,
            'etats' => $etats,
            'participants' => $participants,
            'user' => $user,
            'inscriptions' => $inscriptionsUser,
            'inscriptionsTotals' => $inscriptionsTotals,
            'dateNow' => $heureFrance,
        ]);
    }
}
