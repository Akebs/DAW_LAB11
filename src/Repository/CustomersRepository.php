<?php

namespace App\Repository;

use App\Entity\Customers;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Customers|null find($id, $lockMode = null, $lockVersion = null)
 * @method Customers|null findOneBy(array $criteria, array $orderBy = null)
 * @method Customers[]    findAll()
 * @method Customers[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CustomersRepository extends ServiceEntityRepository
{
    private $em;


    public function __construct(ManagerRegistry $registry, EntityManagerInterface $em)
    {
        parent::__construct($registry, Customers::class);
        $this->em = $em;
    }


    /**
     * Create new Customers, with name, email, password and creation date
     *
     * @param  Customers name, email and password
     */
    public function register_customer($name, $email, $password)
    {
        $customer = new Customers();
        $customer->setName($name);
        $customer->setEmail($email);
        $customer->setPasswordDigest($passwordDigest);
        $customer->setCreatedAt(new \DateTime());
        $this->em->persist($customer);
        $this->em->flush();
    }


    /**
     * Get Customer by email 
     *
     * @param email
     * @return  Returns a Customers object
     */
    public function get_customer($email ) 
    {
        return $this->createQueryBuilder('cust')
            ->andWhere('cust.email = :email')
            ->getQuery()
            ->getResult() ;        
    }

  //TODO  validate_customer($email, $password)
    // ->getOneOrNullResult()



    // /**
    //  * @return Customers[] Returns an array of Customers objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Customers
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
