
-- ---
-- Globals
-- ---

-- SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
-- SET FOREIGN_KEY_CHECKS=0;

-- ---
-- Table 'banque'
--
-- ---

DROP TABLE IF EXISTS `banque`;

CREATE TABLE `banque` (
  `id` INTEGER NOT NULL,
  `CIB` CHAR(5) DEFAULT '00000',
  `nom` VARCHAR(20) DEFAULT NULL,
  `notes` MEDIUMTEXT NOT NULL,
  PRIMARY KEY (`id`)
);

-- ---
-- Table 'cat'
--
-- ---

DROP TABLE IF EXISTS `cat`;

CREATE TABLE `cat` (
  `id` INTEGER NOT NULL,
  `nom` VARCHAR(20) NOT NULL,
  `idtypecat` ENUM('R','D','V') DEFAULT 'D',
  `notes` MEDIUMTEXT DEFAULT NULL,
  PRIMARY KEY (`id`)
);

-- ---
-- Table 'compte'
--
-- ---

DROP TABLE IF EXISTS `compte`;

CREATE TABLE `compte` (
  `id` INTEGER NOT NULL,
  `nom` VARCHAR(20) NOT NULL,
  `titulaire` VARCHAR(20) NOT NULL,
  `type` ENUM('bancaire','espece','passif','actif') DEFAULT 'bancaire',
  `iddevise` INTEGER NOT NULL,
  `idbanque` INTEGER NOT NULL,
  `guichet` INTEGER DEFAULT NULL,
  `num_compte` VARCHAR(20) DEFAULT NULL,
  `cle_compte` INTEGER DEFAULT NULL,
  `solde_init` FLOAT DEFAULT 0,
  `solde_mini_voulu` FLOAT DEFAULT NULL,
  `solde_mini_autorise` FLOAT DEFAULT NULL,
  `date_dernier_releve` TIMESTAMP DEFAULT 0,
  `solde_dernier_releve` FLOAT DEFAULT NULL,
  `dernier_rapprochement` INTEGER DEFAULT NULL,
  `compte_cloture` BOOLEAN DEFAULT TRUE,
  `affichage_r` INTEGER DEFAULT NULL,
  `nb_lignes_ope` INTEGER DEFAULT NULL,
  `notes` MEDIUMTEXT DEFAULT NULL,
  `adresse_du_titulaire` VARCHAR(40) DEFAULT NULL,
  `type_defaut_debit` INTEGER DEFAULT NULL,
  `type_defaut_credit` INTEGER DEFAULT NULL,
  `tri_par_type` BOOLEAN DEFAULT FALSE,
  `neutres_inclus` BOOLEAN DEFAULT TRUE,
  `ordre_du_tri` VARCHAR(40) DEFAULT NULL,
  PRIMARY KEY (`id`)
);

-- ---
-- Table 'devise'
--
-- ---

DROP TABLE IF EXISTS `devise`;

CREATE TABLE `devise` (
  `id` INTEGER NOT NULL,
  `nom` VARCHAR(20) NOT NULL,
  `tx_de_change` FLOAT DEFAULT 0,
  `isocode` VARCHAR(10) DEFAULT NULL,
  `Devise_principale` INTEGER NOT NULL,
  `date_dernier_maj` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
);

-- ---
-- Table 'ech'
--
-- ---

DROP TABLE IF EXISTS `ech`;

CREATE TABLE `ech` (
  `id` INTEGER  NOT NULL,
  `date_ech` TIMESTAMP,
  `compte` INTEGER NOT NULL,
  `montant` FLOAT NOT NULL,
  `devise` INTEGER NOT NULL,
  `tiers` INTEGER NOT NULL,
  `cat` INTEGER NOT NULL,
  `scat` INTEGER DEFAULT NULL,
  `compte_virement` INTEGER DEFAULT NULL,
  `moyen` INTEGER NOT NULL,
  `moyen_virement` INTEGER DEFAULT NULL,
  `exercice` INTEGER DEFAULT NULL,
  `ib` INTEGER DEFAULT NULL,
  `sib` INTEGER DEFAULT NULL,
  `notes` MEDIUMTEXT DEFAULT NULL,
  `imputation_automatique` INTEGER DEFAULT NULL,
  `periodicite` ENUM('u','h','m','a','p') NOT NULL DEFAULT 'u',
  `intervalle` INTEGER DEFAULT NULL,
  `periode_perso` ENUM('j','m','a'),
  `date_limite` TIMESTAMP DEFAULT 0,
  PRIMARY KEY (`id`)
);

-- ---
-- Table 'generalite'
--
-- ---

DROP TABLE IF EXISTS `generalite`;

CREATE TABLE `generalite` (
  `id` INTEGER AUTO_INCREMENT DEFAULT NULL,
  `Titre` VARCHAR(40) DEFAULT NULL,
  `Adresse_commune` VARCHAR(40) NOT NULL,
  `Adresse_secondaire` VARCHAR(40) NOT NULL,
  `Utilise_exercices` BOOLEAN DEFAULT FALSE,
  `Utilise_IB` BOOLEAN DEFAULT FALSE,
  `Utilise_PC` BOOLEAN DEFAULT FALSE,
  `Utilise_info_BG` BOOLEAN DEFAULT FALSE,
  `Numero_devise_totaux_tiers` INTEGER DEFAULT NULL,
  `Type_affichage_des_echeances` INTEGER DEFAULT NULL,
  `Affichage_echeances_perso_nb_libre` INTEGER DEFAULT NULL,
  `Type_affichage_perso_echeances` INTEGER DEFAULT NULL,
  `Chemin_logo` VARCHAR(255) DEFAULT NULL,
  `Affichage_opes` VARCHAR(255) DEFAULT NULL,
  `Rapport_largeur_col` VARCHAR(40) DEFAULT NULL,
  `Ligne_aff_une_ligne` VARCHAR(40) DEFAULT NULL,
  `Lignes_aff_deux_lignes` VARCHAR(40) DEFAULT NULL,
  `Lignes_aff_trois_lignes` VARCHAR(40) DEFAULT NULL,
  PRIMARY KEY (`id`)
);

-- ---
-- Table 'ib'
--
-- ---

DROP TABLE IF EXISTS `ib`;

CREATE TABLE `ib` (
  `id` INTEGER NOT NULL,
  `nom` VARCHAR(20) NOT NULL,
  `idtypeimp` ENUM('R','D','V') DEFAULT 'D',
  `notes` MEDIUMTEXT DEFAULT NULL,
  PRIMARY KEY (`id`)
);

-- ---
-- Table 'moyens'
--
-- ---

DROP TABLE IF EXISTS `moyens`;

CREATE TABLE `moyens` (
  `id` INTEGER NOT NULL,
  `id_compte` INTEGER NOT NULL,
  `nom` VARCHAR(40) NOT NULL,
  `signe` ENUM('R','D','V') NOT NULL,
  `affiche_entree` BOOLEAN DEFAULT TRUE,
  `num_auto` BOOLEAN DEFAULT TRUE,
  `num_en_cours` INTEGER DEFAULT NULL,
  PRIMARY KEY (`id`, `id_compte`)
);

-- ---
-- Table 'ope'
--
-- ---

DROP TABLE IF EXISTS `ope`;

CREATE TABLE `ope` (
  `id` INTEGER NOT NULL,
  `idcompte` INTEGER NOT NULL,
  `date_ope` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `date_val` TIMESTAMP DEFAULT 0,
  `montant` FLOAT DEFAULT 0,
  `iddevise` INTEGER  NOT NULL,
  `idtiers` INTEGER  NOT NULL,
  `idcat` INTEGER  NOT NULL,
  `idscat` INTEGER DEFAULT NULL,
  `is_mere` BOOLEAN DEFAULT FALSE,
  `note` MEDIUMTEXT DEFAULT NULL,
  `idmoyen` INTEGER DEFAULT NULL,
  `numcheque` VARCHAR(20) DEFAULT NULL,
  `pointe` ENUM('p','r','na') DEFAULT 'p',
  `idrappro` INTEGER DEFAULT NULL,
  `idexercice` INTEGER DEFAULT NULL,
  `idib` INTEGER DEFAULT NULL,
  `idsib` INTEGER DEFAULT NULL,
  `piececompt` VARCHAR(40),
  `idjumelle` INTEGER DEFAULT NULL,
  `idcomptevirement` INTEGER DEFAULT NULL,
  `idmere` INTEGER DEFAULT NULL,
  PRIMARY KEY (`id`)
);

-- ---
-- Table 'scat'
--
-- ---

DROP TABLE IF EXISTS `scat`;

CREATE TABLE `scat` (
  `nom` VARCHAR(40) NOT NULL,
  `id_grisbi` INTEGER DEFAULT NULL,
  `id` INTEGER NOT NULL,
  `idcat` INTEGER DEFAULT 0,
  PRIMARY KEY (`id_grisbi`, `idcat`),
  unique (`id`)
);

-- ---
-- Table 'sib'
--
-- ---

DROP TABLE IF EXISTS `sib`;

CREATE TABLE `sib` (
  `nom` VARCHAR(40) NOT NULL,
  `id_grisbi` INTEGER DEFAULT NULL,
  `id` INTEGER NOT NULL,
  `idsib` INTEGER DEFAULT 0,
  PRIMARY KEY (`id_grisbi`, `idsib`),
  unique (`id`)
);
-- ---
-- Table 'tiers'
--
-- ---

DROP TABLE IF EXISTS `tiers`;

CREATE TABLE `tiers` (
  `id` INTEGER NOT NULL,
  `nom` VARCHAR(40) NOT NULL,
  `notes` MEDIUMTEXT DEFAULT NULL,
  PRIMARY KEY (`id`)
);

-- ---
-- Table 'rapp'
--
-- ---

DROP TABLE IF EXISTS `rapp`;

CREATE TABLE `rapp` (
  `id` INTEGER NOT NULL,
  `nom` VARCHAR(20) NOT NULL,
  PRIMARY KEY (`id`)
);

-- ---
-- Table 'exercice'
--
-- ---

DROP TABLE IF EXISTS `exercice`;

CREATE TABLE `exercice` (
  `id` INTEGER NOT NULL,
  `date_debut` TIMESTAMP,
  `date_fin` TIMESTAMP,
  `nom` VARCHAR(20) NOT NULL,
  `affiche` BOOLEAN DEFAULT TRUE,
  PRIMARY KEY (`id`)
);

-- ---
-- Table 'titre'
--
-- ---

DROP TABLE IF EXISTS `titre`;

CREATE TABLE `titre` (
  `id`INTEGER AUTO_INCREMENT,
  `nom` VARCHAR(40) NOT NULL,
  `isin` VARCHAR(20) DEFAULT NULL,
  `id_tiers` INTEGER NOT NULL,
  `id_devise` INTEGER DEFAULT 0,
  PRIMARY KEY (`id`),
  unique(nom,id_devise)
);

-- ---
-- Table 'cours'
--
-- ---

DROP TABLE IF EXISTS `cours`;

CREATE TABLE `cours` (
  `id` INTEGER AUTO_INCREMENT,
  `valeur` FLOAT NOT NULL,
  `id_titre` INTEGER NOT NULL,
  `date_cours` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  unique(`id_titre`, `date_cours`)
);

-- ---
-- Foreign Keys
-- ---

ALTER TABLE `compte` ADD FOREIGN KEY (iddevise) REFERENCES `devise` (`id`);
ALTER TABLE `compte` ADD FOREIGN KEY (idbanque) REFERENCES `banque` (`id`);
ALTER TABLE `compte` ADD FOREIGN KEY (dernier_rapprochement) REFERENCES `rapp` (`id`);
ALTER TABLE `compte` ADD FOREIGN KEY (type_defaut_debit) REFERENCES `moyens` (`id`);
ALTER TABLE `compte` ADD FOREIGN KEY (type_defaut_credit) REFERENCES `moyens` (`id`);
ALTER TABLE `devise` ADD FOREIGN KEY (Devise_principale) REFERENCES `devise` (`id`);
ALTER TABLE `ech` ADD FOREIGN KEY (compte) REFERENCES `compte` (`id`);
ALTER TABLE `ech` ADD FOREIGN KEY (devise) REFERENCES `devise` (`id`);
ALTER TABLE `ech` ADD FOREIGN KEY (tiers) REFERENCES `tiers` (`id`);
ALTER TABLE `ech` ADD FOREIGN KEY (cat) REFERENCES `cat` (`id`);
ALTER TABLE `ech` ADD FOREIGN KEY (scat) REFERENCES `scat` (`id`);
ALTER TABLE `ech` ADD FOREIGN KEY (compte_virement) REFERENCES `compte` (`id`);
ALTER TABLE `ech` ADD FOREIGN KEY (moyen) REFERENCES `moyens` (`id`);
ALTER TABLE `ech` ADD FOREIGN KEY (moyen_virement) REFERENCES `moyens` (`id`);
ALTER TABLE `ech` ADD FOREIGN KEY (exercice) REFERENCES `exercice` (`id`);
ALTER TABLE `ech` ADD FOREIGN KEY (ib) REFERENCES `ib` (`id`);
ALTER TABLE `ech` ADD FOREIGN KEY (sib) REFERENCES `sib` (`id`);
ALTER TABLE `ib` ADD FOREIGN KEY (id) REFERENCES `sib` (`idib`);
ALTER TABLE `moyens` ADD FOREIGN KEY (id_compte) REFERENCES `compte` (`id`);
ALTER TABLE `ope` ADD FOREIGN KEY (idcompte) REFERENCES `compte` (`id`);
ALTER TABLE `ope` ADD FOREIGN KEY (iddevise) REFERENCES `devise` (`id`);
ALTER TABLE `ope` ADD FOREIGN KEY (idtiers) REFERENCES `tiers` (`id`);
ALTER TABLE `ope` ADD FOREIGN KEY (idcat) REFERENCES `cat` (`id`);
ALTER TABLE `ope` ADD FOREIGN KEY (idscat) REFERENCES `scat` (`id`);
ALTER TABLE `ope` ADD FOREIGN KEY (idmoyen) REFERENCES `moyens` (`id`);
ALTER TABLE `ope` ADD FOREIGN KEY (idrappro) REFERENCES `rapp` (`id`);
ALTER TABLE `ope` ADD FOREIGN KEY (idexercice) REFERENCES `exercice` (`id`);
ALTER TABLE `ope` ADD FOREIGN KEY (idib) REFERENCES `ib` (`id`);
ALTER TABLE `ope` ADD FOREIGN KEY (idsib) REFERENCES `sib` (`id`);
ALTER TABLE `ope` ADD FOREIGN KEY (idjumelle) REFERENCES `ope` (`id`);
ALTER TABLE `ope` ADD FOREIGN KEY (idmere) REFERENCES `ope` (`id`);
ALTER TABLE `scat` ADD FOREIGN KEY (idcat) REFERENCES `cat` (`id`);
ALTER TABLE `titre` ADD FOREIGN KEY (id_tiers) REFERENCES `tiers` (`id`);
ALTER TABLE `titre` ADD FOREIGN KEY (id_devise) REFERENCES `devise` (`id`);
ALTER TABLE `cours` ADD FOREIGN KEY (id_titre) REFERENCES `titre` (`id`);

-- ---
-- Table Properties
-- ---

ALTER TABLE `banque` ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
ALTER TABLE `cat` ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
ALTER TABLE `compte` ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
ALTER TABLE `devise` ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
ALTER TABLE `ech` ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
ALTER TABLE `generalite` ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
ALTER TABLE `ib` ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
ALTER TABLE `moyens` ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
ALTER TABLE `ope` ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
ALTER TABLE `scat` ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
ALTER TABLE `sib` ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
ALTER TABLE `tiers` ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
ALTER TABLE `rapp` ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
ALTER TABLE `exercice` ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
ALTER TABLE `titre` ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
ALTER TABLE `cours` ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;


