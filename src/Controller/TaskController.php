<?php

namespace App\Controller;

use App\Entity\Task;
use App\Form\TaskType;
use App\Repository\TaskRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class TaskController extends AbstractController
{
    #[Route('/task', name: 'app_task')]
    public function index(TaskRepository $taskRepository, PaginatorInterface $paginator, Request $request): Response
    {        
        $tasks = $taskRepository->findAll();

        $tasksPagination = $paginator->paginate(
            $tasks, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            10 /*limit per page*/
        );

        return $this->render('task/index.html.twig', [
            'controller_name' => 'TaskController', 'tasks' => $tasksPagination, 
        ]);
    }

    #[Route("/task/add", name: 'app_addTask' , methods:['GET', 'POST'])]
    public function add() : Response
    {
        $task = new Task();
        $form = $this->createForm(TaskType::class, $task);


        return $this->render('task/add.html.twig',[
            'form' => $form->createView(),
        ]);
    }
}
