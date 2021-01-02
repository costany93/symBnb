<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Form\AdType;
use App\Repository\AdRepository;
use App\Services\Pagination;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminAdController extends AbstractController
{

    public function __construct(AdRepository $adRepository, EntityManagerInterface $em)
    {
        $this->adRepository = $adRepository;
        $this->em = $em;
    }
    /**
     * @Route("/admin/ads/{page<\d+>?1}", name="admin_ads_index")
     */
    public function index($page, Pagination $pagination)
    {
        $pagination->setEntityClass(Ad::class)
                    ->setPage($page)
                    ->setLimit(1)
                    
        ;
        $pages = $pagination->getPages();

        return $this->render('admin/ad/index.html.twig', [
            'pagination' => $pagination
        ]);
    }

    /**
     * permet à l'administrateur de modifier une annonce
     * @Route("/admin/ads/{id}/edit", name="admin_ads_edit")
     */
    public function edit(Ad $ad, Request $request){
        $form = $this->createForm(AdType::class, $ad);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $this->em->persist($ad);
            $this->em->flush();

            $this->addFlash("success", "L'annonce ".$ad->getTitle()." a bien été modifié");
        }
        return $this->render('admin/ad/edit.html.twig',[
            'form' => $form->createView(),
            'ad' => $ad
        ]);
    }

    /**
     * @Route("/admin/ads/{id}/deleye", name="admin_ads_delete")
     * @param Ad $ad
     * @return Response
     */
    public function delete(Ad $ad){
        if(count($ad->getBookings()) > 0){
            $this->addFlash("warning","Vous ne pouvez pas supprimer l'annonce car elle possède déjà des réservations");
            return $this->redirectToRoute('admin_ads_index');
        }else{
            $this->em->remove($ad);
            $this->em->flush();
            $this->addFlash("success","L'annonce à bien été supprimer");
            return $this->redirectToRoute('admin_ads_index');
        }
        
    } 
}
