<?php

namespace App\Entity;

use App\Repository\PalindromeRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: PalindromeRepository::class)]
/**
 * @ORM\Entity
 * @UniqueEntity("documentName")
 */

class Palindrome
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'text', nullable: false)]
    private $palindrome;

    #[ORM\Column(type: 'integer', nullable: false)]
    private $frequency;

    #[ORM\Column(type: 'string', nullable: false, unique:true)]
    private $documentName;

    #[ORM\Column(type: 'string', nullable: false, unique:true)]
    private $sentence;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPalindrome(): ?string
    {
        return $this->palindrome;
    }

    public function setPalindrome(?string $palindrome): self
    {
        $this->palindrome = $palindrome;

        return $this;
    }

    public function getFrequency(): ?int
    {
        return $this->frequency;
    }

    public function setFrequency(?int $frequency): self
    {
        $this->frequency = $frequency;

        return $this;
    }

    public function getDocumentName() : string
    {
        return $this->documentName;
    }

    public function setDocumentName($documentName): self
    {
        $this->documentName = $documentName;

        return $this;
    }

    public function getSentence() : string
    {
        return $this->sentence;
    }

    public function setSentence($sentence): self
    {
        $this->sentence = $sentence;

        return $this;
    }
}
