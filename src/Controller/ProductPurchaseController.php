<?

namespace App\Controller;

use App\Entity\Basket;
use App\Form\BasketType;
use App\Entity\Product;
use App\Form\ProductType;
use App\Entity\ProductPurchase;
use App\Form\ProductPurchaseType;
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


class ProductPurchaseController extends BaseController
{
    
    /**
     * 
     * @SWG\Tag(name="ProductPurchase")
     * @SWG\Response(
     *     response=200,
     *     description="Add new ProductPurchase",
     *     @SWG\Schema(
     *         type="array",
     *         @Model(type=ProductPurchase::class, groups={"productPurchase", "Default"})
     *     )
     * )
     * @Rest\View(statusCode=Response::HTTP_CREATED)
     * @Rest\Post("/productPurchase")
     */
    public function postProductPurchase(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
       
        
        $basket = $request->request->get('basket');
        
        if (empty($basket)) {
            return $this->sendMessage('basket not found');
        }

        $product = $request->request->get('product');
        
        if (empty($product)) {
            return $this->sendMessage('product not found');
        }
        
        $productPurchase = new ProductPurchase();
        $form = $this->createForm(ProductPurchaseType::class, $productPurchase);
        
        $form->submit($request->request->all());

        if ( $form->isValid()) {

            $em->persist($productPurchase);
            $em->flush();
           
            return $productPurchase;
        } else {
            return $form;
        }
    }

    /**
     * 
     * @SWG\Tag(name="ProductPurchase")
     * @SWG\Response(
     *     response=200,
     *     description="Update a productPurchase",
     *     @SWG\Schema(
     *         type="array",
     *         @Model(type=ProductPurchase::class, groups={"productPurchase", "Default"})
     *     )
     * )
     * @Rest\View(statusCode=Response::HTTP_OK)
     * @Rest\Put("productPurchase/{id}")
     */
    public function putProductPurchaseAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $productPurchase = $em->getRepository(ProductPurchase::class)
            ->find($request->get('id'));


        $product = $request->request->get('product');
        if (empty($product)) {
            return $this->sendMessage('product not found');
        }
        
        $basket = $em->getRepository(Basket::class)
            ->find($request->get('basket'));
        
        if(empty($basket)) {
            return $this->sendMessage('basket not found');
        }
        $form = $this->createForm(productPurchaseType::class, $productPurchase);
        $form->submit($request->request->all());

        if ($form->isValid()) {
            $em->flush();
            return $productPurchase;
        } else {
            return $form;
        }
    }

    /**
     * 
     * @SWG\Tag(name="ProductPurchase")
     * 
     * @SWG\Response(
     *     response=200,
     *     description="Delete a ProductPurchase"
     * )
     * @Rest\View(statusCode=Response::HTTP_NO_CONTENT)
     * @Rest\Delete("productPurchase/{id}")
     */
    public function removeProductPurchaseAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $productPurchase = $em->getRepository(ProductPurchase::class)
            ->find($request->get('id'));
        

        if ($productPurchase) {
            $em->remove($productPurchase);
            $em->flush();
        }
    }

    /**
     * @SWG\Tag(name="ProductPurchase")
     * @SWG\Response(
     *     response=200,
     *     description="Display ProductPurchase detail",
     *     @SWG\Schema(
     *         type="array",
     *         @Model(type=ProductPurchase::class, groups={"Default", "productPurchase"})
     *     )
     * )
     * @Rest\View(statusCode=Response::HTTP_OK)
     * @Rest\Get("/productPurchase/{id}")
     */
    public function getProductPurchaseAction(Request $request, ParamFetcher $paramFetcher)
    {   
        $em = $this->getDoctrine()->getManager();
        $productPurchase = $em->getRepository(ProductPurchase::class)
            ->find($request->get('id'));

        if (empty($productPurchase)) {
            return $this->sendMessage('productPurchase notfound');
        }

        return $productPurchase;
    }


    
}