<?php

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


class BasketController extends BaseController
{
	/**
	 * @SWG\Tag(name="Basket")
     * @SWG\Response(
     *     response=200,
     *     description="Add new Basket",
     *     @SWG\Schema(
     *         type="array",
     *         @Model(type=Basket::class, groups={"basket", "Default"})
     *     )
     * )
     * 
     * @Rest\View(statusCode=Response::HTTP_CREATED)
     * @Rest\Post("/basket")
     */
    public function postBasket(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        //Add the basket
        $basket = new Basket();
        $formBasket = $this->createForm(BasketType::class, $basket);
        
        if(!empty($request->request->get('productPurchases'))){
        	$em->persist($basket);
			$em->flush();

			return $basket;
	  	} else {
            return $formBasket;
        }
    }

    /**
     * @SWG\Tag(name="Basket")
     * @SWG\Response(
     *     response=200,
     *     description="Add new new Basket with the productPurchases, Example for returning productPurchases {'product':1, 'quantity':3}",
     *     @SWG\Schema(
     *         type="array",
     *         @Model(type=Basket::class, groups={"basket", "Default"})
     *     )
     * )
     * @Rest\View(statusCode=Response::HTTP_CREATED)
     * @Rest\Post("/allbasket")
     */
    public function postAllBasket(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        //add the basket
        $basket = $this->postBasket($request);

        $idBasket = $basket->getId();
        
        $productPArray = $request->request->get('productPurchases');
        
        $productsPurchasesTab = [];

        for($i=0 ; $i<count($productPArray); $i++){
        	$productP = json_decode($productPArray[$i]);
        	$productP->basket = $idBasket;
        	
        	$request->request->replace((array)$productP);
            
        	
        	$productPurchase = new ProductPurchase();
            $form = $this->createForm(ProductPurchaseType::class, $productPurchase);
            $form->submit($request->request->all());
            if ($form->isValid()) {
                $em->persist($productPurchase);
                $em->flush();
            }

            array_push($productsPurchasesTab, $productPurchase);
            
		}
       
        $result = [];
        $result["basket"] = $basket;
        $result["productsPurchases"] = $productsPurchasesTab;
        return  $result;
	}

	/**
	 * @SWG\Tag(name="Basket")
     * @SWG\Response(
     *     response=200,
     *     description="Display Basket",
     *     @SWG\Schema(
     *         type="array",
     *         @Model(type=Basket::class, groups={"Default", "basket"})
     *     )
     * )
     * @Rest\View(statusCode=Response::HTTP_OK)
     * @Rest\Get("/basket/{id}")
     */
    public function getBasket(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $em = $this->getDoctrine()->getManager();
        $basket = $em->getRepository(Basket::class)
            ->find($request->get('id'));

        if (empty($basket)) {
            return $this->sendMessage('basket not found');
        }
        
        return $basket;
        
	}

	/**
     * @SWG\Tag(name="Basket")
     * @SWG\Response(
     *     response=200,
     *     description="Get all products puchases with filter",
     *     @SWG\Schema(
     *         type="array",
     *         @Model(type=ProductPurchase::class, groups={"productPurchase", "Default"})
     *     )
     * )
     * @QueryParam(name="basket", default="", description="")
     * @QueryParam(name="product", default="", description="")
     * @Rest\View(statusCode=Response::HTTP_OK, serializerGroups={"productPurchase", "Default"})
     * @Rest\Get("/productpurchase/all")
     */
    public function getAllProductPurchasesAction(Request $request, ParamFetcher $paramFetcher)
    {
        $em = $this->getDoctrine()->getManager();
        $basket  = $paramFetcher->get('basket');
        $product  = $paramFetcher->get('product');
        $where = false;

        $queryBuilder = $em->getRepository(Product::class)->createQueryBuilder('a');

            
        if (!empty($basket)){
            if (!$where) {
                $queryBuilder->where('a.basket LIKE :basket');
            } else {
                $queryBuilder->andWhere('a.basket LIKE :basket');
            }
            $params['basket'] = '%'.$basket.'%';
            $where = true;
        }
        if (!empty($product)){
            if (!$where) {
                $queryBuilder->where('a.product LIKE :product');
            } else {
                $queryBuilder->andWhere('a.product LIKE :product');
            }
            $params['product'] = '%'.$product.'%';
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

	/**
	 * @SWG\Tag(name="Basket")
     * @SWG\Response(
     *     response=200,
     *     description="Update a Basket",
     *     @SWG\Schema(
     *         type="array",
     *         @Model(type=Basket::class, groups={"basket", "Default"})
     *     )
     * )
     * @Rest\View(statusCode=Response::HTTP_OK)
     * @Rest\Put("/basket/{id}")
     */
    public function putbasketsAction(Request $request)
    {   
        $em = $this->getDoctrine()->getManager();
        
        $basket = $em->getRepository(Basket::class)
            ->find($request->get('id'));

        
        if (empty($basket)) {
            return $this->sendMessage('basket not found');
        }
        
        $productPurchase = $this->putProductPurchaseAction($request);
        
        
        
        $form = $this->createForm(BasketType::class, $basket);
        $form->submit($request->request->all());

        if ($form->isValid()) {
            $em->flush();
            return $basket;
        } else {
            return $form;
        }
    }

    /**
     * @SWG\Tag(name="Basket")
     * 
     * @SWG\Response(
     *     response=200,
     *     description="Delete a basket"
     * )
     * @Rest\View(statusCode=Response::HTTP_NO_CONTENT)
     * @Rest\Delete("basket/{id}")
     */
    public function removeBasketAction(Request $request)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $basket = $em->getRepository(Bakset::class)
            ->find($request->get('id'));

        $this->removeProductPurchaseAction($request);

        if ($basket) {
            $em->remove($basket);
            $em->flush();
        }
    }


	


	
    /***********************/
    /*ProductPurchase*/
    /***********************/


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
       
		
		// $basket = $request->request->get('productPurchases')->basket;
        $basket = $request->request->get('basket');
        
		if (empty($basket)) {
            return $this->sendMessage('basket not found');
        }

        // $product = $request->request->get('productPurchases')->product;
        
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
     * @Rest\Put("productPurchase/{id_basket}/{id}")
     */
    public function putProductPurchaseAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $productPurchase = $em->getRepository(ProductPurchase::class)
            ->find($request->get('id'));

        $basket = $em->getRepository(ProductPurchase::class)
            ->findBy(array('basket' => $request->get('id_basket')));
		if (empty($basket)) {
            return $this->sendMessage('basket not found');
        }

        $product = $request->request->get('product');
        if (empty($product)) {
            return $this->sendMessage('product not found');
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
     * @Rest\Delete("productPurchase/{id_basket}")
     */
    public function removeProductPurchaseAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $productPurchase = $em->getRepository(ProductPurchase::class)
            ->findBy(array('basket' => $request->get('id_basket')));
        

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