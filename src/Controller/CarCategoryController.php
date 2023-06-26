<?php

namespace App\Controller;

use App\Entity\CarCategory;
use App\Service\CarCategoryService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/car/category')]
class CarCategoryController extends AbstractController
{
    // Inject the CarCategoryService via the constructor
    public function __construct(private CarCategoryService $carCategoryService)
    {
    }

    // Display all car categories on the index page
    #[Route('/', name: 'app_car_category_index', methods: ['GET'])]
    public function index(): Response
    {
        // Render the index page with all categories
        return $this->render('car_category/index.html.twig', [
            'car_categories' => $this->carCategoryService->getAllCategories(),
        ]);
    }

    // Handle the creation of a new car category
    #[Route('/new', name: 'app_car_category_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        // Create a new category and handle the request
        [$form, $carCategory] = $this->carCategoryService->createCategory($request);

        // If the form is submitted and valid, redirect to the index page
        if ($form->isSubmitted() && $form->isValid()) {
            return $this->redirectToRoute('app_car_category_index', [], Response::HTTP_SEE_OTHER);
        }

        // Otherwise, render the new category page
        return $this->render('car_category/new.html.twig', [
            'car_category' => $carCategory,
            'form' => $form,
        ]);
    }

    // Show a specific car category
    #[Route('/{id}', name: 'app_car_category_show', methods: ['GET'])]
    public function show(CarCategory $carCategory): Response
    {
        // Render the show page for the specific category
        return $this->render('car_category/show.html.twig', [
            'car_category' => $carCategory,
        ]);
    }

    // Edit a specific car category
    #[Route('/{id}/edit', name: 'app_car_category_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, CarCategory $carCategory): Response
    {
        // Handle the update request for the category
        [$form, $carCategory] = $this->carCategoryService->updateCategory($request, $carCategory);

        // If the form is submitted and valid, redirect to the index page
        if ($form->isSubmitted() && $form->isValid()) {
            return $this->redirectToRoute('app_car_category_index', [], Response::HTTP_SEE_OTHER);
        }

        // Otherwise, render the edit page
        return $this->render('car_category/edit.html.twig', [
            'car_category' => $carCategory,
            'form' => $form,
        ]);
    }

    // Delete a specific car category
    #[Route('/{id}', name: 'app_car_category_delete', methods: ['POST'])]
    public function delete(Request $request, CarCategory $carCategory): Response
    {
        // Delete the category if the CSRF token is valid
        if ($this->carCategoryService->deleteCategory($request, $carCategory)) {
            return $this->redirectToRoute('app_car_category_index', [], Response::HTTP_SEE_OTHER);
        }

        // If CSRF token is invalid, stay on the current page
        return $this->redirectToRoute('app_car_category_show', ['id' => $carCategory->getId()]);
    }
}
