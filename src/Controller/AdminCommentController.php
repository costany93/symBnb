<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Form\AdminCommentType;
use App\Form\CommentType;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminCommentController extends AbstractController
{
    public function __construct(CommentRepository $cm, EntityManagerInterface $em)
    {
        $this->cm = $cm;
        $this->em = $em;
    }
    /**
     *permet d'afficher la liste de tous les commentaires
     * @Route("/admin/comments", name="admin_comments_index")
     * @return Response
     */
    public function index()
    {
        $comment = $this->cm->findAll();
        return $this->render('admin/comment/index.html.twig', [
            'comments' => $comment
        ]);
    }

    /**
     * permet de modifier le contenu d'un commentaire
     * @Route("/admin/comments/{id}/edit", name="admin_comments_edit")
     * @param Comment $comment
     */
    public function edit(Comment $comment, Request $request){
        $form = $this->createForm(AdminCommentType::class, $comment);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $this->em->persist($comment);
            $this->em->flush();

            $this->addFlash("success", "Le commentaire a bien été modifié");

            return $this->redirectToRoute("admin_comments_index");
        }
        return $this->render("admin/comment/edit.html.twig",[
            'form' => $form->createView(),
            'comment' => $comment
        ]);
    }

    /**
     * permet de supprimer une annonce
     * @Route("/admin/comments/{id}/delete", name="admin_comments_delete")
     * @param Comment $comment
     * @return Response
     */
    public function delete(Comment $comment){
        $this->em->remove($comment);
        $this->em->flush();

        $this->addFlash("warning", "Le commentaire à été supprimé");

        return $this->redirectToRoute("admin_comments_index");
    }
}
