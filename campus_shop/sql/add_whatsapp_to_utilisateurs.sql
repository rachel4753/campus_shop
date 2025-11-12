-- Ajoute le champ whatsapp à la table utilisateurs
ALTER TABLE utilisateurs ADD COLUMN whatsapp VARCHAR(30) DEFAULT NULL AFTER email;

-- Pour supprimer le champ si besoin :
-- ALTER TABLE utilisateurs DROP COLUMN whatsapp;
