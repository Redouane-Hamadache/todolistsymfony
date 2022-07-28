<?php

namespace App\Controller;

use App\Entity\Task;
use App\Form\TaskType;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TaskController extends AbstractController
{
    #[Route('/task', name: 'app_task')]
    public function index(TaskRepository $taskRepository, PaginatorInterface $paginator, Request $request): Response
    {        
        $tasks = $taskRepository->findAll();

        $tasksPagination = $paginator->paginate(
            $tasks, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            5 /*limit per page*/
        );

        return $this->render('task/index.html.twig', [
            'controller_name' => 'TaskController', 'tasks' => $tasksPagination, 
        ]);
    }

    #[Route("/task/add", name: 'app_addTask' , methods:['GET', 'POST'])]
    public function add(Request $request, EntityManagerInterface $entityManagerInterface) : Response
    {
        $user= $this->getUser();
        $task = new Task($user);
        $form = $this->createForm(TaskType::class, $task);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $task = $form->getData();
            
            $entityManagerInterface->persist($task);
            $entityManagerInterface->flush();

            return new RedirectResponse('/task');
        }

        return $this->render('task/add.html.twig',[
            'form' => $form->createView(),
        ]);
    }

    #[Route('/task/edit/{id}', name : "app_editTask", methods : ['GET', 'POST'])]
    public function edit(int $id , TaskRepository $taskRepository, EntityManagerInterface $entityManagerInterface, Request $request) : Response
    {
        $task = $taskRepository->findOneBy(["id" => $id]);
        $form = $this->createForm(TaskType::class, $task);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $task = $form->getData();
            
            $entityManagerInterface->persist($task);
            $entityManagerInterface->flush();

            $this->addFlash(
                'sucess',
                'Votre tâche a été modifié !'
        );

            return new RedirectResponse('/task');
        }

        return $this->render('task/edit.html.twig',[
            'form' => $form->createView(),
        ]);
    }

    #[Route('/task/delete/{id}', name : "app_deleteTask", methods : ['GET'])]
    public function delete(int $id , EntityManagerInterface $entityManagerInterface , TaskRepository $taskRepository): Response
    {       
        $task = $taskRepository->findOneBy(["id" => $id]);

        // if (!$task) {
        //     $this->addFlash(
        //         'danger',
        //         "La tâche en question n'a pas été trouvé"
        // );

        // return new RedirectResponse('/task');
        // }
    
        $entityManagerInterface->remove($task);
        $entityManagerInterface->flush();


        $this->addFlash(
                'sucess',
                'Votre tâche a été supprimé !'
        );

        return new RedirectResponse('/task');
    }
}
