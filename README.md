# Sae301 — mon thème enfant Storefront

Je réalise ce thème enfant pour ma SAÉ 3.01, itération 1. J'utilise la version 1.0.0 et je m'appuie sur Storefront comme parent.

## Comment j'installe mon thème

Je vérifie la présence de Storefront. J'installe mon ZIP dans **Apparence > Thèmes > Ajouter un thème > Téléverser un thème**, puis j'active **Sae301**.

En local, je retrouve mes fichiers dans `/home/benjamin/projets/Sae301/web/wp-content/themes/sae301`.

## Comment j'organise mes fichiers

- Dans `style.css`, je déclare le nom, la version et le parent avec `Template: storefront`. Je pourrai y ajouter mes styles.
- Dans `functions.php`, je prépare mes fonctions et j'initialise les traductions du thème enfant. Je complète les fonctions du parent sans les recopier.
- Je laisse Storefront charger automatiquement ses styles et mon `style.css`, sans chargement en double.
- Dans `.gitignore`, je définis mes exclusions usuelles. Je vérifie néanmoins mes fichiers avant chaque commit.

Je conserve l'apparence de Storefront tant que je n'ajoute pas de personnalisation. Je n'ai pas ajouté de `screenshot.png` : je peux voir une vignette vide dans l'administration sans que cela gêne le site.

## Comment je vérifie mon thème

Depuis `/home/benjamin/projets/Sae301`, je lance :

```bash
ddev wp theme list
ddev wp option get stylesheet
ddev wp option get template
ddev exec php -l web/wp-content/themes/sae301/functions.php
```

Je vérifie les valeurs `sae301` et `storefront` et l'absence d'erreur PHP. Je contrôle aussi le site, la boutique et l'administration dans le navigateur.

## Comment je gère Git

J'ai initialisé mon dépôt uniquement dans le dossier du thème enfant. Je n'y inclus ni WordPress, ni base SQL, ni médias, ni configurations privées.

Pour mes prochains commits, je décris les changements réalisés, par exemple : `Je documente mon installation WordPress`.

Je garde mes mots de passe, exports et clés privées hors de Git. Je vérifie la visibilité de mon dépôt GitHub avant de partager son adresse.

## Comment je déploie mon thème

J'ai activé Sae301 en local et sur mon site hébergé par mmi.unilim.fr. Je vérifie la présence de Storefront dans chaque environnement. Je conserve ma procédure complète de migration dans un document local distinct.

Je ne place aucune information privée dans ce README : si je le publie dans GitHub ou un dossier web, je vérifie son accessibilité.
