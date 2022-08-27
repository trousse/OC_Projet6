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
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;

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
     * @Route("/new", name="new", methods={"GET", "POST"})
     */
    public function new(Request $request, TrickRepository $trickRepository): Response
    {
        $trick = new Trick();

        $form = $this->createForm(TrickType::class, $trick);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $trickRepository->add($trick);

            return $this->redirectToRoute('trick_edit', ['id' => $trick->getid()], Response::HTTP_SEE_OTHER);
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

        return $this->render('trick/show.html.twig', [
            'trick' => $trick,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/{id}/edit", name="edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Trick $trick, TrickRepository $trickRepository): Response
    {
        $form = $this->createForm(TrickType::class, $trick);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $trick->setModifingDate(new \DateTime());
            $trickRepository->add($trick);

            return $this->redirectToRoute('trick_show', ["slug" => $trick->getSlug()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('trick/edit.html.twig', [
            'trick' => $trick,
            'form' => $form
        ]);
    }

    /**
     * @Route("/{id}/delete", name="delete", methods={"GET"})
     */
    public function delete(Request $request, Trick $trick, TrickRepository $trickRepository): Response
    {
        $trickRepository->remove($trick);

        return $this->redirectToRoute('trick_index', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * @Route("/{id}", name="delete_trick", methods={"POST"})
     */
    public function deleteTrick(Request $request, Trick $trick, TrickRepository $trickRepository): Response
    {
        $trickRepository->remove($trick);

        return new JsonResponse(['status' => 'OK'], 201);
    }

    /**
     * Call only Ajax
     * @Route("/{id}/upload/image", name="ajax_upload_image", methods={"POST"})
     */
    public function uploadImage(Request $request, Trick $trick, ImageRepository $imageRepository): Response
    {
        $file = $request->files->get('image');

        $fileName = md5(uniqid()) . '.' . $file->guessExtension();

        $request->files->get('image')->move(
            '../public/images/photos/trick_' . $trick->getId() . '/',
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
     */
    public function uploadMainImage(Request $request, Trick $trick, ImageRepository $imageRepository, TrickRepository $trickRepository): Response
    {
        $file = $request->files->get('image');

        $fileName = md5(uniqid()) . '.' . $file->guessExtension();

        $request->files->get('image')->move(
            '../public/images/photos/trick_' . $trick->getId() . '/',
            $fileName
        );

        $image = new Image();
        $image->setName($fileName);
        $image->setTrick($trick);
        $imageRepository->add($image);

        $image = $imageRepository->findOneBy([], ['id' => 'desc']);
        $trick->setMainImage($image);
        $trickRepository->add($trick);

        $html = $this->renderView('trick/include/trick_image.twig', [
            'image' => $image,
            'trick' => $trick,
            'editMode' => true
        ]);

        return new JsonResponse(['status' => 'OK', 'name' => $fileName, "id" => $trick->getId(), 'html' => $html], 201);
    }

    /**
     * Call only Ajax
     * @Route("/{id}/upload/video", name="ajax_upload_video", methods={"POST"})
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
     */
    public function editTrickName(Request $request, Trick $trick, TrickRepository $trickRepository): Response
    {
        $name = $request->request->get('name');
        if (!empty($name)) {

            if(!empty($trickRepository->findOneBy(["name" => $name]))) $trick->setName($name);
            $trickRepository->add($trick);
            return new JsonResponse(['status' => 'OK', 201]);
        } else {
            throw new BadRequestHttpException('no name sent', null, 401);
        }
    }

    /**
     * @Route("/delete/image/{id}", name="delete_image", methods={"POST"})
     */
    public function deleteImage(Request $request, Image $image, ImageRepository $imageRepository): Response
    {
        try {
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
}
