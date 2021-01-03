<?php 

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Environment;


/**
 * Classe de pagination qui extrait toute notion de calcul et de récupération de données de nos controllers
 * 
 * Elle nécessite après instanciation qu'on lui passe l'entité sur laquelle on souhaite travailler
*/
class Pagination {
    /**
     * Le nom de l'entité sur laquelle on veut effectuer une pagination
     *
     * @var string
    */
    private $entityClass;

    /**
     * Le nombre d'enregistrement à récupérer
     *
     * @var integer
    */
    private $limit = 10;

    /**
     * La page sur laquelle on se trouve actuellement
     *
     * @var integer
    */
    private $currentPage = 1;

    /**
     * Le manager de doctrine qui nous permet notamment de trouver le repository dont on a besoin
     *
     * @var EntityManagerInterface
    */
    private $manager;

    /**
     * Le moteur de template twig qui va permettre de générer le rendu de la pagination 
     *
     * @var Twig\Environement
    */
    private $twig;

    /**
     * Le nom de la route que l'on vaut utiliser pour les boutons de la navigation
     *
     * @var string
    */
    private $route;

    /**
     * Le chemin vers le template qui contient la pagination
     *
     * @var string
    */
    private $templatePath;

    /**
     * Constructeur du service de pagination qui sera appelé par symfony
     * 
     * N'oubliez pas de configurer votre fichier service.yaml afin que symfony sache quelle valeur utiliser pour le $templatePath
     *
     * @param EntityManagerInterface $manager
     * @param Environment $twig
     * @param RequestStack $request
     * @param string $templatePath
     */
    public function __construct(EntityManagerInterface $manager,  Environment $twig, RequestStack $request, $templatePath) {

        /*
         * On récupérer le nom de la route à utiliser à partir des attributs de la requête actuelle 
        */
       $this->route = $request->getCurrentRequest()->attributes->get('_route');
        //dump($this->route);
        //die;

        // Autres initialisations
        $this->manager = $manager;
        $this->twig = $twig;
        $this->templatePath = $templatePath;
    }

    /**
     * Permet d'afficher le rendu de la navigation au sein d'un template twig !
     * 
     * On se sert ici de notre moteur de rendu afin de compiler le template qui se trouve au chemin de notre propriété $templatePath, en lui passant les variables :
     *  - Page  => La page actuelle sur laquelle on se trouve
     *  - Pages => Le nombre total de pages qui existent
     *  - route => Le nom de la route à utiliser pour les liens de navigation
     * 
     * Attention : cette fonction ne retourne rien, elle affiche directement le rendu 
     *
     * @return void
    */
    public function display() {
        //$this->twig->display('admin/partials/pagination.html.twig', [
        $this->twig->display($this->templatePath, [
            'page' => $this->currentPage,
            'pages' => $this->getPages(),
            // 'route' => "admin_ads_index"
            'route' => $this->route
        ]);
    }

    /**
     * Permet de récupérer le nombre de pages qui existent sur une entité particulière
     * 
     * Elle se sert de Doctrine pour récupèrer le repository qui correspond à l'entité que l'on souhaite
     * paginer (voir propriété $entityClass) puis elle trouve le nombre total d'enregistrements grâce 
     * à la fonction findAll() du repository
     * 
     * @throws Exception si la propriéré $entityClass n'est pas configurée
     *
     * @return int
    */
    public function getPages() {
        if (empty($this->entityClass)) {
            // S'il n'y a pas d'entité configurée, on ne peut pas charger le repository, la fonction
            // ne peut donc pas continuer !
            throw new Exception("Vous n'avez pas spécifié l'entité sur laquelle nous devons paginer ! Utilisez la méthode setEntityClass de votre objet pagination !");
        }

        // 1) Connaitre la total des enregistrements de la table
        $repo = $this->manager->getRepository($this->entityClass);
        $total = count($repo->findAll());

        // 2) Faire la division, l'arrondi et le renvoyer (Renvoyer les éléments en question)
        return ceil($total / $this->limit);
    }
    
    /**
     * Permet de récupèrer les données paginées pour une entité spécifique
     * 
     * Elle se sert de Doctrine afin de récupèrer le repository pour l'entité spécifiée
     * puis grâce au repository et à sa fonction findBy() on récupère les données dans une certaine limite et en partant d'un offset
     * 
     * @throws Exception si la propriété $entityClass n'est pas définie
     *
     * @return array
     */
    public function getData() {
        if (empty($this->entityClass)) {
            throw new Exception("Vous n'avez pas spécfié l'entité sur laquelle nous devons paginer ! Utilisez la méthode setEntityClass() de votre objet pagination !");
        }
        // 1) Calculer l'offset
        $offset = $this->currentPage * $this->limit - $this->limit;

        /* 2) demander au repository de trouver les éléments à partir d'un offset et
        * dans la limite d'éléments imposée (voir propriété $limit)
        */  
        //$repo = $this->manager->getRepository($this->entityClass);
        //$data = $repo->findBy([], [], $this->limit, $offset);

        // 3) Renvoyer les éléments en question
        return $this->manager->getRepository($this->entityClass)
                    ->findAll([], [], $this->limit, $offset)
        ;
    }

    /**
     * Permet de récupèrer l'entité sur laquelle on est en train de paginer
     *
     * @return string
    */
    public function getEntityClass(): string
    {
        return $this->entityClass;
    }

    /**
     * Permet de spécifier l'entité sur laquelle on souhaite paginer
     * Par exemple :
     *  - App\Entity\Ad::class
     *  - App\Entity\Comment::class
     *
     * @param string $entityClass
     * @return self
    */
    public function setEntityClass($entityClass): self
    {
        $this->entityClass = $entityClass;

        return $this;
    }

    /**
     * Permet de récupèrer le nombre d'enregistrements qui seront renvoyes
     *
     * @return int
     */
    public function getLimit(): int
    {
        return $this->limit;
    }

    /**
     * Permet de spécifier le nombre d'enregistrements que l'on souhaite obtenir !
     *
     * @param int $limit
     * @return self
     */
    public function setLimit($limit): self
    {
        $this->limit = $limit;

        return $this;
    }

    /**
     * Permet d'afficher la page qui est actuellement affiché
     * 
     * @return int
     */ 
    public function getPage(): int
    {
        return $this->currentPage;
    }

    /**
     * Permet de spécifier la page que l'on souhaite afficher
     *
     * @return  self
     */ 
    public function setPage($page): self
    {
        $this->currentPage = $page;

        return $this;
    }

    /**
     * Permet de récupèrer le nom de la route qui sera utilisé sur les liens de la navigation
     * 
     * @return string
     */ 
    public function getRoute(): string
    {
        return $this->route;
    }

    /**
     * Permet de changer la route par défaut pour les liens de la navigation
     * 
     * @param string $route le nom de la route à utiliser
     *
     * @return  self
     */ 
    public function setRoute($route): self 
    {
        $this->route = $route;

        return $this;
    }

    /**
     * Permet de récupèrer le templatePath actuellement utilisé
     * 
     * @return string
     */ 
    public function getTemplatePath(): string
    {
        return $this->templatePath;
    }

    /**
     * Permet de choisir un template de pagination
     * 
     *@param string $templatePath
     * @return  self
     */ 
    public function setTemplatePath($templatePath): self
    {
        $this->templatePath = $templatePath;

        return $this;
    }
}