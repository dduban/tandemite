<?php

namespace App\Controller;

use App\Entity\Person;
use App\Form\PersonDataFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class PersonController extends AbstractController
{
    /**
     * @Route("/list", name="person_list", methods={"GET"})
     */
    public function listing(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/PersonController.php',
        ]);
    }

    /**
     * @Route("/new", name="person_new", methods={"POST", "GET"})
     */
    public function new(Request $request, SluggerInterface $slugger): Response
    {
        $person = new Person();
        $form = $this->createForm(PersonDataFormType::class, $person);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $file */
            $file = $form->get('filePath')->getData();

            if ($file) {
                $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();

                try {
                    $file->move(
                        $this->getParameter('files_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                $person->setFilePath($newFilename);
            }

            // ... persist the $product variable or any other work

            return $this->redirectToRoute('person_list');
        }

        return $this->render('person-form.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
