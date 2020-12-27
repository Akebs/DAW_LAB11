<?php

namespace App\Repository;

use App\Entity\Products;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Products|null find($id, $lockMode = null, $lockVersion = null)
 * @method Products|null findOneBy(array $criteria, array $orderBy = null)
 * @method Products[]    findAll()
 * @method Products[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductsRepository extends ServiceEntityRepository
{
    private $em;

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $em)
    {
        parent::__construct($registry, Products::class);
        $this->em = $em;
    }


    /**
     * Get all products, with category id if specified
     *
     * @param optional products category id
     * @return Products[] Returns an array of Products objects
     */
    public function get_products($id = FALSE)
    {   
        if ($id){
            return $this->createQueryBuilder('prod')
                ->andWhere('prod.cat = :id') 
                ->setParameter('id', $id)               
                ->orderBy('prod.name', 'ASC')
                ->getQuery()
                ->getResult() ;
        }
        else {
            return $this->createQueryBuilder('prod')
                ->orderBy('prod.name', 'ASC')
                ->getQuery()
                ->getResult() ;
        }
    }


    /**
     * Get product by product id
     *
     * @param int product id
     * @return Returns a Product object
     */
    public function get_product($product_id ) 
    {
        return $this->createQueryBuilder('prod')
            ->andWhere('prod.id = :product_id')
            ->getQuery()
            ->getResult() ;
    }




    // /**
    //  * @return Products[] Returns an array of Products objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Products
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
