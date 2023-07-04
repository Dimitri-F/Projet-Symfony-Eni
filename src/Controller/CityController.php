<?php

namespace App\Controller;

use App\Entity\Ville;
use App\Form\CityType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CityController extends AbstractController
{
    #[Route('/city', name: 'app_create_city')]
    public function index(EntityManagerInterface $entityManager, Request $request): Response
    {
        try {
            $city = new Ville();
            $cityForm = $this->createForm(CityType::class,$city);
            $cityForm->handleRequest($request);
            if($cityForm->isSubmitted() && $cityForm->isValid()){
                $entityManager->persist($city);
                $entityManager->flush();
                $this->addFlash('success','La ville a bien été créee dans la base de données');
                return $this->redirectToRoute('app_home');
            }
        }catch (Exception $exception){
            $this->addFlash('danger','Erreur lors de la création de la ville');
        }
        return $this->render('city/create.html.twig', [
            "cityForm" => $cityForm->createView()
        ]);
    }
}
