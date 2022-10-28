<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Image;
use App\Entity\Trick;
use App\Entity\Video;
use App\Form\CommentType;
use App\Form\TrickType;
use App\Repository\CommentRepository;
use App\Repository\ImageRepository;
use App\Repository\TrickRepository;
use App\Repository\VideoRepository;
use phpDocumentor\Reflection\DocBlock\Tags\Var_;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;


/**
 * @Route("/trick", name="trick_")
 */
class TrickController extends AbstractController
{
    /**
     * @Route("/list", name="index", methods={"GET"})
     */
    public function index(TrickRepository $trickRepository): Response
    {
        return $this->render('trick/index.html.twig', [
            'tricks' => $trickRepository->findAll(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="edit", methods={"GET", "POST"})
     * @IsGranted("ROLE_USER")
     */
    public function edit(Request $request, Trick $trick, TrickRepository $trickRepository): Response
    {
        $form = $this->createForm(TrickType::class, $trick);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $trick->setModifingDate(new \DateTime());
            $trickRepository->add($trick);
        }

        return $this->renderForm('trick/edit.html.twig', [
            'trick' => $trick,
            'form' => $form
        ]);
    }

    /**
     * @Route("/new", name="new", methods={"GET", "POST"})
     * @IsGranted("ROLE_USER")
     */
    public function new(Request $request, TrickRepository $trickRepository): Response
    {
        $trick = new Trick();

        $form = $this->createForm(TrickType::class, $trick);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $trickRepository->add($trick);
            return $this->redirectToRoute('trick_list', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('trick/new.html.twig', [
            'trick' => $trick,
            'form' => $form
        ]);
    }

    /**
     * @Route("/{slug}", name="show", methods={"GET","POST"})
     */
    public function show(Request $request, Trick $trick, CommentRepository $commmentRepository): Response
    {


        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->get('security.token_storage')->getToken()->getUser();
            $user->getUsername();
            $comment->setCreatedDate(new \DateTime());
            $comment->setTrick($trick);
            $comment->setUser($user);
            $commmentRepository->add($comment);
        }

        $page = $request->query->get("page") ? (int)$request->query->get("page") : 1;
        $comments = $commmentRepository->findBy(['trick' => $trick], ["createdDate" => "desc"], 10, ($page - 1) * 10);

        return $this->render('trick/show.html.twig', [
            'trick' => $trick,
            'comments' => $comments,
            'page' => $page,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/delete/{id}", name="delete", methods={"GET"})
     * @IsGranted("ROLE_USER")
     */
    public function delete(Request $request, Trick $trick, TrickRepository $trickRepository): Response
    {
        $this->addFlash(
            'success',
            "Trick " . $trick->getName() . " supprimer"
        );
        $trickRepository->remove($trick);

        return $this->redirectToRoute('home_index', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * @Route("/delete/{id}", name="delete_trick", methods={"POST"})
     * @IsGranted("ROLE_USER")
     */
    public function deleteTrick(Request $request, Trick $trick, TrickRepository $trickRepository): Response
    {
        $trickRepository->remove($trick);

        return new JsonResponse(['status' => 'OK'], 201);
    }

    /**
     * Call only Ajax
     * @Route("/{id}/upload/image", name="ajax_upload_image", methods={"POST"})
     * @IsGranted("ROLE_USER")
     */
    public function uploadImage(Request $request, Trick $trick, ImageRepository $imageRepository): Response
    {
        $file = $request->files->get('image');

        $fileName = md5(uniqid()) . '.' . $file->guessExtension();

        $request->files->get('image')->move(
            '../public/images/photos/trick_' . $trick->getSlug() . '/',
            $fileName
        );

        $image = new Image();
        $image->setName($fileName);
        $image->setTrick($trick);
        $imageRepository->add($image);

        $image = $imageRepository->findOneBy([], ['id' => 'desc']);
        $html = $this->renderView('trick/include/trick_image.twig', [
            'image' => $image,
            'trick' => $trick,
            'editMode' => true
        ]);

        return new JsonResponse(['status' => 'OK', 'html' => $html], 201);
    }

    /**
     * Call only Ajax
     * @Route("/{id}/upload/main", name="ajax_upload_main_image", methods={"POST"})
     * @IsGranted("ROLE_USER")
     */
    public function uploadMainImage(Request $request, Trick $trick, ImageRepository $imageRepository, TrickRepository $trickRepository): Response
    {
        $file = $request->files->get('image');

        $fileName = md5(uniqid()) . '.' . $file->guessExtension();

        $request->files->get('image')->move(
            '../public/images/photos/trick_' . $trick->getSlug() . '/',
            $fileName
        );


        $trick->setMainImage($fileName);
        $trickRepository->add($trick);

        return new JsonResponse(['status' => 'OK', 'name' => $fileName, 'slug' => $trick->getSlug()], 201);
    }

    /**
     * Call only Ajax
     * @Route("/{id}/upload/video", name="ajax_upload_video", methods={"POST"})
     * @IsGranted("ROLE_USER")
     */
    public function uploadVideo(Request $request, Trick $trick, VideoRepository $videoRepository): Response
    {
        $url = $request->request->get('url');
        if (!empty($url)) {
            $video = new Video();
            $video->setUrl($url);
            $video->setTrick($trick);
            $videoRepository->add($video);

            $video = $videoRepository->findOneBy([], ['id' => 'desc']);
            $html = $this->renderView('trick/include/trick_video.twig', [
                'video' => $video,
                'trick' => $trick,
                'editMode' => true
            ]);
            $this->addFlash(
                'success',
                "Vidéo ajouter à la liste"
            );
            return new JsonResponse(['status' => 'OK', 'html' => $html], 201);
        } else {
            throw new BadRequestHttpException('no video url sent', null, 401);
        }
    }

    /**
     * Call only Ajax
     * @Route("/{id}/edit/name", name="ajax_edit_name", methods={"POST"})
     * @IsGranted("ROLE_USER")
     */
    public function editTrickName(Request $request, Trick $trick, TrickRepository $trickRepository): Response
    {
        $name = $request->request->get('name');
        if (!empty($name)) {

            if (!empty($trickRepository->findOneBy(["name" => $name]))) $trick->setName($name);
            $trickRepository->add($trick);
            return new JsonResponse(['status' => 'OK', 201]);
        } else {
            throw new BadRequestHttpException('no name sent', null, 401);
        }
    }

    /**
     * @Route("/delete/image/{id}", name="delete_image", methods={"POST"})
     * @IsGranted("ROLE_USER")
     */
    public function deleteImage(Request $request, Image $image, ImageRepository $imageRepository): Response
    {
        try {
            unlink(__DIR__ . '/../../public/images/photos/trick_' . $image->getTrick()->getSlug() . '/' . $image->getName());
            $imageRepository->remove($image);
            $this->addFlash(
                'success',
                "Image supprimé !"
            );
            return new JsonResponse($image);
        } catch (\Exception $exception) {
            dump($exception);
        }
        return new JsonResponse();
    }

    /**
     * @Route("/delete/video/{id}", name="delete_video", methods={"POST"})
     * @IsGranted("ROLE_USER")
     */
    public function deleteVideo(Request $request, Video $video, VideoRepository $videoRepository): Response
    {
        try {
            $videoRepository->remove($video);
            return new JsonResponse($video);
        } catch (\Exception $exception) {
            dump($exception);
        }

        return new JsonResponse();
    }

    /**
     * @Route("/delete/Comment/{id}", name="delete_comment", methods={"GET"})
     * @IsGranted("ROLE_USER")
     */
    public function deleteComment(Request $request, Comment $comment, CommentRepository $commentRepository): Response
    {
        try {
            $trick = $comment->getTrick();
            $commentRepository->remove($comment);
        } catch (\Exception $exception) {
            $this->addFlash('danger', "commentaire non supprimé");
        }
        $this->addFlash('sucess', "commentaire supprimé");
        return $this->redirectToRoute('trick_show', ["slug" => $trick->getSlug()], Response::HTTP_SEE_OTHER);
    }

    /**
     * @Route("/edit/Comment/{id}", name="edit_comment", methods={"POST"})
     * @IsGranted("ROLE_USER")
     */
    public function editComment(Request $request, Comment $comment, CommentRepository $commentRepository): Response
    {
        $content = $request->get("content");
        $comment->setContent($content);
        $commentRepository->add($comment);
        $trick = $comment->getTrick();
        return $this->redirect($this->generateUrl('trick_show', ["slug" => $trick->getSlug()]) . '#comments');
    }

}

