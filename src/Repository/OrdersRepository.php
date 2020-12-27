<?php

namespace App\Repository;

use App\Entity\Orders;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Orders|null find($id, $lockMode = null, $lockVersion = null)
 * @method Orders|null findOneBy(array $criteria, array $orderBy = null)
 * @method Orders[]    findAll()
 * @method Orders[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrdersRepository extends ServiceEntityRepository
{
    private $em;


    public function __construct(ManagerRegistry $registry, EntityManagerInterface $em)
    {
        parent::__construct($registry, Orders::class);
        $this->em = $em;
    }

   //TODO - Order status
    /**
     * Create new order, with customer id, date, total value and status as 0.
     *
     * @param  orders customer id and total
     */
    public function create_order($customer_id, $total)
    {
        $order = new Orders();
        $order->setCustomer($customer_id);
        $order->setTotal($total);
        $order->setCreatedAt(new \DateTime());
        $order->setStatus(0);
        $this->em->persist($order);
        $this->em->flush();
    }


    /**
     * Get orders by customer id
     *
     * @param int order id
     * @return Orders[] Returns an array of Order objects
     */
    public function get_orders($customer_id ) 
    {
        return $this->createQueryBuilder('ord')
            ->andWhere('ord.customer_id = :customer_id')
            ->getQuery()
            ->getResult() ;
    }


    // /**
    //  * @return Orders[] Returns an array of Orders objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('o.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Orders
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
