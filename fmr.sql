-- 1️⃣ Main categories
INSERT INTO `main_category` (`name`, `slug`) VALUES
('Vêtements', 'vetements'),
('Chaussures', 'chaussures'),
('Accessoires', 'accessoires'),
('Tout', 'tout');

-- 2️⃣ Gender categories
INSERT INTO `gender_category` (`name`, `slug`) VALUES
('Femme', 'femme'),
('Homme', 'homme'),
('Non genré', 'neutre'),
('Enfant', 'enfant');

-- 3️⃣ Brands
INSERT INTO `brand` (`name`) VALUES
('Nike'),('Adidas'),('Puma'),('Reebok'),('Under Armour'),
('New Balance'),('Converse'),('Vans'),('Fila'),('Asics'),
('Jordan'),('Levi''s'),('Tommy Hilfiger'),('Calvin Klein'),('Ralph Lauren'),
('Hugo Boss'),('Diesel'),('Gucci'),('Prada'),('Louis Vuitton');

-- 4️⃣ Sizes
INSERT INTO `size` (`name`) VALUES
('Taille unique'),('XS / 32/34'),('S / 36 /38'),('M / 40/42'),('L / 44/46'),
('XL / 48/50'),('2XL'),('3XL'),('4XL'),('5XL'),('6XL'),('7XL'),('8XL'),
('24'),('25'),('26'),('27'),('28'),('29'),('30'),('31'),('32'),('33'),('34'),
('35'),('36'),('37'),('38'),('39'),('40'),('41'),('42'),('43'),('44'),('45'),('46'),('47'),('48'),('49'),('50');

-- 5️⃣ Colors
INSERT INTO `color` (`name`) VALUES
('Rouge'),('Bleu'),('Vert'),('Jaune'),('Orange'),('Violet'),('Rose'),('Gris'),('Noir'),('Blanc'),
('Marron'),('Beige'),('Turquoise'),('Indigo'),('Aubergine'),('Lavande'),('Cyan'),('Pêche'),
('Fuchsia'),('Bordeaux'),('Sable'),('Olive'),('Menthe'),('Or'),('Argent'),('Cuivre'),('Prune'),
('Charcoal'),('Pistache'),('Corail');

-- 6️⃣ Categories
-- ⚠️ On suppose que les main_category_id sont corrects et correspondent à l'ordre d'insertion ci-dessus
INSERT INTO `category` (`main_category_id`, `name`, `slug`, `description`) VALUES
(1, 'T-shirt', 't-shirt', ''),
(1, 'Jeans', 'jeans', ''),
(1, 'Jupe', 'jupe', ''),
(1, 'Pantalon', 'pantalon', ''),
(1, 'Chemise', 'chemise', ''),
(1, 'Veste', 'veste', ''),
(1, 'Pull', 'pull', ''),
(1, 'Manteau', 'manteau', ''),
(1, 'Robe', 'robe', ''),
(1, 'Short', 'short', ''),
(1, 'Blouson', 'blouson', ''),
(1, 'Tunique', 'tunique', ''),
(1, 'Soutien-gorge', 'soutien-gorge', ''),
(3, 'Chapeau', 'chapeau', ''),
(3, 'Cravate', 'cravate', ''),
(1, 'Gilet', 'gilet', ''),
(1, 'Parka', 'parka', ''),
(1, 'Blazer', 'blazer', ''),
(1, 'Cardigan', 'cardigan', ''),
(1, 'Legging', 'legging', ''),
(2, 'Baskets', 'baskets', ''),
(2, 'Mocassins', 'mocassins', ''),
(2, 'Sandales', 'sandales', ''),
(2, 'Escarpins', 'escarpins', ''),
(2, 'Bottines', 'bottines', ''),
(2, 'Boots', 'boots', ''),
(2, 'Chaussures de sport', 'chaussures-de-sport', ''),
(2, 'Tongs', 'tongs', ''),
(2, 'Derbies', 'derbies', ''),
(2, 'Chaussettes', 'chaussettes', ''),
(3, 'Sac à main', 'sac-a-main', ''),
(3, 'Ceinture', 'ceinture', ''),
(3, 'Lunettes de soleil', 'lunettes-de-soleil', ''),
(3, 'Montre', 'montre', ''),
(3, 'Écharpe', 'echarpe', ''),
(3, 'Portefeuille', 'portefeuille', ''),
(3, 'Gants', 'gants', ''),
(3, 'Chapeau de paille', 'chapeau-de-paille', ''),
(3, 'Foulard', 'foulard', ''),
(3, 'Sac à dos', 'sac-a-dos', ''),
(3, 'Masque', 'masque', ''),
(2, 'Bottes', 'bottes', '');

-- 7️⃣ Products
-- ⚠️ Ici tu dois t'assurer que category_id, brand_id, color_id, size_id, gender_category_id existent
INSERT INTO `product` (`user_id`, `category_id`, `brand_id`, `color_id`, `size_id`, `gender_category_id`, `name`, `description`, `price`, `created_at`, `images`, `stock`, `wear`, `main_image`) VALUES
(1, 127, NULL, 9, 1, 3, 'Batcat', 'Un masque pour votre chat préféré', 21, NOW(), '[]', 1, 'Neuf', '67939469b5b40.jpg'),
(1, 69, NULL, 10, 4, 1, 'Robe de présidente', 'Parée pour les élections ?', 124.5, NOW(), '["679394b53382b.jpg","679394b61d85d.jpg","679394b6939cc.jpg"]', 1, 'Neuf avec étiquette', '679394b52f44e.jpg'),
(1, 61, NULL, 10, 4, 2, 'T-shirt blanc', 'Passe-partout, toutes saisons.', 4, NOW(), '["6793959d8c371.jpg","6793959d91b7c.jpg"]', 1, 'Neuf', '6793959cd54a8.jpg');
