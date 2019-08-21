<?php

namespace App\Controller;

use App\Entity\Basket;
use App\Form\BasketType;
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
        
        $productPArray = $request->request->get('productPurchases');
        
        if (empty($basket)) {
            return $this->sendMessage('basket not found');
        }
        $productsPurchasesTab = [];
        for($i=0 ; $i<count($productPArray); $i++){
            $productP = json_decode($productPArray[$i]);
            $productP->basket = $basket->getId();
            $request->request->replace((array)$productP);
            
            
            $productPurchase = new ProductPurchase();
            $form = $this->createForm(ProductPurchaseType::class, $productPurchase);

            $form->submit($request->request->all());
            
            if ($form->isValid() == true) {
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
        $basket = $em->getRepository(Basket::class)
            ->find($request->get('id'));

        if ($basket) {
            $em->remove($basket);
            $em->flush();
        }
    }

}