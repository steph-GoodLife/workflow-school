<?php

namespace App\Controller;

use LogicException;
use App\Entity\Message;
use App\Form\MessageToProfType;
use App\Form\MessageRequestType;
use App\Repository\MessageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Workflow\WorkflowInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class StartController extends AbstractController
{
    private $messageRequestWorkflow;

    public function __construct(WorkflowInterface $messageRequestWorkflow)
    {
        $this->messageRequestWorkflow = $messageRequestWorkflow;
    }


    #[Route('/', name: 'app_start')]
    public function index(): Response
    {
        return $this->render('start/index.html.twig');
    }

    #[Route('/flow_start', name:'app_flow_start')]
    public function flow_start(EntityManagerInterface $entityManager, Request $request): Response
    {
        $message = new Message();

        $message->setUtilisateur($this->getUser());

        $form = $this->createform(MessageRequestType::class, $message);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid())
        {
            $message = $form->getData();

            try {
                $this->messageRequestWorkflow->apply($message, 'to_pending');
            } catch (LogicException $exception){

            }

            $entityManager->persist($message);
            $entityManager->flush();

            $this->addFlash('success', 'Message envoyé !');

            return $this->redirectToRoute('app_start');
        }

        return $this->render('start/flow_start.html.twig',[
            'form' => $form->createView()
        ]);
    }

    #[Route("/prof", name:"app_prof")]
    public function prof(MessageRepository $messageRepository)
    {
        return $this->render('start/prof.html.twig', [
            'message' => $messageRepository->findAll(),
        ]);
    }

    #[Route("/change/{id}/{to}", name:"app_change")]
    public function change(Message $messageRequest, String $to, EntityManagerInterface $entityManager): Response
    {
        try {
            $this->messageRequestWorkflow->apply($messageRequest, $to);
        } catch (LogicException $exception) {
            //
        }

        $entityManager->persist($messageRequest);
        $entityManager->flush();

        $this->addFlash('success', 'Action enregistrée !');

        return $this->redirectToRoute('app_prof');
    }
}
