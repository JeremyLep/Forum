<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use AppBundle\Entity\Discussion;
use AppBundle\Entity\Themes;
use \DateTime;


class ForumController extends Controller
{
  public function indexAction(Request $request, $page)
  {
    if ($page < 1) {
      return $this->redirectToRoute('app_home');
    }

    $nbPerPage = 5;

    $em     = $this->getDoctrine()->getManager();

    $themes = $em
      ->getRepository('AppBundle:Themes')
      ->getThemes($page, $nbPerPage);

    $infosThemes = $em
      ->getRepository('AppBundle:Discussion')
      ->getInfoTheme($page, $nbPerPage);

    $nbDisc = $em
      ->getRepository('AppBundle:Discussion')
      ->getNbDiscussion(); 

    $nbPages = ceil(count($themes)/$nbPerPage);

    if ($page > $nbPages) {
      throw $this->createNotFoundException("La page ".$page." n'existe pas.");
    }

    $formAdd = $this
          ->createFormBuilder()
          ->add('titre', TextType::class, array('label' => 'Titre du thème'))
          ->add('soustitre', TextareaType::class, array('label' => 'Sous-titre du thème'))
          ->add('submit', SubmitType::class, array('label' => 'Envoyer'))
          ->getForm();

    $formAdd->handleRequest($request);

    if ($formAdd->isSubmitted() && $formAdd->isValid()) {
      try {
        $formAdd = $formAdd->getData();
        $theme   = new Themes();
        $theme->setTitre($formAdd['titre']);
        $theme->setSousTitre($formAdd['soustitre']);
        $theme->setNbDiscussion(0);
        
        $em->persist($theme);
        $em->flush();

        $request->getSession()->getFlashBag()->add('notice', 'Le thème est bien enregistré.');

        return $this->redirectToRoute('app_home', array(
          'page'  => 1,
        ));
      } catch (\Exception $exc) {
        $request->getSession()->getFlashBag()->add('notice', 'Le thème n\'a pas pu être enregistré.');

        return $this->redirectToRoute('app_home', array(
          'page'  => 1,
        ));
      }
    }

    return $this->render('AppBundle:Forum:index.html.twig', array(
      'themes'      => $themes,
      'infosThemes' => $infosThemes,
      'nbDisc'      => $nbDisc,
      'nbPages'     => $nbPages,
      'page'        => $page,
      'formAdd'     => $formAdd->createView(),
    ));
  }

    public function discussionAction(Request $request, $theme, $page)
    {
      if ($page < 1) {
        return $this->redirectToRoute('app_discussion', array(
          'theme' => $theme,
          'page' => 1,
        ));
      }
      $nbPerPage   = 10;
      $em          = $this->getDoctrine()->getManager();
      $theme       = $em
        ->getRepository('AppBundle:Themes')
        ->findOneBy(['titre' => $theme]);

      $discussions = $em
        ->getRepository('AppBundle:Discussion')
        ->getDiscussions($theme->getTitre(), $page, $nbPerPage);

      $infosDisc   = $em
        ->getRepository('AppBundle:Discussion')
        ->getInfoCountDiscussions($theme->getTitre());

      if (count($discussions) > 0) {
        $nbPages = ceil(count($discussions)/$nbPerPage);

        if ($page > $nbPages) {
          throw $this->createNotFoundException("La page ".$page." n'existe pas.");
        }
      } else {
        $nbPages = 1;
      }

      $formAdd = $this
          ->createFormBuilder()
          ->add('discussion', TextareaType::class)
          ->add('submit', SubmitType::class, array('label' => 'Envoyer'))
          ->getForm();

      $formAdd->handleRequest($request);

      if ($formAdd->isSubmitted() && $formAdd->isValid()) {
        try {
        // USE SESSION TO GET SESSION USER OBJECT //
          $currUser   = $em
            ->getRepository('AppBundle:User')
            ->findOneBy(['id' => 1]);
        // ---------------------------------------- //

          $formAdd    = $formAdd->getData();
          $discussion = new Discussion();
          $discussion->setContenu($formAdd['discussion']);
          $discussion->setAuteur($currUser);
          $discussion->setTheme($theme);
          $theme->addDiscussion($discussion);
        
          $em->persist($discussion);
          $em->persist($theme);
          $em->flush();

          $request->getSession()->getFlashBag()->add('notice', 'Post bien enregistré.');

          return $this->redirectToRoute('app_discussion', array(
            'theme' => $theme->getTitre(),
            'page'  => 1,
          ));
        } catch (\Exception $exc) {
          $request->getSession()->getFlashBag()->add('notice', 'Post n\'a pas pu être enregistré.');

          return $this->redirectToRoute('app_discussion', array(
            'theme' => $theme->getTitre(),
            'page'  => 1,
          ));
        }
      }

      return $this->render('AppBundle:Forum:discussion.html.twig', array(
        'discussions' => $discussions,
        'theme'       => $theme,
        'infosDisc'   => $infosDisc,
        'nbPages'     => $nbPages,
        'page'        => $page,
        'formAdd'     => $formAdd->createView(),
      ));
    }

    public function editDiscussionAction(Request $request, $theme, $id)
    {
      $em         = $this->getDoctrine()->getManager();
      $discussion = $em
        ->getRepository('AppBundle:Discussion')
        ->findOneBy(['id' => $id]);

      $theme = $em
        ->getRepository('AppBundle:Themes')
        ->findOneBy(['titre' => $theme]);

      $formEdit = $this
          ->createFormBuilder()
          ->add('discussion', TextareaType::class, array(
          'data' => $discussion->getContenu()))
          ->add('submit', SubmitType::class, array(
            'label' => 'Envoyer',
            'attr' => array('class' => 'btn btn-primary'),
          ))
          ->getForm();

      $formEdit->handleRequest($request);

      if ($formEdit->isSubmitted() && $formEdit->isValid()) {
        try {
          $formEdit    = $formEdit->getData();
          $discussion->setContenu($formEdit['discussion']);
        
          $em->persist($discussion);
          $em->flush();

          $request->getSession()->getFlashBag()->add('notice', 'Post bien modifié.');

          return $this->redirectToRoute('app_edit_discussion', array(
            'theme' => $theme->getTitre(),
            'id'  => $discussion->getId(),
          ));
        } catch (\Exception $exc) {
          $request->getSession()->getFlashBag()->add('notice', 'Post n\'a pas pu être modifié.');

          return $this->redirectToRoute('app_edit_discussion', array(
            'theme' => $theme->getTitre(),
            'id'  => $discussion->getId(),
          ));
        }
      }

      return $this->render('AppBundle:Forum:editDiscussion.html.twig', array(
        'discussion' => $discussion,
        'theme'      => $theme,
        'formEdit'   => $formEdit->createView(),
      ));
    }

    public function removeDiscussionAction(Request $request, $theme, $id)
    {
      $em         = $this->getDoctrine()->getManager();
      $discussion = $em
        ->getRepository('AppBundle:Discussion')
        ->findOneBy(['id' => $id]);

      $theme = $em
        ->getRepository('AppBundle:Themes')
        ->findOneBy(['titre' => $theme]);

      $formRemove = $this
          ->createFormBuilder()
          ->add('discussion', TextareaType::class, array(
          'data' => $discussion->getContenu(), 'disabled' => true))
          ->add('submit', SubmitType::class, array(
            'label' => 'Supprimer',
            'attr' => array('class' => 'btn btn-danger')
          ))
          ->getForm();

      $formRemove->handleRequest($request);

      if ($formRemove->isSubmitted() && $formRemove->isValid()) {
        try {

          $theme->removeDiscussion($discussion);
          $em->remove($discussion);
          $em->persist($theme);
          $em->flush();

          $request->getSession()->getFlashBag()->add('notice', 'Post bien supprimé.');

          return $this->redirectToRoute('app_discussion', array(
            'theme' => $theme->getTitre(),
            'page'  => 1,
          ));
          
        } catch (\Exception $exc) {
          $request->getSession()->getFlashBag()->add('notice', 'Post n\'a pas pu être supprimé.');

          return $this->redirectToRoute('app_remove_discussion', array(
            'theme' => $theme->getTitre(),
            'id'    => $id,
          ));
        }
      }

      return $this->render('AppBundle:Forum:editDiscussion.html.twig', array(
        'discussion' => $discussion,
        'theme'      => $theme,
        'formEdit' => $formRemove->createView(),
      ));
    }

    public function profilAction($id, Request $request)
    {
      $em     = $this->getDoctrine()->getManager();
      $profil = $em
        ->getRepository('AppBundle:User')
        ->find($id);
      $stat   = $em
        ->getRepository('AppBundle:Discussion')
        ->getStatProfil($id);
      $statGlobal = $em
        ->getRepository('AppBundle:Discussion')
        ->getStatSite();

      $maxItemP   = array(
                      'theme' => $stat[0]['COUNT(distinct d.theme_id)'], 
                      'disc'  => $stat[0]['COUNT(d.id)'],
                    );

      $maxItemS   = array(
                      'theme' => $statGlobal[0]['COUNT(distinct d.theme_id)'], 
                      'disc'  => $statGlobal[0]['COUNT(d.id)'],
                    );
      $now        = new DateTime();
      $dateProfil = $profil->getDateInscription();
      $days       = $dateProfil->diff($now)->days;

      if ($days == 0) {
        $days = $maxItemP['disc'];
      } else {
        $days = $maxItemP['disc'] / $days;
      }

      $pourcentDisc  = (100 * $maxItemP['disc'])/($maxItemS['disc']);
      $pourcentTheme = (100 * $maxItemP['theme'])/($maxItemS['theme']);

      $form = $this->createFormBuilder()
            ->add('sujet', TextType::class, array('label' => 'Sujet '))
            ->add('contenu', TextareaType::class, array('label' => 'Contenu '))
            ->add('date', HiddenType::class)
            ->add('submit', SubmitType::class, array('label' => 'Envoyer par mail'))
            ->getForm();

      $form->handleRequest($request);

      if ($form->isSubmitted() && $form->isValid()) {
        $mail    = $form->getData();
        $message = \Swift_Message::newInstance()
          ->setSubject($mail['sujet'])
          ->setFrom('forum@mail.com')
          ->setTo($profil->getEmail())
          ->setBody($mail['contenu']);
 
        if ($this->get('mailer')->send($message)) {
            $request->getSession()->getFlashBag()->add('success', 'Succès ! Votre mail a bien été envoyé !');
        } else {
            $request->getSession()->getFlashBag()->add('danger', 'Erreur ! Votre mail n\'a pas pu s\'envoyer !');
        }
        
        return $this->redirectToRoute('app_profil', array('id' => $id));
      }

      return $this->render('AppBundle:Forum:profil.html.twig', array(
        'profil'        => $profil,
        'maxItemP'      => $maxItemP,
        'maxItemS'      => $maxItemS,
        'pourcentDisc'  => $pourcentDisc,
        'pourcentTheme' => $pourcentTheme,
        'days'          => $days,
        'id'            => $id,
        'form'          => $form->createView(),
      ));
    }
}
