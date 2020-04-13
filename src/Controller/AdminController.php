<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\H\Request;
use Symfony\Component\HttpFoundation\Request as HttpFoundationRequest;
use Symfony\Component\Routing\Annotation\Route;

/**
* @Route("/admin")
*/
class AdminController extends AbstractController
{
    /**
     * @Route("/", name="admin")
     */
    public function index(UserRepository $userRepository)
    {
        return $this->render('admin/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    /**
     * @Route("/{id}/userDelete", name="user_delete")
     */
    public function delete(HttpFoundationRequest $request, User $user)
    {
        if($user->hasRole('ROLE_ADMIN'))
        {
            $this->addFlash("danger", "can't delete administrator");
            return $this->redirectToRoute('admin');
        }

        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();

            $check=$entityManager->getRepository(User::class)->findAll();
            if($check == 1 ){
                return $this->redirectToRoute('admin');
            }

            $entityManager->remove($user);
            $entityManager->flush();
        }

        $this->addFlash("success", "User deleted");
        return $this->redirectToRoute('admin');
    }

    /**
     * @Route("/{id}/editRole", name="user_editRole")
     */
    public function editRole(User $user = null)
    {
        if ($user == null){
            return $this->redirectToRoute('admin');
        }

        if ( $user->hasRole('ROLE_ADMIN')){
            $user->setRoles( ['ROLE_USER'] ); 
        }
        else{
            $user->setRoles( ['ROLE_USER','ROLE_AUTHOR','ROLE_ADMIN'] );
        }       
        
        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();

        $this->addFlash("success", "Permission changed");
        return $this->redirectToRoute('admin');

    }

    /**
     * @Route("/{id}/editRolePerm", name="editRolePerm")
     */
    public function editRolePerm(User $user = null)
    {
        if ($user == null){
            return $this->redirectToRoute('admin');
        }

        if ($user->hasRole('ROLE_AUTEUR') ){

            $user->setRoles( ['ROLE_USER'] );
           
        }
        else{
            $user->setRoles( ['ROLE_USER','ROLE_AUTEUR'] );  
        }
        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();

        $this->addFlash("success", "Role modifié");
        return $this->redirectToRoute('admin');

    }


}
