SET FOREIGN_KEY_CHECKS=0;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- DataBase: `express_db` 
--
CREATE DATABASE IF NOT EXISTS `express_db` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `express_db`;


DROP TABLE IF EXISTS `ci_sessions`;
CREATE TABLE IF NOT EXISTS `ci_sessions` (
  `id` varchar(40) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `timestamp` int(10) unsigned NOT NULL DEFAULT '0',
  `data` blob NOT NULL,
  PRIMARY KEY (`id`,`ip_address`),
  KEY `ci_sessions_timestamp` (`timestamp`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

 

DROP TABLE IF EXISTS `Organisms`;
CREATE TABLE IF NOT EXISTS `Organisms` (
  `idOrganisms` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Organism` varchar(45) NOT NULL,
  `Max_transcript_size` int(10) unsigned NOT NULL,
  PRIMARY KEY (`idOrganisms`),
  UNIQUE KEY `Organism_UNIQUE` (`Organism`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;



INSERT INTO `Organisms` (`idOrganisms`, `Organism`, `Max_transcript_size`) VALUES
(1, 'Eucalyptus grandis', 15),
(2, 'Arabidopsis thaliana', 15),
(3, 'Rhizophagus irregularis', 15),
(4, 'Oriza sativa', 21);
 
-- -------------- ---------------------
DROP TABLE IF EXISTS `login_attempts`;
CREATE TABLE IF NOT EXISTS `login_attempts` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `ip_address` varchar(15) NOT NULL,
  `login` varchar(100) NOT NULL,
  `time` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8  ;

 
DROP TABLE IF EXISTS `tables`;
CREATE TABLE IF NOT EXISTS `tables` (
  `IdTables` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `TableName` varchar(255) NOT NULL,
  `MasterGroup` int(11) DEFAULT NULL,
  `Organism` int(11) DEFAULT NULL,
  `Submitter` varchar(15) NOT NULL,
  `version` char(5) NOT NULL,
  `comment` text NOT NULL,
  `original_file` varchar(50) NOT NULL,
  `Root` int(10) unsigned NOT NULL,
  `Child` int(10) unsigned NOT NULL,
  PRIMARY KEY (`IdTables`),
  UNIQUE KEY `TableName` (`TableName`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;



DROP TABLE IF EXISTS `tables_groups`;
CREATE TABLE IF NOT EXISTS `tables_groups` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `table_id` int(10) unsigned DEFAULT NULL,
  `group_id` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `table_id` (`table_id`,`group_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `users_tables`;
CREATE TABLE IF NOT EXISTS `users_tables` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `table_id` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uc_users_groups` (`user_id`,`table_id`),
  KEY `fk_users_groups_users1_idx` (`user_id`),
  KEY `fk_users_groups_groups1_idx` (`table_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------
--  tables below from #Ion Auth 2
-- by [Ben Edmunds](http://benedmunds.com)
-- please read README_io_auth.md
--

DROP TABLE IF EXISTS `groups`;
CREATE TABLE IF NOT EXISTS `groups` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  `description` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;


INSERT INTO `groups` (`id`, `name`, `description`) VALUES
     (1,'admin','Administrator'),
     (2,'members','General User'),
     (3,'Demo','Demo account');

     
DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `ip_address` varchar(15) NOT NULL,
  `username` varchar(100) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `salt` varchar(255) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `activation_code` varchar(40) DEFAULT NULL,
  `forgotten_password_code` varchar(40) DEFAULT NULL,
  `forgotten_password_time` int(11) unsigned DEFAULT NULL,
  `remember_code` varchar(40) DEFAULT NULL,
  `created_on` int(11) unsigned NOT NULL,
  `last_login` int(11) unsigned DEFAULT NULL,
  `active` tinyint(1) unsigned DEFAULT NULL,
  `first_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) DEFAULT NULL,
  `company` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ;


INSERT INTO `users` (`id`, `ip_address`, `username`, `password`, `salt`, `email`, `activation_code`, `forgotten_password_code`, `created_on`, `last_login`, `active`, `first_name`, `last_name`, `company`) VALUES
     ('1','127.0.0.1','administrator','$2a$07$SeBknntpZror9uyftVopmu61qg0ms8Qv1yV6FG.kQOSM.9QhmTo36','','admin@admin.com','',NULL,'1268889823','1268889823','1', 'Admin','istrator','ADMIN'),
     ('2','127.0.0.1','demo','$2y$08$FX8LnT8CWMaEyLmc3OZzueIXT/AhqgyKgUEfmclk8fOD0jyyUmMQO','Fp8NaXlRIvKSzykA3NGu7e','demo@admin.org','',NULL,'1268889823','1268889823','1','demo','demo','demo');

-- --------------------------------------------------------


DROP TABLE IF EXISTS `users_groups`;
CREATE TABLE IF NOT EXISTS `users_groups` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `group_id` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uc_users_groups` (`user_id`,`group_id`),
  KEY `fk_users_groups_users1_idx` (`user_id`),
  KEY `fk_users_groups_groups1_idx` (`group_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;


INSERT INTO `users_groups` (`id`, `user_id`, `group_id`) VALUES
     (1,1,1),
     (2,1,2),
     (3,2,3);

-- --------------------------------------------------------


DROP TABLE IF EXISTS `login_attempts`;
CREATE TABLE IF NOT EXISTS `login_attempts` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `ip_address` varchar(15) NOT NULL,
  `login` varchar(100) NOT NULL,
  `time` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
     

ALTER TABLE `users_groups`
  ADD CONSTRAINT `fk_users_groups_groups1` FOREIGN KEY (`group_id`) REFERENCES `groups` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_users_groups_users1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

  SET FOREIGN_KEY_CHECKS=1;
-- -------------------------------------------------------------------

--
-- Add demo data
--

DROP TABLE IF EXISTS `Myco_AnnotTest`;
CREATE TABLE IF NOT EXISTS `Myco_AnnotTest` (
  `Gene_ID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `Gene_Name` varchar(9) NOT NULL,
  `ERM` double NOT NULL,
  `OC` double NOT NULL,
  `LC` double NOT NULL,
  `7:30J` double NOT NULL,
  `8:30N` double NOT NULL,
  PRIMARY KEY (`Gene_ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;

INSERT INTO `Myco_AnnotTest` (`Gene_ID`, `Gene_Name`, `ERM`, `OC`, `LC`, `7:30J`, `8:30N`) VALUES
(1, '100575.1', 79.91, 240.27, 82.96, 24.1, 26.55),
(2, '1015031.1', 2.15, 50.56, 23.95, 16.08, 12.93),
(3, '1017100.1', 103.27, 6.09, 35.99, 12.98, 16.02),
(4, '1018913.1', 44.92, 7.49, 19.41, 14.98, 17.39),
(5, '1025276.1', 41.53, 54.63, 30.02, 41.16, 36.65),
(6, '1033221.1', 40.02, 22.62, 4.87, 11.04, 8.16),
(7, '1038247.1', 28.19, 24.58, 10.87, 17.22, 18.05),
(8, '1067243.1', 2.15, 811.48, 248.39, 156.36, 167.08),
(9, '1121023.1', 36.32, 24.98, 11.37, 11.26, 4.64),
(10, '1124071.1', 48.35, 36.06, 12.4, 11.74, 10.74),
(11, '1124568.1', 79.27, 108.59, 54.04, 57.49, 39.75),
(12, '1131927.1', 3.92, 15.37, 41.33, 69.65, 46.74),
(13, '1134077.1', 6.06, 21.27, 8.68, 2.94, 10.68),
(14, '1134682.1', 14.67, 8.85, 30.33, 34.09, 37.06),
(15, '1145986.1', 7.81, 15.31, 4.9, 1.58, 2.94),
(16, '1146025.1', 0.63, 18.8, 523.71, 678.14, 639.9),
(17, '1147622.1', 45.7, 6.45, 47.35, 45.83, 59.14),
(18, '1154641.1', 5.36, 8.97, 24.07, 29.2, 24.53),
(19, '1158547.1', 979.6, 287.04, 1552.56, 2068.66, 1465.3);


DROP TABLE IF EXISTS `Annotation_Myco_AnnotTest`;
CREATE TABLE IF NOT EXISTS `Annotation_Myco_AnnotTest` (
  `annot_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Gene_Name` varchar(15) NOT NULL,
  `Analyse` varchar(21) NOT NULL,
  `Signature` varchar(18) NOT NULL,
  `Description` varchar(255) NOT NULL,
  `misc` char(15) DEFAULT NULL,
  PRIMARY KEY (`annot_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;


INSERT INTO `Annotation_Myco_AnnotTest` (`annot_id`, `Gene_Name`, `Analyse`, `Signature`, `Description`) VALUES
(1, '1015031', 'GO', 'GO:0004672', 'protein kinase activity'),
(2, '1015031', 'GO', 'GO:0005524', 'ATP binding'),
(3, '1015031', 'GO', 'GO:0006468', 'protein amino acid phosphorylation'),
(4, '1015031', 'GO', 'GO:0004713', 'protein-tyrosine kinase activity'),
(5, '1015031', 'GO', 'GO:0008253', '5''-nucleotidase activity'),
(6, '1015031', 'GO', 'GO:0017175', 'IMP-GMP specific 5''-nucleotidase activity'),
(7, '1015031', 'IPRSCAN', 'IPR001245', 'Serine-threonine/tyrosine-protein kinase catalytic domain'),
(8, '1015031', 'IPRSCAN', 'IPR006597', 'Sel1-like'),
(9, '1015031', 'IPRSCAN', 'IPR000719', 'Protein kinase domain'),
(10, '1015031', 'IPRSCAN', 'IPR011009', 'Protein kinase-like domain'),
(11, '1015031', 'KEGG', '3.1.3.5', '5''-nucleotidase.'),
(12, '1017100', 'GO', 'GO:0050236', 'pyridoxine 4-dehydrogenase activity'),
(13, '1017100', 'KEGG', '1.1.1.65', 'Pyridoxine 4-dehydrogenase.'),
(14, '1025276', 'GO', 'GO:0003723', 'RNA binding'),
(15, '1025276', 'IPRSCAN', 'IPR003029', 'Ribosomal protein S1, RNA-binding domain'),
(16, '1025276', 'IPRSCAN', 'IPR022967', 'RNA-binding domain, S1'),
(17, '1025276', 'IPRSCAN', 'IPR012340', 'Nucleic acid-binding, OB-fold'),
(18, '1067243', 'GO', 'GO:0003676', 'nucleic acid binding'),
(19, '1067243', 'IPRSCAN', 'IPR003100', 'PAZ domain'),
(20, '1067243', 'IPRSCAN', 'IPR003165', 'Piwi domain'),
(21, '1067243', 'IPRSCAN', 'IPR014811', 'Domain of unknown function DUF1785'),
(22, '1067243', 'IPRSCAN', 'IPR012337', 'Ribonuclease H-like domain'),
(23, '1131927', 'GO', 'GO:0003676', 'nucleic acid binding'),
(24, '1131927', 'GO', 'GO:0005622', 'intracellular'),
(25, '1131927', 'GO', 'GO:0008270', 'zinc ion binding'),
(26, '1131927', 'IPRSCAN', 'IPR007087', 'Zinc finger, C2H2'),
(27, '1131927', 'IPRSCAN', 'IPR015880', 'Zinc finger, C2H2-like'),
(28, '1134077', 'GO', 'GO:0004672', 'protein kinase activity'),
(29, '1134077', 'GO', 'GO:0005524', 'ATP binding'),
(30, '1134077', 'GO', 'GO:0006468', 'protein amino acid phosphorylation'),
(31, '1134077', 'GO', 'GO:0004713', 'protein-tyrosine kinase activity'),
(32, '1134077', 'GO', 'GO:0004709', 'MAP kinase kinase kinase activity'),
(33, '1134077', 'IPRSCAN', 'IPR000719', 'Protein kinase domain'),
(34, '1134077', 'IPRSCAN', 'IPR001245', 'Serine-threonine/tyrosine-protein kinase catalytic domain'),
(35, '1134077', 'IPRSCAN', 'IPR011009', 'Protein kinase-like domain'),
(36, '1134682', 'GO', 'GO:0004672', 'protein kinase activity'),
(37, '1134682', 'GO', 'GO:0005524', 'ATP binding'),
(38, '1134682', 'GO', 'GO:0006468', 'protein amino acid phosphorylation'),
(39, '1134682', 'GO', 'GO:0004713', 'protein-tyrosine kinase activity'),
(40, '1134682', 'IPRSCAN', 'IPR001245', 'Serine-threonine/tyrosine-protein kinase catalytic domain'),
(41, '1134682', 'IPRSCAN', 'IPR000719', 'Protein kinase domain'),
(42, '1134682', 'IPRSCAN', 'IPR011009', 'Protein kinase-like domain'),
(43, '1134682', 'KOG', 'KOG3770', 'Acid sphingomyelinase and PHM5 phosphate metabolism protein'),
(44, '1145986', 'IPRSCAN', 'IPR014016', 'UvrD-like Helicase, ATP-binding domain'),
(45, '1145986', 'IPRSCAN', 'IPR014017', 'DNA helicase, UvrD-like, C-terminal'),
(46, '1145986', 'IPRSCAN', 'IPR027417', 'P-loop containing nucleoside triphosphate hydrolase'),
(47, '1154641', 'GO', 'GO:0005634', 'nucleus'),
(48, '1154641', 'IPRSCAN', 'IPR001214', 'SET domain');

-- ---------------------------------------------------------------------------------------
DROP TABLE IF EXISTS `Annotation_3`;
CREATE TABLE IF NOT EXISTS `Annotation_3` (
  `annot_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Gene_Name` varchar(15) NOT NULL,
  `Analyse` varchar(21) NOT NULL,
  `Signature` varchar(18) NOT NULL,
  `Description` varchar(255) NOT NULL,  
  `misc` char(15) DEFAULT NULL,
  PRIMARY KEY (`annot_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;


INSERT INTO `Annotation_3` (`annot_id`, `Gene_Name`, `Analyse`, `Signature`, `Description`, `misc`) VALUES
(1, '1015031', 'GO', 'GO:0004672', 'protein kinase activity', ''),
(2, '1015031', 'GO', 'GO:0005524', 'ATP binding', ''),
(3, '1015031', 'GO', 'GO:0006468', 'protein amino acid phosphorylation', ''),
(4, '1015031', 'GO', 'GO:0004713', 'protein-tyrosine kinase activity', ''),
(5, '1015031', 'GO', 'GO:0008253', '5''-nucleotidase activity', ''),
(6, '1015031', 'GO', 'GO:0017175', 'IMP-GMP specific 5''-nucleotidase activity', ''),
(7, '1015031', 'IPRSCAN', 'IPR001245', 'Serine-threonine/tyrosine-protein kinase catalytic domain', ''),
(8, '1015031', 'IPRSCAN', 'IPR006597', 'Sel1-like', ''),
(9, '1015031', 'IPRSCAN', 'IPR000719', 'Protein kinase domain', ''),
(10, '1015031', 'IPRSCAN', 'IPR011009', 'Protein kinase-like domain', ''),
(11, '1015031', 'KEGG', '3.1.3.5', '5''-nucleotidase.', ''),
(12, '1017100', 'GO', 'GO:0050236', 'pyridoxine 4-dehydrogenase activity', ''),
(13, '1017100', 'KEGG', '1.1.1.65', 'Pyridoxine 4-dehydrogenase.', ''),
(14, '1025276', 'GO', 'GO:0003723', 'RNA binding', ''),
(15, '1025276', 'IPRSCAN', 'IPR003029', 'Ribosomal protein S1, RNA-binding domain', ''),
(16, '1025276', 'IPRSCAN', 'IPR022967', 'RNA-binding domain, S1', ''),
(17, '1025276', 'IPRSCAN', 'IPR012340', 'Nucleic acid-binding, OB-fold', ''),
(18, '1067243', 'GO', 'GO:0003676', 'nucleic acid binding', ''),
(19, '1067243', 'IPRSCAN', 'IPR003100', 'PAZ domain', ''),
(20, '1067243', 'IPRSCAN', 'IPR003165', 'Piwi domain', ''),
(21, '1067243', 'IPRSCAN', 'IPR014811', 'Domain of unknown function DUF1785', ''),
(22, '1067243', 'IPRSCAN', 'IPR012337', 'Ribonuclease H-like domain', ''),
(23, '1131927', 'GO', 'GO:0003676', 'nucleic acid binding', ''),
(24, '1131927', 'GO', 'GO:0005622', 'intracellular', ''),
(25, '1131927', 'GO', 'GO:0008270', 'zinc ion binding', ''),
(26, '1131927', 'IPRSCAN', 'IPR007087', 'Zinc finger, C2H2', ''),
(27, '1131927', 'IPRSCAN', 'IPR015880', 'Zinc finger, C2H2-like', ''),
(28, '1134077', 'GO', 'GO:0004672', 'protein kinase activity', ''),
(29, '1134077', 'GO', 'GO:0005524', 'ATP binding', ''),
(30, '1134077', 'GO', 'GO:0006468', 'protein amino acid phosphorylation', ''),
(31, '1134077', 'GO', 'GO:0004713', 'protein-tyrosine kinase activity', ''),
(32, '1134077', 'GO', 'GO:0004709', 'MAP kinase kinase kinase activity', ''),
(33, '1134077', 'IPRSCAN', 'IPR000719', 'Protein kinase domain', ''),
(34, '1134077', 'IPRSCAN', 'IPR001245', 'Serine-threonine/tyrosine-protein kinase catalytic domain', ''),
(35, '1134077', 'IPRSCAN', 'IPR011009', 'Protein kinase-like domain', ''),
(36, '1134682', 'GO', 'GO:0004672', 'protein kinase activity', ''),
(37, '1134682', 'GO', 'GO:0005524', 'ATP binding', ''),
(38, '1134682', 'GO', 'GO:0006468', 'protein amino acid phosphorylation', ''),
(39, '1134682', 'GO', 'GO:0004713', 'protein-tyrosine kinase activity', ''),
(40, '1134682', 'IPRSCAN', 'IPR001245', 'Serine-threonine/tyrosine-protein kinase catalytic domain', ''),
(41, '1134682', 'IPRSCAN', 'IPR000719', 'Protein kinase domain', ''),
(42, '1134682', 'IPRSCAN', 'IPR011009', 'Protein kinase-like domain', ''),
(43, '1134682', 'KOG', 'KOG3770', 'Acid sphingomyelinase and PHM5 phosphate metabolism protein', ''),
(44, '1145986', 'IPRSCAN', 'IPR014016', 'UvrD-like Helicase, ATP-binding domain', ''),
(45, '1145986', 'IPRSCAN', 'IPR014017', 'DNA helicase, UvrD-like, C-terminal', ''),
(46, '1145986', 'IPRSCAN', 'IPR027417', 'P-loop containing nucleoside triphosphate hydrolase', ''),
(47, '1154641', 'GO', 'GO:0005634', 'nucleus', ''),
(48, '1154641', 'IPRSCAN', 'IPR001214', 'SET domain', '');



-- -----------------------------------------------------------------------

-- 
-- Update tables and tables_groups with Id of new tables Myco_AnnotTest and Annotation_Myco_AnnotTest
-- 

INSERT INTO `tables` (`IdTables`, `TableName`, `MasterGroup`, `Organism`, `Submitter`, `version`, `comment`, `original_file`, `Root`,`Child`) VALUES
(1, 'Myco_AnnotTest', 1, 3, 'administrator', '1', 'Test data with Rhysophagus expression set', 'annotTester.csv', 1,0),
(2, 'Annotation_3', 1, 3, 'administrator', '1', 'Annotation file for Rhizophagus irregularis', 'Rhizophagus irregularis_annot.txt', 1,0),
(3, 'Annotation_Myco_AnnotTest', 1, 3, 'administrator', '1', 'Annotation file for table Myco_AnnotTest', 'Myco_AnnotTest', 0,0);

INSERT INTO `tables_groups` (`id`, `table_id`, `group_id`) VALUES
(1, 1, 1),
(2, 1, 2),
(3, 1, 3);


-- -------------------------------------------------------------------

--
-- Add Ref  data  Enzymes, Kog, GO, PFAM
--
DROP TABLE IF EXISTS `Ref_Enzymes`;
CREATE TABLE IF NOT EXISTS `Ref_Enzymes` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(20) NOT NULL,
  `annotation` text NOT NULL,
  UNIQUE KEY `ID` (`ID`),
  KEY `nom` (`nom`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COMMENT='KEGG enzymes list Feb/2017' ;

DROP TABLE IF EXISTS `Ref_GO`;
CREATE TABLE IF NOT EXISTS `Ref_GO` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(10) NOT NULL DEFAULT '',
  `annotation` text NOT NULL,
  `type` char(1) NOT NULL DEFAULT '',
  PRIMARY KEY (`ID`),
  UNIQUE KEY `GOref_nom` (`nom`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COMMENT='GO Id and annotation ' ;

DROP TABLE IF EXISTS `Ref_KEGG`;
CREATE TABLE IF NOT EXISTS `Ref_KEGG` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(15) NOT NULL,
  `annotation` text NOT NULL,
  UNIQUE KEY `IdK` (`ID`),
  KEY `Kname` (`nom`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COMMENT='KEGG KO Id and annotation Feb/2017';

DROP TABLE IF EXISTS `Ref_KOG`;
CREATE TABLE IF NOT EXISTS `Ref_KOG` (
  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nom` char(7) NOT NULL,
  `annotation` varchar(255) NOT NULL,
  `type` varchar(4) NOT NULL,
  PRIMARY KEY (`ID`),
  KEY `ref` (`nom`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COMMENT='KOG annotation ' ;

DROP TABLE IF EXISTS `Ref_PANTHER`;
CREATE TABLE IF NOT EXISTS `Ref_PANTHER` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(20) NOT NULL,
  `annotation` text NOT NULL,
  UNIQUE KEY `ID` (`ID`),
  KEY `nom` (`nom`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COMMENT='PANTHER list'  ;

DROP TABLE IF EXISTS `Ref_PFAM`;
CREATE TABLE IF NOT EXISTS `Ref_PFAM` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(20) NOT NULL DEFAULT '',
  `annotation` text NOT NULL,
  PRIMARY KEY (`nom`),
  UNIQUE KEY `ID` (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COMMENT='PFAM Id and annotation PFAM30' ;

 
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
