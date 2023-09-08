<?php


namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;
use App\Entity\Person;

class PersonDto
{
    public ?int $idPerson;

    /**
     * @Assert\Regex(
     *     pattern="#^[a-zA-Z0-9\-.\/]*$#",
     *     message="Name can only contain letters, numbers, -, ., and /"
     * )
     */
    public string $name;

    /**
     * @Assert\Regex(
     *     pattern="#^[a-zA-Z0-9\-./]*$#",
     *     message="Name can only contain letters, numbers, -, ., / and can be blank"
     * )
     */
    public ?string $surname = null;

    public ?string $fileUrl = null;

    public static function mapFromEntity(Person $person): self
    {
        $personDto = new self();
        $personDto->setIdPerson($person->getId());
        $personDto->setName($person->getName());
        $personDto->setSurname($person->getSurname());

        return $personDto;
    }

    public static function mapToEntity(PersonDto $personDto): Person
    {
        $person = new Person();
        $person->setName($personDto->getName());
        if ($personDto->getSurname()) {
            $person->setSurname($personDto->getSurname());
        }
        if ($personDto->getFileUrl()) {
            $person->setFilePath($personDto->getFileUrl());
        }

        return $person;
    }

    /**
     * @param int $idPerson
     * @return PersonDto
     */
    public function setIdPerson(int $idPerson): PersonDto
    {
        $this->idPerson = $idPerson;
        return $this;
    }

    /**
     * @return int
     */
    public function getIdPerson(): int
    {
        return $this->idPerson;
    }

    /**
     * @param string $name
     * @return PersonDto
     */
    public function setName(string $name): PersonDto
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string|null $surname
     * @return PersonDto
     */
    public function setSurname(?string $surname): PersonDto
    {
        $this->surname = $surname;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getSurname(): ?string
    {
        return $this->surname;
    }

    /**
     * @param string|null $fileUrl
     * @return PersonDto
     */
    public function setFileUrl(?string $fileUrl): PersonDto
    {
        $this->fileUrl = $fileUrl;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getFileUrl(): ?string
    {
        return $this->fileUrl;
    }
}