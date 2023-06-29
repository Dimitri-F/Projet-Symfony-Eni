<?php

namespace App\Controller;

use App\Form\LieuType;
use App\Entity\Lieu;
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
}
