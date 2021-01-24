<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210122224113 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE purchase_product');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        //$this->addSql('CREATE SCHEMA public');
        $this->addSql('CREATE TABLE purchase_product (purchase_id INT NOT NULL, product_id INT NOT NULL, PRIMARY KEY(purchase_id, product_id))');
        $this->addSql('CREATE INDEX idx_c890ced44584665a ON purchase_product (product_id)');
        $this->addSql('CREATE INDEX idx_c890ced4558fbeb9 ON purchase_product (purchase_id)');
        $this->addSql('ALTER TABLE purchase_product ADD CONSTRAINT fk_c890ced4558fbeb9 FOREIGN KEY (purchase_id) REFERENCES purchase (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE purchase_product ADD CONSTRAINT fk_c890ced44584665a FOREIGN KEY (product_id) REFERENCES product (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }
}
