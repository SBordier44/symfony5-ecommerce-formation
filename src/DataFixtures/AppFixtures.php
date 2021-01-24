<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Product;
use App\Entity\Purchase;
use App\Entity\PurchaseItem;
use App\Entity\User;
use Bezhanov\Faker\Provider\Commerce;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Liior\Faker\Prices;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
use WW\Faker\Provider\Picture;

class AppFixtures extends Fixture
{
    /**
     * @var SluggerInterface
     */
    private SluggerInterface $slugger;
    /**
     * @var UserPasswordEncoderInterface
     */
    private UserPasswordEncoderInterface $passwordEncoder;

    public function __construct(SluggerInterface $slugger, UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->slugger = $slugger;
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr_FR');
        $faker->addProvider(new Prices($faker));
        $faker->addProvider(new Commerce($faker));
        $faker->addProvider(new Picture($faker));

        $admin = new User();
        $admin->setEmail('admin@gmail.com')
            ->setFullName('Admin')
            ->setRoles(['ROLE_ADMIN'])
            ->setPassword($this->passwordEncoder->encodePassword($admin, 'password'));

        $manager->persist($admin);

        $users = [];
        for ($u = 0; $u < 5; $u++) {
            $user = new User();
            $user->setEmail("user$u@mail.com")
                ->setFullName($faker->name())
                ->setPassword($this->passwordEncoder->encodePassword($user, 'password'));

            $manager->persist($user);
            $users[] = $user;
        }

        $products = [];
        for ($c = 0; $c < 3; $c++) {
            $category = new Category();
            $category->setName($faker->department());

            $manager->persist($category);

            for ($p = 0; $p < random_int(10, 50); $p++) {
                $product = new Product();
                $product->setName($faker->productName())
                    ->setShortDescription($faker->paragraph)
                    ->setMainPicture($faker->pictureUrl(400, 400) . "?random=" . random_int(1, 100000))
                    ->setPrice($faker->price(4000, 20000))
                    ->setCategory($category);

                $manager->persist($product);
                $products[] = $product;
            }
        }

        for ($p = 0; $p < random_int(20, 40); $p++) {
            $purchase = (new Purchase())
                ->setFullName($faker->name)
                ->setAddress($faker->streetAddress)
                ->setPostalCode($faker->postcode)
                ->setCity($faker->city)
                ->setOwner($faker->randomElement($users))
                ->setPurchasedAt($faker->dateTimeThisYear);

            $selectedProducts = $faker->randomElements($products, random_int(3, 5));

            foreach ($selectedProducts as $product) {
                $purchaseItem = new PurchaseItem();
                $purchaseItem->setProduct($product)
                    ->setProductName($product->getName())
                    ->setQuantity(random_int(1, 3))
                    ->setProductPrice($product->getPrice())
                    ->setTotal($purchaseItem->getProduct()->getPrice() * $purchaseItem->getQuantity())
                    ->setPurchase($purchase);

                $manager->persist($purchaseItem);
            }

            if ($faker->boolean(90)) {
                $purchase->setStatus(Purchase::STATUS_PAID);
            }

            $manager->persist($purchase);
        }

        $manager->flush();
    }
}
