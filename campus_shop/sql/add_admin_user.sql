-- Ajoute un utilisateur admin par défaut
INSERT INTO utilisateurs (nom, email, whatsapp, role, password)
VALUES ('Administrateur', 'admin@campusshop.com', '22501020304', 'admin', MD5('admin123'));
