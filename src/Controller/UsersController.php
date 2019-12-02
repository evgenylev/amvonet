<?php

namespace App\Controller;

use Doctrine\Bundle\DoctrineBundle\DoctrineBundle,
    FOS\RestBundle\Controller\AbstractFOSRestController,
    FOS\RestBundle\Controller\Annotations as Rest,
    Symfony\Component\HttpFoundation\Response,
    Symfony\Component\HttpFoundation\Request,

    App\Entity\Users,
    App\Entity\Transactions;

/**
 * Class UsersController
 * @package App\Controller
 *
 * @Rest\Route("/rest", name="rest_")
 */
class UsersController extends AbstractFOSRestController
{
    /**
     * @Rest\Get("/users", name="user_list")
     *
     * @return Response
     */
    public function getUserList()
    {
        $repository = $this->getDoctrine()->getRepository(Users::class);
        $users = $repository->findBy(['isActive'=>1]);
        $view = $this->view($users, Response::HTTP_OK);
        return $this->handleView($view);
    }

    /**
     * @Rest\Get("/users/{id}", name="user_by_id", requirements={"id"="[-]?\d+"})
     *
     * @param int $id
     * @return Response
     */
    public function getUserById($id)
    {
        $repository = $this->getDoctrine()->getRepository(Users::class);
        if (!$user = $repository->find($id)) {
            $view = $this->view(['error'=>'User #'.$id.' not found'], Response::HTTP_NOT_FOUND);
        } else {
            $view = $this->view($user, Response::HTTP_OK);
        }
        return $this->handleView($view);
    }

    /**
     * @Rest\Get("/users/{id}/transactions", name="user_transactions", requirements={"id"="[-]?\d+"})
     *
     * @param int $id User ID
     * @return Response
     */
    public function getUserTransactions($id)
    {
        $repository = $this->getDoctrine()->getRepository(Transactions::class);
        if (!$transactions = $repository->findBy(['debetUser'=>$id])) {
            $view = $this->view(['No transactions for user #'.$id], Response::HTTP_NOT_FOUND);
        } else {
            $view = $this->view($transactions, Response::HTTP_OK);
        }
        return $this->handleView($view);
    }

    /**
     * @param int $debetUserId User Id from
     * @param int $creditUserId User Id to
     * @param float $summ Money amount to transfer
     * @return bool true if success
     *
     * throws Exception
     */
    private function _makeTransaction($debetUserId, $creditUserId, $summ)
    {
        $conn = $this->getDoctrine()->getConnection();
        $stmt = $conn->prepare("CALL makeTransaction($debetUserId, $creditUserId, $summ)");
        return $stmt->execute();
    }

    /**
     * @Rest\Post("/users/{id}/transactions", name="make_transaction", requirements={"id"="[-]?\d+"})
     *
     * @param int $id User ID debet (from)
     * @param Request $request The request itself
     * @return Response
     */
    public function makeTransaction($id, Request $request)
    {
        if (empty($request->get('to')) || empty($request->get('summ'))) {
            $view = $this->view(['error'=>'Invalid parameters'], Response::HTTP_OK);
        } else {
            try {
                $this->_makeTransaction($id, $request->get('to'), $request->get('summ'));
                $view = $this->view(['state'=>'ok'], Response::HTTP_CREATED);
            } catch (\Exception $e) {
                $view = $this->view(['error'=>$e->getMessage()], Response::HTTP_OK);
            }
        }

        return $this->handleView($view);
    }
}
