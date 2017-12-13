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
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use AppBundle\Entity\Discussion;
use AppBundle\Entity\Themes;
use UserBundle\Form\EditUserType;
use \DateTime;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Security\Core\Role\Role;



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
      $securityContext = $this->get('security.authorization_checker');
      if ($formAdd->isSubmitted() && $formAdd->isValid() && $securityContext->isGranted('IS_AUTHENTICATED_FULLY')) {
        try {
          $currUser   = $this->getUser();
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
      $user       = $this->getUser();
      $security   = $this->get('security.authorization_checker');
      $em         = $this->getDoctrine()->getManager();
      $discussion = $em
        ->getRepository('AppBundle:Discussion')
        ->findOneBy(['id' => $id]);

      if (($security->isGranted('ROLE_MODO')) or ($discussion->getAuteur()->isAuthor($user))) {
           
      }
      else {
        throw $this->createAccessDeniedException('Vous n\'etes pas autorisé à accéder à cette page !');
      }

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
      if ($formEdit->isSubmitted() && $formEdit->isValid() && $security->isGranted('IS_AUTHENTICATED_FULLY')) {
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
      } else {
        $this->denyAccessUnlessGranted('ROLE_USER', $id, 'Vous ne pouvez pas editer cet élément.');
      }

      return $this->render('AppBundle:Forum:editDiscussion.html.twig', array(
        'discussion' => $discussion,
        'theme'      => $theme,
        'formEdit'   => $formEdit->createView(),
      ));
    }

    public function removeDiscussionAction(Request $request, $theme, $id)
    {
      $user       = $this->getUser();
      $security   = $this->get('security.authorization_checker');
      $em         = $this->getDoctrine()->getManager();
      $discussion = $em
        ->getRepository('AppBundle:Discussion')
        ->findOneBy(['id' => $id]);

      if (($security->isGranted('ROLE_MODO')) or ($discussion->getAuteur()->isAuthor($user))) {
        
      } else {
        throw $this->createAccessDeniedException('Vous n\'etes pas autorisé à accéder à cette page !');
      }
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
      if ($formRemove->isSubmitted() && $formRemove->isValid() && $security->isGranted('IS_AUTHENTICATED_FULLY')) {
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
      } else {
        $this->denyAccessUnlessGranted('ROLE_USER', $id, 'Vous ne pouvez pas ajouter cet élément.');
      }

      return $this->render('AppBundle:Forum:editDiscussion.html.twig', array(
        'discussion' => $discussion,
        'theme'      => $theme,
        'formEdit'   => $formRemove->createView(),
      ));
    }

    /**
     *
     * @Security("has_role('ROLE_USER')")
     */  
    public function profilAction($id, Request $request)
    {
      $em     = $this->getDoctrine()->getManager();
      $profil = $em
        ->getRepository('UserBundle:User')
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

    /**
     *
     * @Security("has_role('ROLE_MODO')")
     */  
    public function listUserAction(Request $request)
    {
      $em    = $this->getDoctrine()->getManager();
      $users = $em
        ->getRepository('UserBundle:User')
        ->findAll();

      return $this->render('AppBundle:Forum:listUser.html.twig', array(
        'users' => $users,
      ));
    }

    public function listEditUserAction(Request $request, $id)
    {
      $currUser    = $this->getUser();
      $userManager = $this->get('fos_user.user_manager');
      $security    = $this->get('security.authorization_checker');
      $em          = $this->getDoctrine()->getManager();

      $user = $em
        ->getRepository('UserBundle:User')
        ->findOneBy(['id' => $id]);

      if (($security->isGranted('ROLE_MODO')) or ($user->isAuthor($currUser))) {
           
      }
      else {
        throw $this->createAccessDeniedException('Vous n\'etes pas autorisé à accéder à cette page !');
      }

      $roleAdmin = new Role('ROLE_ADMIN');
      $roleModo  = new Role('ROLE_MODO');
      $roleUser  = new Role('ROLE_USER');

      $formEditUser = $this->createFormBuilder()
      ->add('username', TextType::class, array('data' => $user->getUsername()))
      ->add('email', EmailType::class, array('data' => $user->getEmail()))
      ->add('nom', TextType::class, array('data' => $user->getNom(), 'required' => false))
      ->add('prenom', TextType::class, array('data' => $user->getPrenom(), 'required' => false))
      ->add('avatar', TextType::class, array('data' => $user->getAvatar(), 'required' => false))
      ->add('submit', SubmitType::class, array(
                'label' => 'Envoyer',
                'attr' => array('class' => 'btn btn-primary')
      ))
      ->getForm();

      if ($security->isGranted('ROLE_ADMIN')) {
          $formEditUser = $this->createFormBuilder()
            ->add('username', TextType::class, array('data' => $user->getUsername()))
            ->add('email', EmailType::class, array('data' => $user->getEmail()))
            ->add('nom', TextType::class, array('data' => $user->getNom(), 'required' => false))
            ->add('prenom', TextType::class, array('data' => $user->getPrenom(), 'required' => false))
            ->add('avatar', TextType::class, array('data' => $user->getAvatar(), 'required' => false))
            ->add('roles', ChoiceType::class, array('choices' => ["Admin" => $roleAdmin, "Moderateur" => $roleModo, "User" => $roleUser]))
            ->add('submit', SubmitType::class, array(
              'label' => 'Envoyer',
              'attr' => array('class' => 'btn btn-primary')
            ))
            ->getForm();
        } else {
          $formEditUser = $this->createFormBuilder()
            ->add('username', TextType::class, array('data' => $user->getUsername()))
            ->add('email', EmailType::class, array('data' => $user->getEmail()))
            ->add('nom', TextType::class, array('data' => $user->getNom(), 'required' => false))
            ->add('prenom', TextType::class, array('data' => $user->getPrenom(), 'required' => false))
            ->add('avatar', TextType::class, array('data' => $user->getAvatar(), 'required' => false))
            ->add('submit', SubmitType::class, array(
                      'label' => 'Envoyer',
                      'attr' => array('class' => 'btn btn-primary')
            ))
            ->getForm();
        }

      $formEditUser->handleRequest($request);
      if ($formEditUser->isSubmitted() && $formEditUser->isValid() && $security->isGranted('ROLE_MODO')) {
        try {
          $formEditUser = $formEditUser->getData();
          $role = array($formEditUser['roles']->getRole() => $formEditUser['roles']->getRole());
          $user->setUsername($formEditUser['username']);
          $user->setEmail($formEditUser['email']);
          $user->setNom($formEditUser['nom']);
          $user->setPrenom($formEditUser['prenom']);
          $user->setAvatar($formEditUser['avatar']);
          if ($security->isGranted('ROLE_ADMIN')) {
            $user->setRoles($role);
          }

          $userManager->updateUser($user, false);

          $em->flush();

          $request->getSession()->getFlashBag()->add('notice', 'Le profil bien modifié.');

          return $this->redirectToRoute('app_list_user_edit', array(
            'id'  => $user->getId(),
          ));
        } catch (\Exception $exc) {
          $request->getSession()->getFlashBag()->add('notice', 'Le profil n\'a pas pu être modifié.');

          return $this->redirectToRoute('app_list_user_edit', array(
            'id'  => $user->getId(),
          ));
        }
      } else {
        $this->denyAccessUnlessGranted('ROLE_USER', $id, 'Vous ne pouvez pas editer cet élément.');
      }

      return $this->render('AppBundle:Forum:editUser.html.twig', array(
        'user'         => $user,
        'formEditUser' => $formEditUser->createView(),
      ));
    }
}
