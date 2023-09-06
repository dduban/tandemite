<?php


namespace App\DTO;


use App\Entity\Person;

class PersonDto
{
    /** @var int */
    public $idPerson;

    /** @var string */
    public $name;

    /** @var string|null */
    public $surname;

    /** @var string|null */
    public $filePath;

    public static function mapFromEntity(Person $person): self
    {
        $personDto = new self();
        $personDto->setIdPerson($person->getId());
        $personDto->setName($person->getName());
        $personDto->setSurname($person->getSurname());
        $personDto->setFilePath($person->getFilePath());

        return $personDto;
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
     * @param string|null $filePath
     * @return PersonDto
     */
    public function setFilePath(?string $filePath): PersonDto
    {
        $this->filePath = $filePath;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getFilePath(): ?string
    {
        return $this->filePath;
    }
}