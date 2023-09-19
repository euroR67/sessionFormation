-- --------------------------------------------------------
-- Hôte:                         127.0.0.1
-- Version du serveur:           8.0.30 - MySQL Community Server - GPL
-- SE du serveur:                Win64
-- HeidiSQL Version:             12.1.0.6537
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

-- Listage des données de la table sessionformation.categorie : ~4 rows (environ)
INSERT INTO `categorie` (`id`, `libelle`) VALUES
	(1, 'Développement web'),
	(2, 'Bureautique'),
	(3, 'Webdesign'),
	(4, 'Vente');

-- Listage des données de la table sessionformation.doctrine_migration_versions : ~1 rows (environ)
INSERT INTO `doctrine_migration_versions` (`version`, `executed_at`, `execution_time`) VALUES
	('DoctrineMigrations\\Version20230919080334', '2023-09-19 08:04:32', 206);

-- Listage des données de la table sessionformation.formateur : ~3 rows (environ)
INSERT INTO `formateur` (`id`, `nom`, `prenom`) VALUES
	(1, 'SMAIL', 'Stéphane'),
	(2, 'MURMANN', 'Mickael'),
	(3, 'MATHIEU', 'Quentin');

-- Listage des données de la table sessionformation.formation : ~3 rows (environ)
INSERT INTO `formation` (`id`, `nom_formation`) VALUES
	(1, 'Développement Web'),
	(2, 'Webdesign'),
	(3, 'Bureautique');

-- Listage des données de la table sessionformation.messenger_messages : ~0 rows (environ)

-- Listage des données de la table sessionformation.modules : ~11 rows (environ)
INSERT INTO `modules` (`id`, `nom_module`, `categories_id`) VALUES
	(1, 'HTML5', 1),
	(2, 'CSS3', 1),
	(3, 'PHP', 1),
	(4, 'SQL', 1),
	(5, 'Photoshop', 3),
	(6, 'E-commerce', 4),
	(7, 'Powerpoint', 2),
	(8, 'Word', 2),
	(9, 'Excel', 2),
	(10, 'Adobe XD', 3),
	(11, 'Figma', 3);

-- Listage des données de la table sessionformation.programme : ~6 rows (environ)
INSERT INTO `programme` (`id`, `session_id`, `module_id`, `duree_jour`) VALUES
	(1, 1, 11, 3),
	(2, 1, 8, 5),
	(3, 1, 9, 6),
	(4, 3, 1, 2),
	(5, 3, 2, 4),
	(6, 4, 11, 8);

-- Listage des données de la table sessionformation.session : ~5 rows (environ)
INSERT INTO `session` (`id`, `nom_session`, `nb_place`, `date_session`, `date_fin`, `formation_id`, `formateur_id`) VALUES
	(1, 'Plateau numérique', 25, '2023-06-19', '2024-02-15', 1, 2),
	(2, 'Strasbourg - DWWM2', 5, '2023-10-19', '2023-11-19', 1, 3),
	(3, 'Découverte des métiers du numérique', 15, '2024-01-05', '2024-03-18', 1, 1),
	(4, 'Colmar - Webdesign1', 14, '2023-11-19', '2024-04-06', 2, 1),
	(5, 'Strasbourg - DWWM1', 14, '2022-09-19', '2023-04-19', 1, 2);

-- Listage des données de la table sessionformation.stagiaire : ~5 rows (environ)
INSERT INTO `stagiaire` (`id`, `nom`, `prenom`, `date_naissance`, `sexe`, `ville`, `email`, `telephone`) VALUES
	(1, 'Akkari', 'Zizou', '2001-09-19', 'Homme', 'Strasbourg', 'zizou@gmail.com', '0605040908'),
	(2, 'Aliev', 'Baisangour', '1997-09-19', 'Homme', 'Strasbourg', 'baisangour@gmail.com', '0788554968'),
	(3, 'Falda', 'Cedric', '1992-09-19', 'Homme', 'Strasbourg', 'cedric@gmail.com', '0645889354'),
	(4, 'Chamaev', 'Mansour', '1997-06-20', 'Homme', 'Strasbourg', 'mansour@gmail.com', '0650313066'),
	(5, 'Kouzena', 'Ammar', '1994-09-19', 'Homme', 'Strasbourg', 'ammar@gmail.com', '0781569547');

-- Listage des données de la table sessionformation.stagiaire_session : ~6 rows (environ)
INSERT INTO `stagiaire_session` (`stagiaire_id`, `session_id`) VALUES
	(1, 2),
	(1, 4),
	(2, 4),
	(3, 4),
	(4, 2),
	(5, 3);

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
