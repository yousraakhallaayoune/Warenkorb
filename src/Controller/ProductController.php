<?
namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
// use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;

use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Request\ParamFetcher;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use JMS\Serializer\SerializationContext;
use Nelmio\ApiDocBundle\Annotation\Model;
use App\Controller\ProductPurchaseController;
use Swagger\Annotations as SWG;


class ProductController extends BaseController
{

	/**
     * 
     * @SWG\Tag(name="Product")
     * @SWG\Response(
     *     response=200,
     *     description="Add new product",
     *     @SWG\Schema(
     *         type="array",
     *         @Model(type=Product::class, groups={"product", "Default"})
     *     )
     * )
     * @Rest\View(statusCode=Response::HTTP_CREATED)
     * @Rest\Post("/product")
     */
    public function postProduct(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        
		$form->submit($request->request->all());

        if ($form->isValid()) {

            $em->persist($product);
			$em->flush();
            
            return $product;
		} else {
            return $form;
        }
    }

    /**
     * @SWG\Tag(name="Product")
     * @SWG\Response(
     *     response=200,
     *     description="Update a product",
     *     @SWG\Schema(
     *         type="array",
     *         @Model(type=Product::class, groups={"product", "Default"})
     *     )
     * )
     * @Rest\View(statusCode=Response::HTTP_OK)
     * @Rest\Put("product/{id}")
     */
    public function putProductAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $product = $em->getRepository(Product::class)
            ->find($request->get('id'));

        if (empty($product)) {
            return $this->sendMessage('product not found');
        }

        $form = $this->createForm(ProductType::class, $product);
        $form->submit($request->request->all());

        if ($form->isValid()) {
            $em->flush();
            return $product;
        } else {
            return $form;
        }
    }

    /**
     * @SWG\Tag(name="Product")
     * 
     * @SWG\Response(
     *     response=200,
     *     description="Delete a product"
     * )
     * @Rest\View(statusCode=Response::HTTP_NO_CONTENT)
     * @Rest\Delete("product/{id}")
     */
    public function removeProductAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $product = $em->getRepository(Product::class)
            ->find($request->get('id'));

        if ($product) {
            $em->remove($product);
            $em->flush();
        }
    }

    /**
     * 
     * @SWG\Tag(name="Product")
     * @SWG\Response(
     *     response=200,
     *     description="Display product detail",
     *     @SWG\Schema(
     *         type="array",
     *         @Model(type=Product::class, groups={"Default", "product"})
     *     )
     * )
     * @Rest\View(statusCode=Response::HTTP_OK, serializerGroups={"Default", "city"})
     * @Rest\Get("/product/{id}")
     */
    public function getProductAction(Request $request, ParamFetcher $paramFetcher)
    {   
        $em = $this->getDoctrine()->getManager();
        $product = $em->getRepository(Product::class)
            ->find($request->get('id'));

        if (empty($product)) {
            return $this->sendMessage('Product notfound');
        }

        return $product;
    }

    /**
     * @SWG\Tag(name="Product")
     * @SWG\Response(
     *     response=200,
     *     description="Get all products",
     *     @SWG\Schema(
     *         type="array",
     *         @Model(type=Product::class, groups={"product", "Default"})
     *     )
     * )
     * @QueryParam(name="name", default="", description="")
     * @Rest\View(statusCode=Response::HTTP_OK, serializerGroups={"city", "Default"})
     * @Rest\Get("/products/all")
     */
    public function getAllProductsAction(Request $request, ParamFetcher $paramFetcher)
    {
        $em = $this->getDoctrine()->getManager();
        $name  = $paramFetcher->get('name');
        $where = false;

        $queryBuilder = $em->getRepository(Product::class)->createQueryBuilder('a');

            
        if (!empty($name)){
            if (!$where) {
                $queryBuilder->where('a.name LIKE :name');
            } else {
                $queryBuilder->andWhere('a.name LIKE :name');
            }
            $params['name'] = '%'.$name.'%';
            $where = true;
        }

        if(!empty($params)){
            $queryBuilder->setParameters($params)
                ->orderBy('a.id');
            } else {
                $queryBuilder->orderBy('a.id');
            }
        
        
        return $queryBuilder->getQuery()->getResult();
    }


}