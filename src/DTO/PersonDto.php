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
    public $fileUrl;

    public static function mapFromEntity(Person $person): self
    {
        $personDto = new self();
        $personDto->setIdPerson($person->getId());
        $personDto->setName($person->getName());
        $personDto->setSurname($person->getSurname());
        $personDto->fileUrl = $this->generateUrl('app_public_files', ['filename' => $person->getFilePath()]);

        return $personDto;
    }

    public function generateFileUrl(string $filesDirectory): ?string
    {
        if ($this->fileUrl) {
            return sprintf('%s/%s', rtrim($filesDirectory, '/'), ltrim($this->fileUrl, '/'));
        }

        return null;
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