<?php

namespace App\Controller;

use App\Entity\Advert;
use App\Entity\AdvertSkill;
use App\Entity\Application;
use App\Form\AdvertType;
use App\Repository\AdvertRepository;
use App\Service\FileUploader;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class AdvertController extends AbstractController
{
    /**
     * @Route("/list", name="list")
     */
    public function index($page = 1, AdvertRepository $advertRepository)
    {
        if ($page < 1) {
            throw $this->createNotFoundException("La page " . $page . " n'existe");
        }

        $nbPerPage = 10;

        // Pour récupérer la liste de toutes les annonces : en utilisant findAll
        $listAdverts = $advertRepository->getAdverts($page, $nbPerPage);

        $nbPages = ceil(count($listAdverts) / $nbPerPage);

        if ($page > $nbPages) {
            throw $this->createNotFoundException("La page " . $page . " n'existe pas");
        }

        return $this->render('advert/index.html.twig', [
            'listAdverts' => $listAdverts,
            'nbPages' => $nbPages,
            'page' => $page
        ]);
    }

    /**
     * @Route("/view/{id}", name="view")
     */
    public function view(Advert $advert)
    {
        if (null === $advert) {
            throw new NotFoundHttpException("L'annonce d'id " . $advert->getId() . " n'existe pas");
        }

        // Récupérer la liste des candidatures de l'annonce
        $listApplications = $this->getDoctrine()
            ->getManager()
            ->getRepository(Application::class)
            ->findBy(['advert' => $advert]);

        // Récupérer des advertSkill de l'annonce
        $listAdvertSkills = $this->getDoctrine()
            ->getManager()
            ->getRepository(AdvertSkill::class)
            ->findBy(['advert' => $advert]);

        return $this->render('advert/view.html.twig', [
            'advert' => $advert,
            'listApplications' => $listApplications,
            'listAdvertSkills' => $listAdvertSkills
        ]);
    }

    /**
     * @Route("/add", name="add")
     */
    public function add(Request $request, FileUploader $fileUploader, ValidatorInterface $validator)
    {
        $em = $this->getDoctrine()->getManager();

        $advert = new Advert();

        $form = $this->createForm(AdvertType::class, $advert);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $file = $advert->getImage()->getFile();
                $fileName = $fileUploader->upload($file);

                $advert->getImage()->setUrl($fileName);
                $advert->getImage()->setAlt($fileName);

                //$advert->getImage()->upload();

                $listErrors = $validator->validate($advert);
                if (count($listErrors) > 0) {
                    return new Response((string) $listErrors);
                } else {
                    return new Response("L'annonce est valide !");
                }

                $em->persist($advert);
                $em->flush();

                $this->addFlash('notice', 'Annonce a bien été enregistrée');

                return $this->redirectToRoute('view', ['id' => $advert->getId()]);
            }
        }

        return $this->render('advert/add.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/edit/{id}", name="edit")
     */
    public function edit(Request $request, Advert $advert)
    {
        if (null === $advert) {
            throw new NotFoundHttpException("L'annonce d'id " . $advert->getId() . " n'existe pas");
        }

        $em = $this->getDoctrine()->getManager();

        $form = $this->createForm(AdvertType::class, $advert);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $em->flush();

                $this->addFlash('notice', 'Annonce bien modifiée');

                return $this->redirectToRoute('view', ['id' => $advert->getId()]);
            }
        }

        return $this->render('advert/edit.html.twig', [
            'advert' => $advert,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function delete(Request $request, Advert $advert)
    {
        if (null === $advert) {
            throw new NotFoundHttpException("L'annonce d'id " . $advert->getId() . " n'existe pas");
        }

        $em = $this->getDoctrine()->getManager();

        $form = $this->get('form.factory')->create();

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $em->remove($advert);
            $em->flush();

            $this->addFlash('info', "L'annonce a bien été supprimée");

            return $this->redirectToRoute('list');
        }

        return $this->render('advert/delete.html.twig', [
            'advert' => $advert,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/menu", name="menu")
     */
    public function menu($limit)
    {
        $em = $this->getDoctrine()->getManager();

        $listAdverts = $em
            ->getRepository(Advert::class)
            ->findBy(
                [],
                ['date' => 'desc'],
                $limit,
                0
            );

        return $this->render('advert/menu.html.twig', [
            'listAdverts' => $listAdverts
        ]);
    }
}
