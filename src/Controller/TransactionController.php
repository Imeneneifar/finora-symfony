<?php

namespace App\Controller;

use App\Entity\TransactionWallet;
use App\Form\TransactionType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Category; 
use App\Repository\CategoryRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
class TransactionController extends AbstractController
{


#[Route('/add', name: 'transaction_add')]
public function add(Request $req, EntityManagerInterface $em): Response
{
    $transaction = new TransactionWallet();

    $transaction->setUserId(1);
    $transaction->setSource("manual");

    // 🟢 نجيبو categories قبل
    $categories = $em->getRepository(Category::class)->findAll();

    $form = $this->createForm(TransactionType::class, $transaction);
    $form->handleRequest($req);

    if ($form->isSubmitted() && $form->isValid()) {

        if ($transaction->getType() == "OUTCOME") {
            $transaction->setMontant(-abs($transaction->getMontant()));
        } else {
            $transaction->setMontant(abs($transaction->getMontant()));
        }

        $em->persist($transaction);
        $em->flush();

        return $this->redirectToRoute('transactions');
    }

    // ✅ return واحد فقط
    return $this->render('wallet/add.html.twig', [
        'form' => $form->createView(),
        'categories' => $categories
    ]);
}

    #[Route('/transactions', name: 'transactions')]
    public function list(EntityManagerInterface $em): Response
    {
        $transactions = $em->getRepository(TransactionWallet::class)->findAll();

        return $this->render('wallet/list.html.twig', [
            'transactions' => $transactions
        ]);
    }

   #[Route('/dashboard', name: 'dashboard')]
public function dashboard(EntityManagerInterface $em): Response
{
    $transactions = $em->getRepository(TransactionWallet::class)->findAll();

    $income = 0;
    $outcome = 0;

    foreach ($transactions as $t) {
        if ($t->getMontant() > 0) {
            $income += $t->getMontant();
        } else {
            $outcome += abs($t->getMontant());
        }
    }

    $balance = $income - $outcome;

    return $this->render('wallet/dashboard.html.twig', [
        'income' => $income,
        'outcome' => $outcome,
        'balance' => $balance,
    ]);
}

    #[Route('/edit/{id}', name: 'transaction_edit')]
    public function edit($id, Request $req, EntityManagerInterface $em): Response
    {
        $transaction = $em->getRepository(TransactionWallet::class)->find($id);

        if (!$transaction) {
            return new Response("Transaction non trouvée");
        }

        $form = $this->createForm(TransactionType::class, $transaction);
        $form->handleRequest($req);

        if ($form->isSubmitted() && $form->isValid()) {

            if ($transaction->getType() == "OUTCOME") {
                $transaction->setMontant(-abs($transaction->getMontant()));
            } else {
                $transaction->setMontant(abs($transaction->getMontant()));
            }

            $em->flush();

            return $this->redirectToRoute('transactions');
        }

        return $this->render('wallet/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/delete/{id}', name: 'transaction_delete')]
    public function delete($id, EntityManagerInterface $em): Response
    {
        $transaction = $em->getRepository(TransactionWallet::class)->find($id);

        if ($transaction) {
            $em->remove($transaction);
            $em->flush();
        }

        return $this->redirectToRoute('transactions');
    }



#[Route('/api/categories/{type}', name: 'categories_by_type')]
public function getCategoriesByType($type, EntityManagerInterface $em): JsonResponse
{
    $categories = $em->getRepository(Category::class)
        ->findBy(['type' => $type]);

    $data = [];

    foreach ($categories as $cat) {
        $data[] = [
            'id' => $cat->getId(),
            'nom' => $cat->getNom()
        ];
    }

    return new JsonResponse($data);
}
}