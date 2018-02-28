<?php

namespace App\Controller;

use App\Contributors\Service\Repository as ContributorsRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class HomeController extends Controller
{
    /**
     * @Route("/", name="homepage")
     * @param ContributorsRepository $contributorsRepository
     * @return Response
     */
    public function index(ContributorsRepository $contributorsRepository): Response
    {
        $contributors = $contributorsRepository->all();

        return $this->render(
            'home/index.html.twig', [
                'contributors' => $contributors
            ]
        );
    }
}
