<?php

// src/Command/PopulateDatabaseCommand.php

namespace App\Command;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Faker\Factory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PopulateDatabaseCommand extends Command
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
    }

    protected function configure()
    {
        // Set the name and description for the command
        $this->setName('app:populate-products')
            ->setDescription('Populate the database with dummy product data');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $faker = Factory::create(); // Create an instance of the Faker library
        $numberOfProducts = 100; // Number of products to create

        // Create and persist dummy products
        for ($i = 0; $i < $numberOfProducts; $i++) {
            $product = new Product();
            $product->setName($faker->word) // Generate a random product name
                    ->setCategory($faker->word) // Generate a random category
                    ->setPrice($faker->randomFloat(2, 5, 1000)) // Generate a random price
                    ->setCreatedAt($faker->dateTimeThisYear); // Generate a random creation date within the last year
            
            // Persist each product to the database
            $this->entityManager->persist($product);
        }

        // Flush to save all the data in one transaction
        $this->entityManager->flush();

        // Output a message indicating the number of products added
        $output->writeln("$numberOfProducts dummy products added successfully!");

        return Command::SUCCESS;
    }
}

