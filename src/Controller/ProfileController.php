<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ProfileEditType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;


/**
* @Route("profile")
*/
class ProfileController extends AbstractController
{
    /**
     * @Route("/", name="profile")
     */
    public function index()
    {
        return $this->render('profile/index.html.twig', [
        ]);
    }

    /**
     * @Route("/{id}/editUser", name="editUser")
     */
    public function editUser(Request $request, User $user=null)
    {
        if( $user != null){

            $form = $this->createForm(ProfileEditType::class, $user);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                
                $currentPicture = $user->getPicture();
                $photo=$form->get('picture')->getData();

                if($currentPicture === 'default.jpg')
                {
                    if($photo) {
                        $nomPhoto=uniqid() . '.'. $photo->guessExtension();        
                        try {
                            $photo->move($this->getParameter('upload_pp'),
                                $nomPhoto);
                        }        
                        catch(FileException $e) {
                            return $this->redirectToRoute('profile');
                        }        
                        $user->setPicture($nomPhoto);
                    }
                }
                if($photo != null & $currentPicture != 'default.jpg'){
                    
                    unlink($this->getParameter('upload_pp').$currentPicture);

                    if($photo) {
                        $nomPhoto=uniqid() . '.'. $photo->guessExtension();        
                        try {
                            $photo->move($this->getParameter('upload_pp'),
                                $nomPhoto);
                        }        
                        catch(FileException $e) {
                            return $this->redirectToRoute('profile');
                        }        
                        $user->setPicture($nomPhoto);
                    }
                }
                                
                $this->getDoctrine()->getManager()->flush();

                return $this->redirectToRoute('profile');
            }

            return $this->render('profile/edit.html.twig', [
                'form_editUser' => $form->createView(),
            ]);
        }
        else{
            return $this->redirectToRoute('index');
        }        
    }
}
