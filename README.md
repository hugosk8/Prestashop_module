# Module PrestaShop – Exercice Dashboard

## Objectif  
Créer un module PrestaShop affichant le prix du Bitcoin depuis l’API CoinGecko dans le dashboard du back-office.

## Fonctionnalités  
- Formulaire de configuration avec :
  - Clé API
  - Fréquence de mise à jour (manuelle / 24h)
  - Activation / désactivation du module
- Récupération du prix BTC via API
- Sauvegarde en base de données
- Affichage prévu dans le dashboard PrestaShop 

## Installation  
1. Lancer le docker via ```docker compose up -d```
2. Installer le module via le back-office  
3. Le configurer dans la section Modules > exercisedashboard > Configurer

## 🔗 API utilisée  
[CoinGecko – Bitcoin Price](https://www.coingecko.com/fr/api)

## Ce qui fonctionne  
- Installation / désinstallation propre
- Formulaire de configuration
- Connexion API + insertion en base

## Bug 
- L'affichage dans le dashboard ne fonctionne pas encore
