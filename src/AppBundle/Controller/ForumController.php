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
use \DateTime;


class ForumController extends Controller
{
  public function indexAction($page)
  {
    if ($page < 1) {
      return $this->redirectToRoute('app_home');
    }

    $nbPerPage = 5;

    $em     = $this->getDoctrine()->getManager();
    $themes = $em
      ->getRepository('AppBundle:Themes')
      ->getThemesLastDiscus($page, $nbPerPage);

    $nbDisc = $em
      ->getRepository('AppBundle:Discussion')
      ->getNbDiscussion(); 

    $nbPages = ceil(count($themes)/$nbPerPage);

    if ($page > $nbPages) {
      throw $this->createNotFoundException("La page ".$page." n'existe pas.");
    }

    return $this->render('AppBundle:Forum:index.html.twig', array(
      'themes'   => $themes,
      'nbDisc'   => $nbDisc,
      'nbPages'  => $nbPages,
      'page'     => $page,
    ));
  }

    public function discussionAction($theme, $page)
    {
      if ($page < 1) {
        return $this->redirectToRoute('app_discussion', array(
          'theme' => $theme, 
          'page' => 1,
        ));
      }
      $nbPerPage   = 10;
      $em          = $this->getDoctrine()->getManager();
      $discussions = $em
        ->getRepository('AppBundle:Discussion')
        ->getDiscussions($theme, $page, $nbPerPage);

      $infosDisc   = $em
        ->getRepository('AppBundle:Discussion')
        ->getInfoCountDiscussions($theme);

      $nbPages = ceil(count($discussions)/$nbPerPage);

      if ($page > $nbPages) {
        throw $this->createNotFoundException("La page ".$page." n'existe pas.");
      }

      return $this->render('AppBundle:Forum:discussion.html.twig', array(
        'discussions' => $discussions,
        'theme'       => $theme,
        'infosDisc'   => $infosDisc,
        'nbPages'     => $nbPages,
        'page'        => $page,
      ));
    }

    public function profilAction($id, Request $request)
    {
      $em     = $this->getDoctrine()->getManager();
      $profil = $em
        ->getRepository('AppBundle:Membres')
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
      $days       = $maxItemP['disc'] / $days;

      $pourcentDisc      = (100 * $maxItemP['disc'])/($maxItemS['disc']);
      $pourcentTheme     = (100 * $maxItemP['theme'])/($maxItemS['theme']);

      $form = $this->createFormBuilder()
            ->add('sujet', TextType::class, array('label' => 'Sujet '))
            ->add('contenu', TextareaType::class, array('label' => 'Contenu '))
            ->add('date', HiddenType::class)
            ->add('submit', SubmitType::class, array('label' => 'Envoyer par mail'))
            ->getForm();

      $form->handleRequest($request);

      if ($form->isSubmitted() && $form->isValid()) {
        $mail = $form->getData();

        $message = \Swift_Message::newInstance()
          ->setSubject($mail['sujet'])
          ->setFrom('forum@mail.com')
          ->setTo($profil->getEmail())
          ->setBody($mail['contenu']);
 
        if ($this->get('mailer')->send($message)) {
            $request->getSession()->getFlashBag()->add('success', 'Success ! Votre mail a bien été envoyé !');
        } else {
            $request->getSession()->getFlashBag()->add('danger', 'Erreur ! Votre mail n\'a pas pu s\'envoyer !');
        }
        
        return $this->redirectToRoute('app_profil', array('id' => $id));
      }

      return $this->render('AppBundle:Forum:profil.html.twig', array(
        'profil'   => $profil,
        'maxItemP' => $maxItemP,
        'maxItemS' => $maxItemS,
        'pourcentDisc'    => $pourcentDisc,
        'pourcentTheme'   => $pourcentTheme,
        'days'     => $days,
        'id'       => $id,
        'form' => $form->createView(),
      ));
    }
}
