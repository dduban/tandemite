<?php

namespace App\Controller;

use App\DTO\PersonDto;
use App\Entity\Person;
use App\Form\PersonDataFormType;
use App\Repository\PersonRepository;
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
    /** @var PersonRepository */
    private $personRepository;

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
            $personsDto[] = $personDto;
        }

        return $this->render('person-list.html.twig', [
            'persons' => $personsDto,
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
                    dump('error');
                }

                $person->setFilePath($newFilename);
            }

            $this->personRepository->add($person, true);

            return $this->redirectToRoute('person_list');
        }

        return $this->render('person-form.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
