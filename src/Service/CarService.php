<?php

namespace App\Service;

use App\Entity\Car;
use App\Form\CarFilterType;
use App\Form\CarType;
use App\Repository\CarRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

class CarService
{
    public function __construct(
        private CarRepository $carRepository,
        private EntityManagerInterface $entityManager,
        private PaginatorInterface $paginator,
        private FormFactoryInterface $formFactory,
        private CsrfTokenManagerInterface $csrfTokenManager
    ) {
    }

    /**
     * Handles car filtering and pagination.
     */
    public function filterCars(Request $request): array
    {
        // Create and handle the filter form
        $form = $this->formFactory->create(CarFilterType::class);
        $form->handleRequest($request);

        $categoryFilter = null;
        $nameSearch = null;

        // Get filter values if form is valid
        if ($form->isSubmitted() && $form->isValid()) {
            $categoryFilter = $form->get('category')->getData();
            $nameSearch = $form->get('name')->getData();
        }

        // Filter and paginate cars
        $cars = $this->carRepository->searchCars($categoryFilter, $nameSearch);
        $pagination = $this->paginator->paginate(
            target: $cars,
            page: $request->query->getInt('page', 1),
            limit: 20
        );

        return [$form, $pagination];
    }

    /**
     * Handles car creation and editing.
     */
    public function handleCarForm(Request $request, Car $car = null): mixed
    {
        $isNewCar = false;

        if (!$car) {
            $isNewCar = true;
            $car = new Car();
        }

        // Create and handle the car form
        $form = $this->formFactory->create(CarType::class, $car);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Save the car
            if ($isNewCar) {
                $this->entityManager->persist($car);
            }
            $this->entityManager->flush();

            return true;
        }

        return [$car, $form];
    }

    /**
     * Fetches all cars.
     */
    public function getAllCars(): array
    {
        // Get all cars from the repository
        return $this->carRepository->findAll();
    }

    /**
     * Handles car deletion.
     */
    public function deleteCar(Request $request, Car $car): bool
    {
        // Validate the CSRF token
        $token = new CsrfToken('delete'.$car->getId(), $request->request->get('_token'));

        if ($this->csrfTokenManager->isTokenValid($token)) {
            // Remove the car and flush changes
            $this->entityManager->remove($car);
            $this->entityManager->flush();

            return true;
        }

        return false;
    }
}
