<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CategoryController extends AbstractController
{
    #[Route('/categories', name: 'category_list')]
    public function list(EntityManagerInterface $em): Response
    {
        $categories = $em->getRepository(Category::class)->findAll();

        return $this->render('category/listC.html.twig', [
            'categories' => $categories
        ]);
    }

    #[Route('/category/add', name: 'category_add')]
    public function add(Request $req, EntityManagerInterface $em): Response
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);

        $form->handleRequest($req);

        if ($form->isSubmitted() && $form->isValid()) {

            $category->setUserId(1); // temporaire

            $em->persist($category);
            $em->flush();

            return $this->redirectToRoute('category_list');
        }

        return $this->render('category/addC.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/category/edit/{id}', name: 'category_edit')]
    public function edit($id, Request $req, EntityManagerInterface $em): Response
    {
        $category = $em->getRepository(Category::class)->find($id);

        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($req);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            return $this->redirectToRoute('category_list');
        }

        return $this->render('category/editC.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/category/delete/{id}', name: 'category_delete')]
    public function delete($id, EntityManagerInterface $em): Response
    {
        $category = $em->getRepository(Category::class)->find($id);

        $em->remove($category);
        $em->flush();

        return $this->redirectToRoute('category_list');
    }
}