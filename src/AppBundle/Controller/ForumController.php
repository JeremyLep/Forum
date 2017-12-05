<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class ForumController extends Controller
{


    public function indexAction()
    {
      $em     = $this->getDoctrine()->getManager();
      $themes = $em
          ->getRepository('AppBundle:Themes')
          ->getThemesLastDiscus();
      $nbDisc = $em
          ->getRepository('AppBundle:Discussion')
          ->getNbDiscussion(); 

      return $this->render('AppBundle:Forum:index.html.twig', array(
        'themes' => $themes,
        'nbDisc' => $nbDisc,
      ));
    }

    public function discussionAction($theme)
    {
      $em          = $this->getDoctrine()->getManager();
      $discussions = $em
        ->getRepository('AppBundle:Discussion')
        ->getDiscussions($theme);
      $infosDisc   = $em
        ->getRepository('AppBundle:Discussion')
        ->getInfoCountDiscussions($theme);

      return $this->render('AppBundle:Forum:discussion.html.twig', array(
        'discussions' => $discussions,
        'theme' => $theme,
        'infosDisc'=> $infosDisc,
      ));
    }
}
