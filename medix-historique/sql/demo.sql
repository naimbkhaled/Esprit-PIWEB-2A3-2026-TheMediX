-- Données de démonstration pour le PREMIER utilisateur créé (via /dev/connexion).
USE session;
SET @u = (SELECT MIN(id) FROM users);

INSERT INTO articles (titre, categorie, date_pub, source, statut) VALUES
('Alimentation équilibrée : les bases essentielles','Nutrition','2024-05-12','Santé Publique France','pub'),
('Bouger au quotidien : par où commencer ?','Activité','2024-06-03','OMS','pub');
SET @a1 = (SELECT MIN(id) FROM articles);
INSERT INTO lectures (utilisateur_id, article_id, lu_le) VALUES (@u, @a1, NOW() - INTERVAL 1 DAY), (@u, @a1 + 1, NOW() - INTERVAL 3 HOUR);
INSERT INTO favoris (utilisateur_id, article_id) VALUES (@u, @a1);

INSERT INTO mesures (utilisateur_id, valeur, unite, ts, source) VALUES
(@u,118,'mg/dL',NOW() - INTERVAL 10 MINUTE,'relais mobile'),(@u,186,'mg/dL',NOW() - INTERVAL 2 HOUR,'relais mobile'),
(@u,72,'mg/dL',NOW() - INTERVAL 7 HOUR,'API fabricant'),(@u,6.1,'mmol/L',NOW() - INTERVAL 1 DAY,'saisie manuelle'),(@u,64,'mg/dL',NOW() - INTERVAL 5 DAY,'saisie manuelle');
INSERT INTO a1c (utilisateur_id, date_mesure, valeur_pct) VALUES (@u,'2026-08-10',7.2),(@u,'2026-11-05',6.9);
INSERT INTO prises_medicament (utilisateur_id, ts, medicament, statut) VALUES (@u,NOW() - INTERVAL 8 HOUR,'Metformine 850 mg','confirmee'),(@u,NOW() - INTERVAL 32 HOUR,'Metformine 850 mg','manquee');
INSERT INTO repas (utilisateur_id, ts, type, aliments, glucides_g) VALUES (@u,NOW() - INTERVAL 5 HOUR,'dejeuner','Riz, poulet, salade',60),(@u,NOW() - INTERVAL 20 HOUR,'diner','Soupe de légumes, pain complet',45);
INSERT INTO insuline (utilisateur_id, ts, dose_ui, type, repas_id) VALUES (@u,NOW() - INTERVAL 5 HOUR,6,'rapide',(SELECT MIN(id) FROM repas WHERE utilisateur_id=@u)),(@u,NOW() - INTERVAL 14 HOUR,18,'lente',NULL);
INSERT INTO activites (utilisateur_id, ts, type, duree_min) VALUES (@u,NOW() - INTERVAL 1 DAY,'marche',40),(@u,NOW() - INTERVAL 3 DAY,'velo',30);
INSERT INTO rendez_vous (utilisateur_id, debut, medecin_nom, motif, lieu, statut) VALUES (@u,NOW() + INTERVAL 6 DAY,'Dr Martin','Contrôle diabète','Cabinet, Tunis','a_venir'),(@u,NOW() - INTERVAL 40 DAY,'Dr Ben Salah','Bilan annuel','Clinique','honore');
INSERT INTO teleconsultations (utilisateur_id, debut, medecin_nom, duree_min, statut) VALUES (@u,NOW() - INTERVAL 12 DAY,'Dr Martin',18,'terminee');
INSERT INTO messages (utilisateur_id, ts, medecin_nom, auteur, contenu) VALUES
(@u,NOW() - INTERVAL 12 DAY,'Dr Martin','patient','Bonjour, ma glycémie est souvent haute après le déjeuner.'),
(@u,NOW() - INTERVAL 12 DAY + INTERVAL 1 HOUR,'Dr Martin','medecin','Merci, notez vos repas pendant une semaine et nous en reparlons.');
INSERT INTO alertes_glycemie (utilisateur_id, ts, type, valeur, seuil, statut) VALUES (@u,NOW() - INTERVAL 5 DAY,'hypo',64,70,'lue'),(@u,NOW() - INTERVAL 2 HOUR,'hyper',186,180,'envoyee');
INSERT INTO sos_evenements (utilisateur_id, ts, declencheur, etape, contact_prevenu) VALUES
(@u,NOW() - INTERVAL 20 DAY,'non_confirmation','Rappel de prise sans confirmation',NULL),(@u,NOW() - INTERVAL 20 DAY + INTERVAL 15 MINUTE,'non_confirmation','Appel automatique au proche','Sami (frère)');
INSERT INTO contacts_urgence (utilisateur_id, nom, telephone, relation, consentement, actif) VALUES (@u,'Sami','+216 20 123 456','famille',1,1);
INSERT INTO publications (utilisateur_id, titre, categorie, contenu) VALUES (@u,'Mes astuces pour le petit-déjeuner','Nutrition','Je privilégie les flocons d''avoine et les fruits entiers.');
INSERT INTO commentaires (utilisateur_id, publication_id, contenu) VALUES (@u,(SELECT MIN(id) FROM publications),'Merci pour le partage !');
INSERT INTO chatbot_messages (utilisateur_id, ts, role, contenu) VALUES (@u,NOW() - INTERVAL 2 DAY,'user','Que manger avant une séance de sport ?'),(@u,NOW() - INTERVAL 2 DAY,'assistant','Information générale : une collation légère riche en glucides. Demandez conseil à votre médecin.');
INSERT INTO groupes_membres (utilisateur_id, groupe_id) VALUES (@u,(SELECT MIN(id) FROM groupes));
INSERT INTO partages (utilisateur_id, categorie, autorise) VALUES (@u,'glycemie',1),(@u,'a1c',1);
