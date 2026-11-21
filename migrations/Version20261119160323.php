<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20261119160323 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE adhesion_fonds (id INT AUTO_INCREMENT NOT NULL, montant_du DOUBLE PRECISION NOT NULL, montant_verse DOUBLE PRECISION NOT NULL, date_mise_ajour DATE NOT NULL, statut VARCHAR(100) NOT NULL, motif_derogation VARCHAR(255) DEFAULT NULL, user_id INT DEFAULT NULL, membre_id INT DEFAULT NULL, INDEX IDX_3C39BC82A76ED395 (user_id), INDEX IDX_3C39BC826A99F74A (membre_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE membre (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(100) NOT NULL, prenom VARCHAR(100) NOT NULL, telephone VARCHAR(100) NOT NULL, email VARCHAR(180) DEFAULT NULL, adresse VARCHAR(255) DEFAULT NULL, piece_type VARCHAR(50) NOT NULL, piece_numero VARCHAR(100) NOT NULL, date_entree DATE NOT NULL, date_saisie DATE NOT NULL, statut VARCHAR(50) NOT NULL, personne_prevenir_nom VARCHAR(150) DEFAULT NULL, personne_prevenir_lien VARCHAR(80) DEFAULT NULL, personne_prevenir_tel VARCHAR(100) DEFAULT NULL, utilisateur_id INT DEFAULT NULL, cree_part_id INT DEFAULT NULL, UNIQUE INDEX UNIQ_F6B4FB29FB88E14F (utilisateur_id), INDEX IDX_F6B4FB29FEE4ED82 (cree_part_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE plan_regularisation (id INT AUTO_INCREMENT NOT NULL, montant_du_fige DOUBLE PRECISION NOT NULL, date_adhesion DATE NOT NULL, delai_mois VARCHAR(20) DEFAULT NULL, date_limite DATE DEFAULT NULL, montant_verse DOUBLE PRECISION NOT NULL, reliquat DOUBLE PRECISION NOT NULL, statut VARCHAR(100) DEFAULT NULL, user_id INT DEFAULT NULL, membre_id INT DEFAULT NULL, INDEX IDX_174F4F5DA76ED395 (user_id), INDEX IDX_174F4F5D6A99F74A (membre_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE adhesion_fonds ADD CONSTRAINT FK_3C39BC82A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE adhesion_fonds ADD CONSTRAINT FK_3C39BC826A99F74A FOREIGN KEY (membre_id) REFERENCES membre (id)');
        $this->addSql('ALTER TABLE membre ADD CONSTRAINT FK_F6B4FB29FB88E14F FOREIGN KEY (utilisateur_id) REFERENCES user (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE membre ADD CONSTRAINT FK_F6B4FB29FEE4ED82 FOREIGN KEY (cree_part_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE plan_regularisation ADD CONSTRAINT FK_174F4F5DA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE plan_regularisation ADD CONSTRAINT FK_174F4F5D6A99F74A FOREIGN KEY (membre_id) REFERENCES membre (id)');
        $this->addSql('ALTER TABLE user ADD is_active TINYINT NOT NULL, CHANGE nom nom_complet VARCHAR(100) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE adhesion_fonds DROP FOREIGN KEY FK_3C39BC82A76ED395');
        $this->addSql('ALTER TABLE adhesion_fonds DROP FOREIGN KEY FK_3C39BC826A99F74A');
        $this->addSql('ALTER TABLE membre DROP FOREIGN KEY FK_F6B4FB29FB88E14F');
        $this->addSql('ALTER TABLE membre DROP FOREIGN KEY FK_F6B4FB29FEE4ED82');
        $this->addSql('ALTER TABLE plan_regularisation DROP FOREIGN KEY FK_174F4F5DA76ED395');
        $this->addSql('ALTER TABLE plan_regularisation DROP FOREIGN KEY FK_174F4F5D6A99F74A');
        $this->addSql('DROP TABLE adhesion_fonds');
        $this->addSql('DROP TABLE membre');
        $this->addSql('DROP TABLE plan_regularisation');
        $this->addSql('ALTER TABLE user DROP is_active, CHANGE nom_complet nom VARCHAR(100) NOT NULL');
    }
}
