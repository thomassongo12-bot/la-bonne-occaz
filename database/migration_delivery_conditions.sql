-- ============================================================
-- Migration : ajout des conditions de livraison dans site_settings
-- À exécuter dans phpMyAdmin sur Hostinger
-- ============================================================

SET NAMES utf8mb4;

INSERT IGNORE INTO `site_settings` (`setting_key`, `setting_val`, `setting_type`, `label`, `group_name`)
VALUES (
    'delivery_conditions',
    'Conditions et modalités de livraison des véhicules\n\nAfin de vous garantir une expérience d\'achat confortable et simplifiée, notre établissement propose un service de livraison directement à l\'adresse de votre choix. L\'acheminement des véhicules est confié à un prestataire logistique professionnel partenaire.\n\nDélai de livraison\nLa livraison du véhicule s\'effectue généralement dans un délai de 24 à 48 heures suivant la réception du règlement des frais de livraison et la validation du dossier.\n\nFrais de livraison\nLes frais de livraison varient entre 150 € et 300 €, en fonction de la distance séparant notre établissement de l\'adresse communiquée par le client. Le montant exact est précisé avant toute validation.\n\nModalités de paiement\nLe règlement des frais de livraison s\'effectue exclusivement par virement bancaire.\nDès réception du paiement, le véhicule est confié à notre prestataire logistique professionnel. Un numéro de suivi est communiqué au client afin de lui permettre de suivre l\'acheminement de son véhicule jusqu\'à sa destination.\n\nRéception du véhicule\nLors de la livraison, le client est invité à vérifier l\'état général du véhicule, sa conformité aux caractéristiques annoncées ainsi que l\'ensemble des documents administratifs qui l\'accompagnent, avant de confirmer sa réception.\n\nNotre engagement : vous garantir une livraison professionnelle, sécurisée et transparente, tout en vous accompagnant jusqu\'à la réception de votre véhicule.',
    'textarea',
    'Conditions de livraison',
    'reservation'
);
