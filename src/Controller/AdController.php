<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Entity\Image;
use App\Form\AdType;
use App\Repository\AdRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class AdController extends AbstractController
{
    public function __construct(AdRepository $adRepository, EntityManagerInterface $em)
    {
         $this->adRepository = $adRepository;
         $this->em = $em;
    }
    /**
     * page d'affichage des annonces
     * @Route("/ads", name="ads_index")
     * @return Response
     */
    public function index()
    {

        //$repo = $this->getDoctrine()->getRepository(Ad::class);
        $ads = $this->adRepository->findAll();
        return $this->render('ad/index.html.twig', [
            'ads' => $ads
        ]);
    }
    /**
     * Permettra de créer une annonce
     * @Route("/ads/create", name="ad_create")
     * @IsGranted("ROLE_USER")
     * @param Request
     * @return Response
     */
    public function create(Request $request){
        $ad = new Ad();
       
        $form = $this->createForm(AdType::class, $ad);
        $form->handleRequest($request);
       
        if($form->isSubmitted() && $form->isValid()){
            $this->em->persist($ad);
            foreach($ad->getImages() as $images){
                //ici on gére notre collection d'image on récupère chaque image et on là  lie à l'annonce qui correspond avant de la persister
                $images->setAd($ad);
                $this->em->persist($images);
            }

            $ad->setAuthor($this->getUser());
            $this->em->flush();

            $this->addFlash('success', 'l\'annonce '.$ad->getTitle().' que vous avez créé à bien été enregistré');
            return $this->redirectToRoute('ad_show',[
                'slug' => $ad->getSlug()
            ]);
        }
        return $this->render('ad/create.html.twig',[
            'form' => $form->createView()
        ]);
    }

   /**
     * cette fonction permettra d'afficher une seule annonce
     * @param Ad $ad
     * @Route("/ads/{slug}", name="ad_show")
     * @return Response
     */

     //ici nous faisons une injection de dépendance alors symfony prendra notre slug et 
     //va trouver l'annonce qui correspond à notre slug ainsi il nous retournera directment l'annonce en question il s'agit la du param converter
    public function show(Ad $ad){
        return $this->render('ad/show.html.twig',[
            'ad' => $ad
        ]);
    }
    /**
     * permet d'afficher le formulaire d'édition
     * @Route("/ads/{slug}/edit", name="ad_edit")
     * @Security("(is_granted('ROLE_USER') and user === ad.getAuthor()) or is_granted('ROLE_ADMIN')", message="Cette annonce ne vous appartient pas, vous ne pouvez pas la modifier")
     * 
     * @param Ad $ad
     * @param Request $request
     * @return Response
     */
    public function edit(Ad $ad,Request $request){
        $form = $this->createForm(AdType::class, $ad);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $this->em->persist($ad);
            foreach($ad->getImages() as $images){
                //ici on gére notre collection d'image on récupère chaque image et on là  lie à l'annonce qui correspond avant de la persister
                $images->setAd($ad);
                
                $this->em->persist($images);
            }
            $this->em->flush();
            $this->addFlash('warning', 'l\'annonce '.$ad->getTitle().'  à bien été modifier');
            return $this->redirectToRoute('ad_show',[
                'slug' => $ad->getSlug()
            ]);
        }
        return $this->render('ad/edit.html.twig',[
            'ad' => $ad,
            'form' => $form->createView()
        ]);
    }

    /**
     * Permet de supprimer une annonce
     * @Route("/ads/{slug}/delete", name="ad_delete")
     * @Security("is_granted('ROLE_USER') and user === ad.getAuthor()", message="Vous n'avez pas le droit de supprimer cette annonce")
     * @param Ad $ad
     * @return Response
     */
    public function delete(Ad $ad):Response
    {
        $this->em->remove($ad);
        $this->em->flush();
        $this->addFlash("success", "L\'annonce ".$ad->getTitle()." a bien été supprimé ");
        return $this->redirectToRoute('ads_index');
    }
}
