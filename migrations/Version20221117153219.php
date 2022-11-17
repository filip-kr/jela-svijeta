<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221117153219 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE dish_ingredient DROP FOREIGN KEY FK_77196056933FE08C');
        $this->addSql('ALTER TABLE dish_ingredient DROP FOREIGN KEY FK_77196056148EB0CB');
        $this->addSql('DROP INDEX IDX_77196056933FE08C ON dish_ingredient');
        $this->addSql('DROP INDEX IDX_77196056148EB0CB ON dish_ingredient');
        $this->addSql('ALTER TABLE dish_ingredient ADD id INT AUTO_INCREMENT NOT NULL, DROP PRIMARY KEY, ADD PRIMARY KEY (id)');
        $this->addSql('ALTER TABLE dish_tag DROP FOREIGN KEY FK_64FF4A98148EB0CB');
        $this->addSql('ALTER TABLE dish_tag DROP FOREIGN KEY FK_64FF4A98BAD26311');
        $this->addSql('DROP INDEX IDX_64FF4A98148EB0CB ON dish_tag');
        $this->addSql('DROP INDEX IDX_64FF4A98BAD26311 ON dish_tag');
        $this->addSql('ALTER TABLE dish_tag ADD id INT AUTO_INCREMENT NOT NULL, DROP PRIMARY KEY, ADD PRIMARY KEY (id)');
        $this->addSql('ALTER TABLE dish_tag ADD FOREIGN KEY (dish_id) REFERENCES dish(id)');
        $this->addSql('ALTER TABLE dish_tag ADD FOREIGN KEY (tag_id) REFERENCES tag(id)');
        $this->addSql('ALTER TABLE dish_ingredient ADD FOREIGN KEY (dish_id) REFERENCES dish(id)');
        $this->addSql('ALTER TABLE dish_ingredient ADD FOREIGN KEY (ingredient_id) REFERENCES ingredient(id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE dish_ingredient MODIFY id INT NOT NULL');
        $this->addSql('DROP INDEX `PRIMARY` ON dish_ingredient');
        $this->addSql('ALTER TABLE dish_ingredient DROP id');
        $this->addSql('ALTER TABLE dish_ingredient ADD CONSTRAINT FK_77196056933FE08C FOREIGN KEY (ingredient_id) REFERENCES ingredient (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE dish_ingredient ADD CONSTRAINT FK_77196056148EB0CB FOREIGN KEY (dish_id) REFERENCES dish (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_77196056933FE08C ON dish_ingredient (ingredient_id)');
        $this->addSql('CREATE INDEX IDX_77196056148EB0CB ON dish_ingredient (dish_id)');
        $this->addSql('ALTER TABLE dish_ingredient ADD PRIMARY KEY (dish_id, ingredient_id)');
        $this->addSql('ALTER TABLE dish_tag MODIFY id INT NOT NULL');
        $this->addSql('DROP INDEX `PRIMARY` ON dish_tag');
        $this->addSql('ALTER TABLE dish_tag DROP id');
        $this->addSql('ALTER TABLE dish_tag ADD CONSTRAINT FK_64FF4A98148EB0CB FOREIGN KEY (dish_id) REFERENCES dish (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE dish_tag ADD CONSTRAINT FK_64FF4A98BAD26311 FOREIGN KEY (tag_id) REFERENCES tag (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_64FF4A98148EB0CB ON dish_tag (dish_id)');
        $this->addSql('CREATE INDEX IDX_64FF4A98BAD26311 ON dish_tag (tag_id)');
        $this->addSql('ALTER TABLE dish_tag ADD PRIMARY KEY (dish_id, tag_id)');
    }
}
