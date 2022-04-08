<?php

namespace App\Controller;


use App\Entity\Activiteit;
use App\Entity\Soortactiviteit;
use App\Form\ActiviteitType;
use App\Form\SoortactiviteitType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\ResetType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;

class MedewerkerController extends AbstractController
{
    /**
     * @Route("/admin/activiteiten", name="activiteitenoverzicht")
     */
    public function activiteitenOverzichtAction()
    {

        $activiteiten=$this->getDoctrine()
            ->getRepository('App:Activiteit')
            ->findAll();

        return $this->render('medewerker/activiteiten.html.twig', [
            'activiteiten'=>$activiteiten
        ]);
    }

    /**
     * @Route("/admin/details/{id}", name="details")
     */
    public function detailsAction($id)
    {
        $activiteiten=$this->getDoctrine()
            ->getRepository('App:Activiteit')
            ->findAll();
        $activiteit=$this->getDoctrine()
            ->getRepository('App:Activiteit')
            ->find($id);

        $deelnemers=$this->getDoctrine()
            ->getRepository('App:User')
            ->getDeelnemers($id);


        return $this->render('medewerker/details.html.twig', [
            'activiteit'=>$activiteit,
            'deelnemers'=>$deelnemers,
            'aantal'=>count($activiteiten)
        ]);
    }

    /**
     * @Route("/admin/beheer", name="beheer")
     */
    public function beheerAction()
    {
        $activiteiten=$this->getDoctrine()
            ->getRepository('App:Activiteit')
            ->findAll();

        return $this->render('medewerker/beheer.html.twig', [
            'activiteiten'=>$activiteiten
        ]);
    }

    /**
     * @Route("/admin/add", name="add")
     */
    public function addAction(Request $request)
    {
        // create a user and a contact
        $a=new Activiteit();

        $form = $this->createForm(ActiviteitType::class, $a);
        $form->add('save', SubmitType::class, array('label'=>"voeg toe"));
        //$form->add('reset', ResetType::class, array('label'=>"reset"));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->persist($a);
            $em->flush();

            $this->addFlash(
                'notice',
                'activiteit toegevoegd!'
            );
            return $this->redirectToRoute('beheer');
        }
        $activiteiten=$this->getDoctrine()
            ->getRepository('App:Activiteit')
            ->findAll();
        return $this->render('medewerker/add.html.twig',array('form'=>$form->createView(),'naam'=>'toevoegen','aantal'=>count($activiteiten)
            ));
    }

    /**
     * @Route("/admin/update/{id}", name="update")
     */
    public function updateAction($id,Request $request)
    {
        $a=$this->getDoctrine()
            ->getRepository('App:Activiteit')
            ->find($id);

        $form = $this->createForm(ActiviteitType::class, $a);
        $form->add('save', SubmitType::class, array('label'=>"aanpassen"));

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();

            // tells Doctrine you want to (eventually) save the contact (no queries yet)
            $em->persist($a);


            // actually executes the queries (i.e. the INSERT query)
            $em->flush();
            $this->addFlash(
                'notice',
                'activiteit aangepast!'
            );
            return $this->redirectToRoute('beheer');
        }

        $activiteiten=$this->getDoctrine()
            ->getRepository('App:Activiteit')
            ->findAll();

        return $this->render('medewerker/add.html.twig',array('form'=>$form->createView(),'naam'=>'aanpassen','aantal'=>count($activiteiten)));
    }

    /**
     * @Route("/admin/delete/{id}", name="delete")
     */
    public function deleteAction($id)
    {
        $em=$this->getDoctrine()->getManager();
        $a= $this->getDoctrine()
            ->getRepository('App:Activiteit')->find($id);
        $em->remove($a);
        $em->flush();

        $this->addFlash(
            'notice',
            'activiteit verwijderd!'
        );
        return $this->redirectToRoute('beheer');

    }

    /**
     * @Route("/admin/soortactiviteit", name="app_soortactiviteit_index")
     */
    public function index(EntityManagerInterface $em)
    {
        $soort = $em->getRepository(Soortactiviteit::class)->findAll();
        return $this->render('soortactiviteit/index.html.twig', [
            'soortactiviteits' => $soort,
        ]);
    }

    /**
     * @Route("/admin/soortactiviteit/new", name="app_soortactiviteit_new", methods={"GET", "POST"})
     */
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $soortactiviteit = new Soortactiviteit();
        $form = $this->createForm(SoortactiviteitType::class, $soortactiviteit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($soortactiviteit);
            $entityManager->flush();

            return $this->redirectToRoute('app_soortactiviteit_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('soortactiviteit/new.html.twig', [
            'soortactiviteit' => $soortactiviteit,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/soortactiviteit/{id}", name="app_soortactiviteit_show", methods={"GET"})
     */
    public function show(Soortactiviteit $soortactiviteit): Response
    {
        return $this->render('soortactiviteit/show.html.twig', [
            'soortactiviteit' => $soortactiviteit,
        ]);
    }

    /**
     * @Route("/admin/soortactiviteit/{id}/edit", name="app_soortactiviteit_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Soortactiviteit $soortactiviteit, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(SoortactiviteitType::class, $soortactiviteit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_soortactiviteit_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('soortactiviteit/edit.html.twig', [
            'soortactiviteit' => $soortactiviteit,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/soortactiviteit/{id}", name="app_soortactiviteit_delete", methods={"POST"})
     */
    public function delete(Request $request, Soortactiviteit $soortactiviteit, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$soortactiviteit->getId(), $request->request->get('_token'))) {
            $entityManager->remove($soortactiviteit);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_soortactiviteit_index', [], Response::HTTP_SEE_OTHER);
    }
}
