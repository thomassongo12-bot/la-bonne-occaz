-- ============================================================
-- Migration : mise à jour des conditions de réservation et livraison
-- Format mini-markdown : ## Titre, ### Sous-titre, **gras**, *italique*
-- À exécuter dans phpMyAdmin sur Hostinger
-- ============================================================

SET NAMES utf8mb4;

UPDATE `site_settings` SET `setting_val` =
'## Conditions Générales de Réservation

*La Bonne Occaz s\'engage à vous offrir un service transparent et professionnel à chaque étape de votre réservation.*

### 1. Objet de la réservation
La présente procédure de réservation vous permet de **bloquer un véhicule** de notre parc dans l\'attente de la finalisation de votre achat. Elle ne constitue pas un contrat de vente définitif.

### 2. Confirmation et acompte
Pour valider votre réservation, un **acompte de réservation** vous sera communiqué par notre équipe lors de la prise de contact. Cet acompte est déduit du prix de vente final du véhicule.

*En cas de désistement de votre part après confirmation, cet acompte ne pourra être remboursé.*

### 3. Durée de maintien du véhicule
Le véhicule est **maintenu à votre disposition** pendant une durée limitée à compter de la confirmation de votre réservation. Passé ce délai, sans accord préalable de notre part, le véhicule pourra être remis en vente.

### 4. Obligations du client
Vous vous engagez à :
- Vous présenter avec une **pièce d\'identité valide** lors du rendez-vous
- Confirmer votre réservation dans les **24 heures** suivant votre demande
- Respecter le rendez-vous convenu ou nous prévenir dans les meilleurs délais

### 5. Obligations de La Bonne Occaz
Nous nous engageons à :
- Maintenir le véhicule **réservé et non visible** à la vente pendant la durée convenue
- Vous transmettre l\'ensemble des **documents relatifs au véhicule** avant la signature
- Assurer un **accompagnement personnalisé** jusqu\'à la remise des clés

### 6. Annulation
Toute demande d\'annulation doit être formulée par écrit (email ou message) dans les délais convenus. La Bonne Occaz se réserve le droit d\'annuler une réservation en cas de force majeure ou d\'indisponibilité du véhicule, auquel cas l\'acompte vous sera intégralement restitué.

*Pour toute question, notre équipe reste disponible par téléphone ou par email.*'
WHERE `setting_key` = 'reservation_conditions';

UPDATE `site_settings` SET `setting_val` =
'## Conditions de Livraison des Véhicules

*Afin de vous garantir une expérience d\'achat confortable et simplifiée, La Bonne Occaz propose un service de livraison directement à l\'adresse de votre choix, confié à un prestataire logistique professionnel partenaire.*

### 1. Délai de livraison
La livraison du véhicule s\'effectue généralement dans un délai de **24 à 48 heures** suivant :
- La réception du règlement des frais de livraison
- La validation complète de votre dossier d\'achat

### 2. Frais de livraison
Les frais de livraison varient en fonction de la distance séparant notre établissement de l\'adresse de livraison communiquée. Le **montant exact vous est précisé avant toute validation**, sans surprise.

*Ces frais sont à régler séparément du prix du véhicule.*

### 3. Modalités de paiement des frais
Le règlement des frais de livraison s\'effectue **exclusivement par virement bancaire**. Dès réception du paiement, le véhicule est confié à notre prestataire logistique.

Un **numéro de suivi** vous est communiqué afin de suivre l\'acheminement de votre véhicule en temps réel jusqu\'à sa destination.

### 4. Réception du véhicule
Lors de la livraison, vous êtes invité à vérifier :
- L\'**état général du véhicule** (carrosserie, intérieur, pneumatiques)
- La **conformité** aux caractéristiques annoncées sur la fiche véhicule
- L\'**ensemble des documents administratifs** (carte grise, contrôle technique, certificat de cession)

*Toute réserve doit être signalée immédiatement au livreur et notifiée par écrit à notre équipe dans les 24 heures suivant la livraison.*

### 5. Notre engagement
La Bonne Occaz s\'engage à vous garantir une livraison **professionnelle, sécurisée et transparente**, avec un accompagnement continu jusqu\'à la réception de votre véhicule.

*Notre équipe reste joignable à tout moment durant le processus de livraison.*'
WHERE `setting_key` = 'delivery_conditions';
