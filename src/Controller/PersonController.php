<?php

namespace App\Controller;

use App\DTO\PersonDto;
use App\Form\PersonDataFormType;
use App\Repository\PersonRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class PersonController extends AbstractController
{
    private PersonRepository $personRepository;

    public function __construct(
        PersonRepository $personRepository
    )
    {
        $this->personRepository = $personRepository;
    }

    /**
     * @Route("/list", name="person_list", methods={"GET"})
     */
    public function listing(): Response
    {
        $persons = $this->personRepository->findAll();

        $personsDto = [];

        foreach ($persons as $person) {
            $personDto = PersonDto::mapFromEntity($person);
            if ($person->getFilePath()) {
                $personDto->fileUrl = $this->generateUrl('app_public_files', ['filename' => $person->getFilePath()]);
            }
            $personsDto[] = $personDto;
        }

        return $this->render('person-list.html.twig', [
            'persons' => $personsDto,
        ]);
    }

    /**
     * @Route("/uploads/files/{filename}", name="app_public_files")
     */
    public function show(string $filename): Response
    {
        $imageDirectory = $this->getParameter('files_directory');
        $filePath = $imageDirectory . "/" . $filename;

        if (!file_exists($filePath)) {
            throw $this->createNotFoundException('Plik nie istnieje');
        }

        $mimeTypes = [
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
        ];

        $response = new BinaryFileResponse(new File($filePath));
        $response->headers->set('Content-Type', $mimeTypes);

        return $response;
    }

    /**
     * @Route("/", name="person_new", methods={"POST", "GET"})
     */
    public function new(Request $request, SluggerInterface $slugger, ValidatorInterface $validator): Response
    {
        $personDto = new PersonDto();
        $form = $this->createForm(PersonDataFormType::class, $personDto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /** @var UploadedFile $file */
            $file = $form->get('fileUrl')->getData();

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
                    return new JsonResponse(['errors' => 'Nie udało się załadować pliku.'], Response::HTTP_BAD_REQUEST);
                }

                $personDto->setFileUrl($newFilename);
            }

            $errors = $validator->validate($personDto);

            if (count($errors) > 0) {
                $response = new JsonResponse(['errors' => 'Niepoprawne dane.'], Response::HTTP_BAD_REQUEST);
            } else {
                $person = PersonDto::mapToEntity($personDto);
                $this->personRepository->add($person, true);
                $response = new JsonResponse(['message' => 'Pomyślnie utworzono.'], Response::HTTP_CREATED);
            }

            return $response;
        }

        return $this->render('person-form.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
