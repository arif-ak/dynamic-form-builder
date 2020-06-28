<?php

namespace App\Entity;

use App\Repository\QuestionChoiceRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=QuestionChoiceRepository::class)
 */
class QuestionChoice
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=500)
     */
    private $choice;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isActive;

    /**
     * @ORM\ManyToOne(targetEntity=Question::class, inversedBy="questionChoices", cascade={"persist"})
     * @ORM\JoinColumn(name="question_id",referencedColumnName="id", nullable=true, onDelete="CASCADE"))
     */
    private $question;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getChoice(): ?string
    {
        return $this->choice;
    }

    public function setChoice(string $choice): self
    {
        $this->choice = $choice;

        return $this;
    }

    public function getIsActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(?bool $isActive): self
    {
        $this->isActive = $isActive;

        return $this;
    }

    public function getQuestion(): ?Question
    {
        return $this->question;
    }

    public function setQuestion(?Question $question): self
    {
        $this->question = $question;

        return $this;
    }
}
