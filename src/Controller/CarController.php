<?php

namespace App\Controller;

use App\Entity\Car;
use App\Form\CarType;
use App\Form\CarFilterType;
use App\Repository\CarRepository;
use App\Repository\CarCategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/car')]
class CarController extends AbstractController
{
    #[Route('/', name: 'app_car_index', methods: ['GET', 'POST'])]
    public function indexClient(Request $request, EntityManagerInterface $entityManager, PaginatorInterface $paginator, CarCategoryRepository $carCategoryRepository): Response
    {
        $form = $this->createForm(CarFilterType::class);
        $form->handleRequest($request);

        $categoryFilter = null;
        $nameSearch = null;

        if ($form->isSubmitted() && $form->isValid()) {
            $categoryFilter = $form->get('category')->getData();
            $nameSearch = $form->get('name')->getData();
        }

        $carRepository = $entityManager->getRepository(Car::class);
        $cars = $carRepository->searchCars($categoryFilter, $nameSearch);

        $pagination = $paginator->paginate(
            $cars,
            $request->query->getInt('page', 1),
            20
        );

        return $this->render('car/index.html.twig', [
            'pagination' => $pagination,
            'carCategories' => $carCategoryRepository->findAll(),
            'form' => $form->createView(),
                        // 'temperature' => $temperature,
        ]);
    }

    #[Route('/all', name: 'app_car_index', methods: ['GET'])]
    public function index(CarRepository $carRepository): Response
    {
        return $this->render('car/showall.html.twig', [
            'cars' => $carRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_car_new', methods: ['GET', 'POST'])]
    public function new(Request $request, CarRepository $carRepository): Response
    {
        $car = new Car();
        $form = $this->createForm(CarType::class, $car);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $carRepository->save($car, true);

            return $this->redirectToRoute('app_car_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('car/new.html.twig', [
            'car' => $car,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_car_show', methods: ['GET'])]
    public function show(Car $car): Response
    {
        return $this->render('car/show.html.twig', [
            'car' => $car,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_car_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Car $car, CarRepository $carRepository): Response
    {
        $form = $this->createForm(CarType::class, $car);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $carRepository->save($car, true);

            return $this->redirectToRoute('app_car_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('car/edit.html.twig', [
            'car' => $car,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_car_delete', methods: ['POST'])]
    public function delete(Request $request, Car $car, CarRepository $carRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$car->getId(), $request->request->get('_token'))) {
            $carRepository->remove($car, true);
        }

        return $this->redirectToRoute('app_car_index', [], Response::HTTP_SEE_OTHER);
    }
}
