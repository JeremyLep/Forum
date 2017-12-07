<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

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
}
