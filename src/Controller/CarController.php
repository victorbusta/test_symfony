<?php

namespace App\Controller;

use App\Entity\Car;
use App\Service\CarService;
use App\Repository\CarCategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/car')]
class CarController extends AbstractController
{
    public function __construct(private CarService $carService) {}

    /**
     * Lists and filters cars.
     */
    #[Route('/', name: 'app_car_index', methods: ['GET', 'POST'])]
    public function indexClient(Request $request, CarCategoryRepository $carCategoryRepository): Response
    {
        // Filter cars based on the request
        [$form, $pagination] = $this->carService->filterCars($request);

        // Render the index page with the filtered cars, pagination, and car categories
        return $this->render('car/index.html.twig', [
            'pagination' => $pagination,
            'carCategories' => $carCategoryRepository->findAll(),
            'form' => $form->createView(),
        ]);
    }

    /**
     * Lists all cars.
     */
    #[Route('/all', name: 'app_car_all', methods: ['GET', 'POST'])]
    public function index(Request $request): Response
    {
        [$form, $pagination] = $this->carService->filterCars($request);

        // Get all cars
        return $this->render('car/showall.html.twig', [
            'pagination' => $pagination,
            'cars' => $this->carService->getAllCars(),
            'form' => $form->createView(),
        ]);
    }

    /**
     * Handles car creation.
     */
    #[Route('/new', name: 'app_car_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        // Handle the car form submission
        $formResult = $this->carService->handleCarForm($request);

        // If the form is successfully submitted, redirect to the car index page
        if ($formResult === true) {
            return $this->redirectToRoute('app_car_all', [], Response::HTTP_SEE_OTHER);
        }

        // If the form has errors or is not submitted, render the new car page with the form and car entity
        [$car, $form] = $formResult;

        return $this->render('car/new.html.twig', [
            'car' => $car,
            'form' => $form,
        ]);
    }

    /**
     * Shows a car.
     */
    #[Route('/{id}', name: 'app_car_show', methods: ['GET'])]
    public function show(Car $car): Response
    {
        // Render the car show page with the specified car entity
        return $this->render('car/show.html.twig', [
            'car' => $car,
        ]);
    }

    /**
     * Handles car editing.
     */
    #[Route('/{id}/edit', name: 'app_car_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Car $car): Response
    {
        // Handle the car form submission for editing
        $formResult = $this->carService->handleCarForm($request, $car);

        // If the form is successfully submitted, redirect to the car index page
        if ($formResult === true) {
            return $this->redirectToRoute('app_car_all', [], Response::HTTP_SEE_OTHER);
        }

        // If the form has errors or is not submitted, render the edit car page with the form and car entity
        [$car, $form] = $formResult;

        return $this->render('car/edit.html.twig', [
            'car' => $car,
            'form' => $form,
        ]);
    }

    /**
     * Handles car deletion.
     */
    #[Route('/{id}', name: 'app_car_delete', methods: ['POST'])]
    public function delete(Request $request, Car $car): Response
    {
        // Delete the specified car
        if ($this->carService->deleteCar($request, $car)) {
            // If the car is successfully deleted, redirect to the car index page
            return $this->redirectToRoute('app_car_all', [], Response::HTTP_SEE_OTHER);
        }

        // If the car deletion fails, redirect to the car index page
        return $this->redirectToRoute('app_car_all', [], Response::HTTP_SEE_OTHER);
    }
}
