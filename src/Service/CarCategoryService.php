<?php

namespace App\Service;

use App\Entity\CarCategory;
use App\Form\CarCategoryType;
use App\Repository\CarCategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

class CarCategoryService
{
    // Injecting dependencies via constructor
    public function __construct(
        private CarCategoryRepository $carCategoryRepository,
        private EntityManagerInterface $entityManager,
        private FormFactoryInterface $formFactory,
        private CsrfTokenManagerInterface $csrfTokenManager
    ) {
    }

    // Fetch all car categories from the database
    public function getAllCategories(): array
    {
        return $this->carCategoryRepository->findAll();
    }

    // Create a new car category and handle the request
    public function createCategory(Request $request): array
    {
        // Create new category object
        $carCategory = new CarCategory();

        // Create form and handle request data
        $form = $this->formFactory->create(CarCategoryType::class, $carCategory);
        $form->handleRequest($request);

        // If form is valid, persist and flush the entity manager
        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($carCategory);
            $this->entityManager->flush();
        }

        // Return both the form and the entity object
        return [$form, $carCategory];
    }

    // Delete a car category if the CSRF token is valid
    public function deleteCategory(Request $request, CarCategory $carCategory): bool
    {
        // Validate CSRF token
        $token = new CsrfToken('delete'.$carCategory->getId(), $request->request->get('_token'));

        // If token is valid, remove entity and flush
        if ($this->csrfTokenManager->isTokenValid($token)) {
            $this->entityManager->remove($carCategory);
            $this->entityManager->flush();

            return true;
        }

        return false;
    }

    // Update a car category if form is valid
    public function updateCategory(Request $request, CarCategory $carCategory): array
    {
        // Create form and handle request data
        $form = $this->formFactory->create(CarCategoryType::class, $carCategory);
        $form->handleRequest($request);

        // If form is valid, flush the entity manager
        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();
        }

        // Return both the form and the entity object
        return [$form, $carCategory];
    }
}
