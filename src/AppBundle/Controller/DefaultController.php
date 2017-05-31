<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Form\FormType;
use AppBundle\Entity\Form;

use Symfony\Component\Form\Extension\Core\Type\SubmitType;


class DefaultController extends Controller
{
    /**
     * Fonction qui affiche des mots aléatoirement. Max permet de choisir le nombre de mot max que l'on veut utiliser
     * @Route("/word/{max}", name="word", requirements={"max": "\d+"})
     */
    public function indexAction(int $max = 0)
    {
        //génération d'un nombre aléatoire
        $number = mt_rand(0, $max);
        $words = [
            'bonjour', 'test', 'hello', 'symfony', 'hahahaha', 
            'voiture', 'moto','grand', 'petit', 'repas', 'soleil'
            ];
        $finalWord = $words[$number];

        return $this->render ('default/word.html.twig', array(
            'word' => $finalWord
        ));
    }


    /**
    *@Route(
    *       "/blog/{year}/{title}/", name="blog1",
    *       defaults={"_locale" : "fr"},
    *       requirements={
    *           "_locale": "en|fr",
    *           "year": "\d{4}",
    *           "title": "\w+"
    *        }
    *)
    *
    *Fonction qui affiche une phrase en fr on en avec les information indiqué dans la requête 
    *@Route(
    *       "/blog/{_locale}/{year}/{title}/", name="blog2",
    *       requirements={
    *           "_locale": "en|fr",
    *           "year": "\d{4}",
    *           "title": "\w+"
    *        }
    *)
    */

    public function blogAction($_locale, $year, $title)
    {
        return $this->render('default/blog.html.twig', array(
            'year' => $year,
            'locale' => $_locale,
            'title' => $title,
        ));
    }
    

    /**
    *Fonction qui affiche les article avec une note supérieur au minimum indiqué 
    *@Route("/blog/afficheMin/{min}/", name="affiche")
    *
    */
    public function afficheAction(int $min)
    {
        $repo = $this->getDoctrine()->getRepository('AppBundle:Test')
                                    ->orderByMin($min);

        $articles = $repo->execute();

            return $this->render('default/affiche.html.twig', array(
                'articles' => $articles,
            ));
    }


    /**
    *Fonction qui affiche un article en fonction de son id 
    *@Route("/blog/afficheId/{id}/", name="afficheById")
    *
    */
    public function afficheByIdAction(int $id)
    {
        $repo = $this->getDoctrine()->getRepository('AppBundle:Form');

        $article = $repo->findOneById($id);
        
        $form = $this->createDeleteForm($article);

        return $this->render('default/afficheById.html.twig', array(
            'article' => $article,
            'formDelete' => $form->createView()
        ));
    }
    
    
    /**
    *Fonction qui crée un formulaire 
    *@Route("/form/", name="form")
    *
    */ 
    public function formAction(Request $request)
    {
//        Création d'un nouveau formulaire
        $testForm = new Form();
        
//        Ligne de notre formulaire qui apparaitront à l'écran
        $form = $this->createForm( FormType::class, $testForm );
        
//        On récupère les données qui ont été ajoutées au formulaire
        $form->handleRequest($request);
        
//        On verifie que le formualire a bien été ajouté et qu'il est valide
        if( $form->isSubmitted() && $form->isValid() )
        {
            $testForm = $form->getData();
            
//            On récupère entityManager
            $em = $this->getDoctrine()->getManager();
//            On utilise persist afin de valider les informations
            $em->persist($testForm);
//            On valide et on ajoute les informations en base avec flush
            $em->flush();
            
//            On redirige vers une route qui affiche un message de confirmation
            return $this->redirectToRoute('formInfos');
        }
        
        return $this->render('default/form.html.twig', array(
            'form' => $form->createView(),
        ));

    }
    
    
    /**
    * Fonction qui confirme l'ajout à la bdd et qui
    *  affiche les données qui ont été ajouté à la bdd
    *@Route("/formInfos/", name="formInfos")
    *
    */
    public function formShowAction()
    {
        $success = "Votre inscription a bien été prise en compte, merci!";
        $repoInfos = $this->getDoctrine()
                ->getRepository('AppBundle:Form');
        
        $infos = $repoInfos->findAll();
        
        return $this->render('default/formInfos.html.twig', array(
           'infos' => $infos,
           'success' => $success
        ));
    }
    
    
    /**
    * Fonction qui met à jour la bdd en fonction de l'id
    *@Route("/formInfos/update/{id}", name="formInfosUpdateId")
    *
    */
    public function formUpdateAction(Request $request, int $id)
    {
        $em = $this->getDoctrine()->getManager();
        $formUpdate = $em->getRepository('AppBundle:Form')
                         ->findOneById($id);
        
        
//        Ligne de notre formulaire qui apparaitront à l'écran
        $form = $this->createForm( FormType::class, $formUpdate );
        
//        On récupère les données qui ont été ajoutées au formulaire
        $form->handleRequest($request);
        
//        On verifie que le formualire a bien été ajouté et qu'il est valide
        if( $form->isSubmitted() && $form->isValid() )
        {
            $formUpdate = $form->getData();
            
//            On récupère entityManager
            $em = $this->getDoctrine()->getManager();
//            On utilise persist afin de valider les informations
            $em->persist($formUpdate);
//            On valide et on ajoute les informations en base avec flush
            $em->flush();
            
//            On redirige vers une route qui affiche un message de confirmation
            return $this->redirectToRoute('formInfos');
        }
        
        return $this->render('default/form.html.twig', array(
            'form' => $form->createView(),
        ));

        
    }
    
    
    /**
     * Crée un formulaire pour supprimer un entité Article
     *
     */
    private function createDeleteForm(Form $article)
    {
        //on crée un formulaire
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('formInfosDeleteId', array('id' => $article->getId())))
            ->setMethod('DELETE')
            ->add('delete', SubmitType::class)
            ->getForm()
        ;
    } 
    
    /**
    * Fonction qui met efface une entrée de la bdd
    *@Route("/formDelete/{id}", name="formInfosDeleteId")
    *
    */      
    public function formDeleteAction(Request $request, Form $formEntity)
    {

        $formDelete = $this->createDeleteForm($formEntity);
        
        $formDelete->handleRequest($request);
        
//        On verifie que le formualire a bien été ajouté et qu'il est valide
        if( $formDelete->isSubmitted() && $formDelete->isValid() )
        {
            $em = $this->getDoctrine()->getManager();
            $em->remove($formEntity);
            $em->flush();
            
            return $this->redirectToRoute('formInfos');
        }
        
        
        
    }
}
