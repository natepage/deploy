<?php
declare(strict_types=1);

namespace App\Controller;

use App\Database\Entity\Deployment;
use App\Services\Deployment\Command;
use App\Services\Deployment\Configuration;
use App\Services\Deployment\Deploy;
use App\Services\Deployment\Error;
use App\Services\Deployment\Interfaces\ContextInterface;
use App\Services\Deployment\System;
use App\Services\Security\Interfaces\PermissionCheckerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class DeployController extends Controller
{
    /**
     * @var \App\Services\Deployment\Interfaces\ContextInterface
     */
    private $context;

    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var \App\Services\Security\Interfaces\PermissionCheckerInterface
     */
    private $permissionChecker;

    /**
     * DeployController constructor.
     *
     * @param \App\Services\Deployment\Interfaces\ContextInterface $context
     * @param \Doctrine\ORM\EntityManagerInterface $entityManager
     * @param \App\Services\Security\Interfaces\PermissionCheckerInterface $permissionChecker
     */
    public function __construct(
        ContextInterface $context,
        EntityManagerInterface $entityManager,
        PermissionCheckerInterface $permissionChecker
    ) {
        $this->context = $context;
        $this->entityManager = $entityManager;
        $this->permissionChecker = $permissionChecker;
    }

    /**
     * Deploy.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function deploy(): Response
    {
        if ($this->permissionChecker->isGranted() === false) {
            return $this->render('access_denied.html.twig');
        }

        $this->context->deploy();
        $this->saveDeployment($this->context);

        return $this->render('deploy.html.twig', ['context' => $this->context]);
    }

    /**
     * Index.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function index(): Response
    {
        return $this->render('index.html.twig', [
            'deployments' => \array_reverse($this->entityManager->getRepository(Deployment::class)->findAll())
        ]);
    }

    /**
     * @param int $deploymentId
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Exception
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function show(int $deploymentId): Response
    {
        $deployment = $this->entityManager->getRepository(Deployment::class)->find($deploymentId);

        if ($deployment === null) {
            return $this->render('deployment_not_found.html.twig');
        }

        return $this->render('deploy.html.twig', ['context' => $this->getContextFromDeployment($deployment)]);
    }

    /**
     * Get unserialized context from given deployment.
     *
     * @param \App\Database\Entity\Deployment $deployment
     *
     * @return \App\Services\Deployment\Interfaces\ContextInterface
     */
    private function getContextFromDeployment(Deployment $deployment): ContextInterface
    {
        return \unserialize($deployment->getContext(), [
            'allowed_classes' => [
                \DateTime::class,
                Command::class,
                Configuration::class,
                Deploy::class,
                Error::class,
                System::class
            ]
        ]);
    }

    /**
     * Save deployment in database.
     *
     * @param \App\Services\Deployment\Interfaces\ContextInterface $context
     *
     * @return void
     */
    private function saveDeployment(ContextInterface $context): void
    {
        $deployment = (new Deployment())
            ->setContext(\serialize($context))
            ->setDate($context->getDate())
            ->setDuration($context->getDuration())
            ->setStatus(empty($context->getErrors()));

        $this->entityManager->persist($deployment);
        $this->entityManager->flush();
    }
}
