<?php

namespace App\Controller;

use App\Form\FilterCityType;
use App\Form\LieuType;
use App\Entity\Lieu;
use App\Form\UpdatePlaceType;
use App\Repository\LieuRepository;
use App\Repository\VilleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LieuController extends AbstractController
{
    #[Route('/lieu/create','app_create_lieu')]
    public function create(EntityManagerInterface $entityManager,Request $request): Response
    {
        try {
            $lieu = new Lieu();
            $lieuForm = $this->createForm(LieuType::class,$lieu);
            $lieuForm->handleRequest($request);
            if($lieuForm->isSubmitted() && $lieuForm->isValid()){
                $entityManager->persist($lieu);
                $entityManager->flush();
                $this->addFlash('success','Le lieu a bien été crée dans la base de données');
                return $this->redirectToRoute('app_create');
            }
        }catch (Exception $exception){
            $this->addFlash('danger','Erreur lors de la création du lieu');
        }
        return $this->render('lieu/create.html.twig',[
            "lieuForm" => $lieuForm->createView(),
        ]);
    }
    #[Route('/lieu/manage', 'app_manage_lieu')]
    public function manage(VilleRepository $villeRepository, Request $request, EntityManagerInterface $entityManager): Response
    {
        $villes = $villeRepository->findAll();
        $filterForm = $this->createForm(FilterCityType::class);
        $filterForm->handleRequest($request);

//        $filteredCities = [];

        if ($filterForm->isSubmitted()) {
            $data = $filterForm->getData();
            $filteredCities = $villeRepository->findFilterCity($data);
            dump($filteredCities);
        }


        return $this->render('lieu/managePlace.html.twig', [
            "villes" => $villes,
            "filterCity" => $filterForm->createView(),
            "filteredCities" => $filteredCities,
        ]);
    }



    #[Route('/lieu/update/{id}','app_update_ville')]
    public function update($id,VilleRepository $villeRepository,LieuRepository $lieuRepository): Response
    {
        $ville = $villeRepository->find($id);
        $lieuId = $lieuRepository->find($id);
        $lieux = [];
        foreach ($ville->getLieux() as $lieu) {
            $lieux[] = $lieu->getNom();
        }
        return $this->render('lieu/update.html.twig',[
            "ville" => $ville,
            "lieux" => $lieux,
            "lieuId" => $lieuId
        ]);
    }

    #[Route('/lieu/delete/{id}','app_remove_ville')]
    public function delete($id,VilleRepository $villeRepository,EntityManagerInterface $entityManager) : Response
    {
        try {
            $ville = $villeRepository->find($id);
            $lieux = $ville->getLieux();
            foreach ($lieux as $lieu){
                if ($lieu->getSorties()->count() > 0) {
                    $this->addFlash('danger','Impossible de supprimer la ville car des lieux sont liés à des sorties.');
                    return $this->redirectToRoute('app_manage_lieu');
                }
                $ville->removeLieux($lieu);
                $entityManager->remove($lieu);
            }
            $entityManager->remove($ville);
            $entityManager->flush();
            $this->addFlash('success','Le ville a bien été supprimée dans la base de données');
            return $this->redirectToRoute('app_manage_lieu');
        }catch (Exception $exception){
            $this->addFlash('danger','Erreur lors de la suppression de la ville');
        }
        return $this->render('lieu/delete.html.twig');
    }

    #[Route('/lieu/updatePlace/{id}','app_update_lieu')]
    public function updatePlace($id,LieuRepository $lieuRepository,Request $request,EntityManagerInterface $entityManager,VilleRepository $villeRepository) : Response
    {
        try {
            $lieu = $lieuRepository->find($id);
            $ville = $villeRepository->find($id);
            $lieuForm = $this->createForm(UpdatePlaceType::class,$lieu);
            $lieuForm->handleRequest($request);

            if($lieuForm->isSubmitted() && $lieuForm->isValid()){
                $entityManager->persist($lieu);
                $entityManager->flush();
                $this->addFlash('success','Le lieu a bien été modifiée dans la base de données');
                return $this->redirectToRoute('app_update_ville',['id' => $ville->getId()]);
            }
        }catch (Exception $exception){
            $this->addFlash('danger', 'Erreur lors de la modification du lieu');
        }


        return $this->render('lieu/updatePlace.html.twig',[
            "lieu" => $lieu,
            "ville" => $ville,
            "lieuForm" => $lieuForm->createView()
        ]);
    }

    #[Route('/lieu/deletePlace/{id}','app_remove_lieu')]
    public function deletePlace($id,VilleRepository $villeRepository,EntityManagerInterface $entityManager) : Response
    {
        try {
            $ville = $villeRepository->find($id);
            $lieux = $ville->getLieux();
            foreach ($lieux as $lieu){
                if ($lieu->getSorties()->count() > 0) {
                    $this->addFlash('danger','Impossible de supprimer le lieu car il est lié à une sortie.');
                    return $this->redirectToRoute('app_update_ville',['id' => $ville->getId()]);
                }
                $ville->removeLieux($lieu);
                $entityManager->remove($lieu);
            }
            $entityManager->remove($ville);
            $entityManager->flush();
            $this->addFlash('success','Le lieu a bien été supprimée dans la base de données');
            return $this->redirectToRoute('app_update_ville',['id' => $ville->getId()]);
        }catch (Exception $exception){
            $this->addFlash('danger','Erreur lors de la suppression du lieu');
        }
        return $this->render('lieu/deletePlace.html.twig');
    }
}
