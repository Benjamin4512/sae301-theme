# Sae301 — theme enfant Storefront

Theme prepare pour la SAE 3.01, iteration 1. Version : 1.0.0.

## Installation locale

Storefront doit etre installe. Installer le ZIP dans Apparence > Themes > Ajouter un theme > Televerser un theme, puis activer Sae301.

Le dossier attendu est `/home/benjamin/projets/Sae301/web/wp-content/themes/sae301`.

## Comprendre les fichiers

- `style.css` declare le nom, la version et le parent (`Template: storefront`). Ajouter les personnalisations CSS ici.
- `functions.php` ajoute les fonctions du projet et initialise les traductions du theme enfant. Il est charge en complement du fichier du parent.
- Storefront charge automatiquement sa feuille CSS et celle du theme enfant. Il est inutile de les charger une deuxieme fois.
- `.gitignore` exclut les fichiers temporaires, dependances, secrets et sauvegardes usuels. Il ne remplace pas une verification des fichiers avant chaque commit.

L'apparence reste celle de Storefront tant qu'aucune personnalisation n'est ajoutee.

## Verification dans le terminal Linux

Depuis `/home/benjamin/projets/Sae301` :

```bash
ddev wp theme list
ddev wp option get stylesheet
ddev wp option get template
ddev exec php -l web/wp-content/themes/sae301/functions.php
```

Les deux options doivent afficher respectivement `sae301` et `storefront`. Le controle PHP doit reussir. Verifier egalement la page publique, la boutique et le tableau de bord dans le navigateur.

## Git

Initialiser le depot uniquement dans le dossier du theme enfant. Ne pas inclure WordPress, la base, les fichiers televerses ou les configurations locales.

Aucun depot distant ni deploiement en production n'a ete effectue. Une autorisation explicite est necessaire avant publication sur GitHub ou intervention sur mmi.unilim.fr.
