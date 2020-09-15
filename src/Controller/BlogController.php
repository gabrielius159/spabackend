<?php declare(strict_types=1);

namespace App\Controller;

use App\Entity\BlogPost;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Serializer;

/**
 * @Route("/blog")
 */
class BlogController extends AbstractController
{
    /**
     * @Route("/{page}", name="blog_list", defaults={"page"=5}, requirements={"page"="\d+"})
     *
     * @param Request $request
     * @param int     $page
     *
     * @return JsonResponse
     */
    public function listAction(Request $request, int $page = 1): JsonResponse
    {
        $limit      = $request->get('limit', 10);
        $repository = $this->getDoctrine()->getManager()->getRepository(BlogPost::class);
        $items      = $repository->findAll();

        return new JsonResponse([
                'page' => $page,
                'limit' => $limit,
                'data' => array_map(function (BlogPost $item) {
                    return $this->generateUrl('blog_by_slug', ['slug' => $item->getSlug()]);
                }, $items)
            ]
        );
    }

    /**
     * @Route("/post/{id}", name="blog_by_id", requirements={"id"="\d+"}, methods={"GET"})
     *
     * @param BlogPost $blogPost
     *
     * @return JsonResponse
     */
    public function postAction(BlogPost $blogPost): JsonResponse
    {
        return $this->json($blogPost);
    }

    /**
     * @Route("/post/{slug}", name="blog_by_slug", methods={"GET"})
     * @ParamConverter("blogPost", class="App:BlogPost", options={"mapping": {"slug": "slug"}})
     *
     * @param BlogPost $blogPost
     *
     * @return JsonResponse
     */
    public function postBySlug($blogPost): JsonResponse
    {
        return $this->json($blogPost);
    }

    /**
     * @Route("/add", name="blog_add", methods={"POST"})
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function addAction(Request $request): JsonResponse
    {
        /*** @var Serializer $serializer */
        $serializer = $this->get('serializer');

        $blogPost = $serializer->deserialize($request->getContent(), BlogPost::class, 'json');

        $em = $this->getDoctrine()->getManager();

        $em->persist($blogPost);
        $em->flush();

        return $this->json($blogPost);
    }

    /**
     * @Route("/post/{id}", name="blog_delete", methods={"DELETE"})
     *
     * @param BlogPost $blogPost
     *
     * @return JsonResponse
     */
    public function deleteAction(BlogPost $blogPost): JsonResponse
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($blogPost);
        $em->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}