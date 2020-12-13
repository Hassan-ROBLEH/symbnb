<?php

namespace App\Entity;

use Symfony\Component\Validator\Constraints as Assert;


class PasswordUpdate
{

    private $id;

    private $oldPassword;

    /**
     * @Assert\length(min=8, minMessage="Votre mot de passe doit faire au moins 8 caractères !")
     *
     * @var [type]
     */
    private $newPassword;

    /**
     * @Assert\EqualTo(propertyPath="newPassword", message="Vous n'avez pas correctement confirmé votre mot de passe")
     *
     */
    private $confirmePassword;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOldPassword(): ?string
    {
        return $this->oldPassword;
    }

    public function setOldPassword(string $oldPassword): self
    {
        $this->oldPassword = $oldPassword;

        return $this;
    }

    public function getNewPassword(): ?string
    {
        return $this->newPassword;
    }

    public function setNewPassword(string $newPassword): self
    {
        $this->newPassword = $newPassword;

        return $this;
    }

    public function getConfirmePassword(): ?string
    {
        return $this->confirmePassword;
    }

    public function setConfirmepassword(string $confirmePassword): self
    {
        $this->confirmePassword = $confirmePassword;

        return $this;
    }
}