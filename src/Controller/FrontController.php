<?php

namespace App\Controller;

use App\Entity\Produit;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class FrontController extends AbstractController
{
    #[Route('/', name: 'homepage')]
    public function home(ManagerRegistry $doctrine): Response
    {
        $produits = $doctrine->getRepository(Produit::class)->productList();
        return $this->render('front/index.html.twig', ['produits' => $produits]);
    }

    #[Route('/produit/{id}/{url}', name: 'produit', requirements: ['id' => "\d+", 'url'=>'.{1,}'])]
    #[ParamConverter('Produit', class: Produit::class)]
    public function produit(produit $produit, Request $request, SessionInterface $session): Response
    {

        if($request->request->get('ajout')){

            $quantite = $request->request->get('quantite');
            $typeProduit = $request->request->get('produit');

            $session->set('quantite', $quantite, 'produit', $typeProduit);

            dump($session);

            return $this->redirectToRoute('panier');
        
        }

        return $this->render('front/produit.html.twig', ['produit' => $produit]);
    }

    #[Route('/panier', name: 'panier')]
    public function panier(SessionInterface $session): Response
    {
        $quantite = $session->get('quantite');
        $produit = $session->get('produit');

        return $this->render('front/panier.html.twig', ['quantite' => $quantite, 'produit' => $produit]);
    }

    public function menu(){
        $listMenu = [
            ['title'=> "Site E-commerce", "text"=>'Acceuil', "url"=> $this->generateUrl('homepage')],
            ['title'=> "Panier", "text"=>'Panier', "url"=> $this->generateUrl('panier')],
        ];

        return $this->render("parts/menu.html.twig", ["listMenu" => $listMenu]);
    }

}
