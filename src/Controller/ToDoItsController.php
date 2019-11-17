<?php


namespace App\Controller;

use App\Entity\Project;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Serializer;

/**
 * @Route("/todo")
 */
class ToDoItsController extends AbstractController
{
    /**
     * @Route("/{page}", name="projects_list", defaults={"page":5}, requirements={"page"="\d+"})
     */
    public function projects( $page=1, Request $request)
    {
        $limit = $request->get('limit',10);
        $repository = $this->getDoctrine()->getRepository(Project::class);
        $items = $repository->findAll();
        return $this->json(
            [
                'page'=>$page,
                'limit'=>$limit,
                'data'=>array_map(function(Project $item){
                    return $this->generateUrl('projekt_by_slug', ['slug'=> $item->getSlug()]);
                }, $items)
            ]
        );
    }

    /**
     * @Route("/project/{id}", name="project_by_id", requirements={"id"="\d+"}, methods={"GET"})
     * @ParamConverter("project", class="App:Project")
     */
    public function projectById($project)
    {
      return $this->json($project);
    }

    /**
    * @Route("/project/{slug}", name="projekt_by_slug", methods={"GET"})
    * @ParamConverter("project", class="App:Project", options={"mapping":{"slug":"slug"}})
     */
    public function projectByTitle($project)
    {
        return $this->json($project);
    }

    /**
     * @Route("/add", name="project_add", methods={"POST"})
     */
    public function add(Request $request)
    {
        /**
         * @var Serializer $serializer
         */
        $serializer = $this->get('serializer');
        $project = $serializer->deserialize($request->getContent(), Project::class, 'json');
        $em = $this->getDoctrine()->getManager();
        $em->persist($project);
        $em->flush();

        return $this->json($project);

    }

    /**
     * @Route("/project/{id}", name="project_delete", methods={"DELETE"})
     */
    public function delete(Project $project)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($project);
        $em->flush();
        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }



}
