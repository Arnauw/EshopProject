<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\ProductHistory;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/admin/product')]
final class ProductController extends AbstractController
{
    #[Route(name: 'app_product_index', methods: ['GET'])]
    public function index(ProductRepository $productRepository): Response
    {
        return $this->render('product/index.html.twig', [
            'products' => $productRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_product_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product, [
            'is_edit-new_form' => true,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $image = $form->get('image')->getData();

            if ($image) {
                $originalFilename = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid('', true).'.'.$image->guessExtension();
                // TODO: Reencode the image to webp and compress. Also add image description. (for alt tag)
                try {
                    $image->move(
                        $this->getParameter('product_images_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    $product->setImage($newFilename);
                    // Handle exception if something happens during file upload
                }

                $product->setImage($newFilename);
            }

            $entityManager->persist($product);
            $entityManager->flush();

            $stockHistory = new ProductHistory();
            $stockHistory->setProduct($product);
            $stockHistory->setQuantity($product->getStock());
            $stockHistory->setCreatedAt(new \DateTimeImmutable('now', new \DateTimeZone('Europe/Paris')));
            $entityManager->persist($stockHistory);
            $entityManager->flush();

            $this->addFlash('success', 'Product created successfully!');
            return $this->redirectToRoute('app_product_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('product/new.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_product_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Product $product, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(ProductType::class, $product, [
            'is_edit-new_form' => true,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $image = $form->get('image')->getData();


            if ($image) {
                $originalFilename = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid('', true).'.'.$image->guessExtension();

                try {
                    $image->move(
                        $this->getParameter('product_images_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    $product->setImage($newFilename);
                    // Handle exception if something happens during file upload
                }

                $product->setImage($newFilename);
            }


            $entityManager->flush();

            $this->addFlash('success', 'Product updated successfully!');
            return $this->redirectToRoute('app_product_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('product/edit.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/addStock', name: 'app_product_addStock', methods: ['GET', 'POST'])]
    public function addStock(Request $request, Product $product, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $oldStock = $product->getStock();
//        var_dump($oldStock);

        $form = $this->createForm(ProductType::class, $product, [
            'is_stock_edit_form' => true,
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $newStock = $form->get('stock')->getData() + $oldStock;
//            var_dump($form->get('stock')->getData());
            $product->setStock($newStock);

            $entityManager->persist($product);
            $entityManager->flush();

            $stockHistory = new ProductHistory();
            $stockHistory->setProduct($product);
            $stockHistory->setQuantity($product->getStock());
            $stockHistory->setCreatedAt(new \DateTimeImmutable('now', new \DateTimeZone('Europe/Paris')));
            $entityManager->persist($stockHistory);
            $entityManager->flush();

            $this->addFlash('success', 'Product stock add
            ed successfully!');
            return $this->redirectToRoute('app_product_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('product/addStock.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/history', name: 'app_product_history_show', methods: ['GET'])]
    public function historyShow(Product $product): Response
    {
        return $this->render('product/historyShow.html.twig', [
            'product' => $product,
        ]);
    }

    #[Route('/{id}', name: 'app_product_delete', methods: ['POST'])]
    public function delete(Request $request, Product $product, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$product->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($product);
            $entityManager->flush();
            $this->addFlash('success', 'Product deleted successfully!');
        }

        return $this->redirectToRoute('app_product_index', [], Response::HTTP_SEE_OTHER);
    }
}
