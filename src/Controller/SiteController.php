<?php

namespace App\Controller;

use App\Entity\Site;
use App\Form\CreateSiteType;
use App\Form\FilterSiteType;
use App\Repository\ParticipantRepository;
use App\Repository\SiteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SiteController extends AbstractController
{
    #[Route('/site/manage', name: 'app_manage_site')]
    public function manage(Security $security,SiteRepository $siteRepository,Request $request): Response
    {
        if(!$security->isGranted('ROLE_ADMIN')){
            return $this->redirectToRoute('app_home');
        }

        $sites = $siteRepository->findAll();
        $filterForm = $this->createForm(FilterSiteType::class);
        $filterForm->handleRequest($request);

        if($filterForm->isSubmitted()){
            $data = $filterForm->getData();
            $sites = $siteRepository->findFilterSite($data);
        }

        return $this->render('site/manage.html.twig', [
            "sites" => $sites,
            "filterForm" => $filterForm->createView(),
        ]);
    }

    #[Route('/site/create', name: 'app_create_site')]
    public function create(Request $request, EntityManagerInterface $entityManager,Security $security) : Response
    {
        if(!$security->isGranted('ROLE_ADMIN')){
            return $this->redirectToRoute('app_home');
        }

        try {
            $site = new Site();
            $siteForm = $this->createForm(CreateSiteType::class,$site);
            $siteForm->handleRequest($request);
            if($siteForm->isSubmitted() && $siteForm->isValid()){
                $entityManager->persist($site);
                $entityManager->flush();
                $this->addFlash('success','Le site a bien été crée dans la base de données');
                return $this->redirectToRoute('app_manage_site');
            }
        }catch (Exception $exception){
            $this->addFlash('danger','Erreur lors de la création du site');
        }

        return $this->render('site/create.html.twig',[
            "siteForm" => $siteForm->createView()
        ]);
    }

    #[Route('/site/update/{id}','app_update_site')]
    public function update($id,Security $security,SiteRepository $siteRepository,Request $request,EntityManagerInterface $entityManager) : Response
    {
        if(!$security->isGranted('ROLE_ADMIN')){
            return $this->redirectToRoute('app_home');
        }
        try {
            $site = $siteRepository->find($id);
            $siteForm = $this->createForm(CreateSiteType::class,$site);
            $siteForm->handleRequest($request);

            if($siteForm->isSubmitted() && $siteForm->isValid()){
                $entityManager->persist($site);
                $entityManager->flush();
                $this->addFlash('success','Le site a bien été modifié dans la base de données');
                return $this->redirectToRoute('app_manage_site');
            }
        }catch (Exception $exception){
            $this->addFlash('danger','Erreur lors de la modification du lieu');
        }

        return $this->render('site/update.html.twig',[
            "site" => $site,
            "siteForm" => $siteForm->createView()
        ]);
    }

    #[Route('/site/delete/{id}', 'app_delete_site')]
    public function delete(Security $security, $id, SiteRepository $siteRepository, ParticipantRepository $participantRepository, EntityManagerInterface $entityManager): Response
    {
        if (!$security->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('app_home');
        }
        try {
            $site = $siteRepository->find($id);
            $participants = $participantRepository->findBy(['site' => $site]);
            if (count($participants) > 0) {
                $this->addFlash('danger', 'Impossible de supprimer le site car il est lié à des participants.');
                return $this->redirectToRoute('app_manage_site');
            }
            $entityManager->remove($site);
            $entityManager->flush();
            $this->addFlash('success', 'Le site a bien été supprimé de la base de données');
        } catch (\Exception $exception) {
            $this->addFlash('danger', 'Erreur lors de la suppression du site : ' . $exception->getMessage());
        }

        return $this->redirectToRoute('app_manage_site');
    }
    }

