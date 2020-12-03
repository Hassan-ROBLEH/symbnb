<?php 

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController {

    /**
     * @Route("/boujour/{prenom}/age/{age}", name="hello")
     * @Route("/boujour", name="hello_base")
     * @Route("/bonjour/{prenom}", name="hello_prenom")
     * 
     * Montre la page qui dit bonjour
     *
     * @return void
     */
    public function hello($prenom = "anonyme", $age = 0) {

        //return new Response("Bonjour " . $prenom . " vous avez " . $age . " ans");
        return $this->render(
            'hello.html.twig',
            [
                'prenom' => $prenom,
                'age' => $age
            ]
        );
    }

    /**
     * @Route("/", name = "homepage")
     */
    public function home() {

        $prenoms = ["Lior" => 33, "Robleh" => 55, "Hassan" => 31];

        return $this->render(
            'home.html.twig',
            [
                'title' => "Au revoir tout le monde",
                'age' => 11,
                'tableau' => $prenoms
            ]
        );

    }
}


?>