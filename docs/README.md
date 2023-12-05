# **Introduction**

Ce projet regroupe les informations obtenues à l'aide d'**Active Directory** et de **WMI** sur les ordinateurs du domaine. Il permet d'avoir énorméments d'informations sur les ordinateurs du domaine, d'en modifier certaines, de faire des exports customisés.

# **Table des matières**

- [**Introduction**](#introduction)
- [**Table des matières**](#table-des-matières)
- [**Documentation utilisateur**](#documentation-utilisateur)
	- [**Authentification**](#authentification)
	- [**Utilisation**](#utilisation)
		- [**Ordinateurs**](#ordinateurs)
			- [**Liste des ordinateurs**](#liste-des-ordinateurs)
			- [**Exporter depuis la liste**](#exporter-depuis-la-liste)
			- [**Fiche d'un ordinateur**](#fiche-dun-ordinateur)
			- [**Nouvel ordinateur**](#nouvel-ordinateur)
		- [**Réparations**](#réparations)
			- [**Liste des réparations**](#liste-des-réparations)
			- [**Exporter depuis la liste**](#exporter-depuis-la-liste-1)
			- [**Créer une réparation**](#créer-une-réparation)
			- [**Editer une réparation**](#editer-une-réparation)
			- [**Supprimer une réparation**](#supprimer-une-réparation)
		- [**Mises à jour**](#mises-à-jour)
			- [**Depuis l'AD**](#depuis-lad)
			- [**Depuis WMI**](#depuis-wmi)
		- [**Paramètres**](#paramètres)
			- [**Gérer les types**](#gérer-les-types)
			- [**Gérer les statuts**](#gérer-les-statuts)
			- [**Gérer les marques**](#gérer-les-marques)
- [**Documentation technique**](#documentation-technique)
	- [**Structure du projet**](#structure-du-projet)
	- [**Style**](#style)
		- [**Utilisation**](#utilisation-1)
		- [**Classes pré-définies**](#classes-pré-définies)
			- [**Formulaires**](#formulaires)
	- [**Javascript**](#javascript)
		- [**Utilisation**](#utilisation-2)
	- [**Modules**](#modules)
		- [**Utilisation**](#utilisation-3)
	- [**Outils**](#outils)
	- [**AJAX**](#ajax)
	- [**Exports**](#exports)
	- [**Classes**](#classes)

# **Documentation utilisateur**

## **Authentification**

L'autentification se fait à l'aide du même système que celui de la gestion de stock.

## **Utilisation**

### **Ordinateurs**

Cette application permet de gérer les ordinateurs du domaine ainsi que ceux hors domaine.

#### **Liste des ordinateurs**

Dans l'onglet `Ordinateurs` se trouve la liste des ordinateurs, cette liste est filtrable par différents critères.

- La recherche,
- Les différents selecteurs,
- Les différents filtres.

En haut à droite vous avez accès aux actions suivantes :

- Exporter la liste,
- Réinitialiser les filtres.

L'affichage de la liste est dynamique, dès lors que vous modifiez un filtre, la liste se met à jour automatiquement.

Dans la liste vous pouvez faire les actions suivantes :

- Voir la fiche d'un ordinateur en cliquant sur sa ligne,
- Enregistrer une nouvelle réparation pour cet ordinateur en cliquant sur l'icon tout à droite de la ligne.

#### **Exporter depuis la liste**

En cliquant sur le bouton `Exporter la liste` cela télécharge côté client un fichier csv contenant les informations de la liste au moment du clic.

C'est à dire que si vous modifiez un filtre, puis que vous cliquez sur le bouton `Exporter la liste`, le fichier csv contiendra les informations de la liste filtrée.

#### **Fiche d'un ordinateur**

En cliquant sur une ligne de la liste des ordinateurs, vous accédez à la fiche de l'ordinateur.

Depuis cette fiche vous avec accès à toutes les informations de l'ordinateur, vous pouvez également modifier certaines informations.

#### **Nouvel ordinateur**

En cliquant sur le bouton `Nouvel ordinateur` vous accédez à un formulaire vous permettant d'ajouter un nouvel ordinateur.

### **Réparations**

Cette application permet de gérer les réparations des ordinateurs.

#### **Liste des réparations**

Dans l'onglet `Réparations` se trouve la liste des réparations, par défaut, uniquement les réparations en cours sont affichées.

Cette liste est filtrable par différents critères.

- La recherche,
- Les différents selecteurs,
- Les différents filtres.

En haut à droite vous avez accès aux actions suivantes :

- Exporter la liste,
- Réinitialiser les filtres.

L'affichage de la liste est dynamique, dès lors que vous modifiez un filtre, la liste se met à jour automatiquement.

Dans la liste vous pouvez faire les actions suivantes :

- Editer une réparation en cliquant sur l'icon "crayon" tout à droite de la ligne,
- Supprimer une réparation en cliquant sur l'icon "poubelle" tout à droite de la ligne.

La suppression d'une réparation est définitive, une confirmation vous sera demandée avant la suppression.

#### **Exporter depuis la liste**

Comme pour la liste des ordinateurs, en cliquant sur le bouton `Exporter la liste` cela télécharge côté client un fichier csv contenant les informations de la liste au moment du clic.

C'est à dire que si vous modifiez un filtre, puis que vous cliquez sur le bouton `Exporter la liste`, le fichier csv contiendra les informations de la liste filtrée.

#### **Créer une réparation**

Vous pouvez créer une réparation depuis deux endroits :

- Depuis la liste des ordinateurs en cliquant sur l'icon "plus" tout à droite de la ligne de l'ordinateur,
- Depuis la fiche d'un ordinateur en cliquant sur le bouton `Nouvelle réparation` dans l'onglet `Réparations`.

Dans les deux cas, vous accédez au même formulaire.

#### **Editer une réparation**

Vous pouvez éditer une réparation depuis deux endroits :

- Depuis la fiche d'un ordinateur en cliquant sur le bouton `Editer` dans l'onglet `Réparations` en cliquant sur l'icon "crayon" tout à droite de la ligne de la réparation,
- Depuis la liste des réparations en cliquant sur l'icon "crayon" tout à droite de la ligne de la réparation.

Dans les deux cas, vous accédez au même formulaire.

#### **Supprimer une réparation**

Vous pouvez supprimer une réparation depuis deux endroits :

- Depuis la fiche d'un ordinateur en cliquant sur le bouton `Supprimer` dans l'onglet `Réparations` en cliquant sur l'icon "poubelle" tout à droite de la ligne de la réparation,
- Depuis la liste des réparations en cliquant sur l'icon "poubelle" tout à droite de la ligne de la réparation.

Dans les deux cas, une confirmation vous sera demandée avant la suppression.

### **Mises à jour**

L'applications permet de mettre à jour les informations des ordinateurs de la base de données et d'en ajouter de nouveaux à l'aide d'AD et de WMI.

#### **Depuis l'AD**

En cliquant sur le bouton `Depuis l'AD` cela déclenche une mise à jour des informations des ordinateurs de la base de données à l'aide de l'AD.

Dans le cas où les ordinateurs ne sont pas encore dans la base de données, ils seront ajoutés.

#### **Depuis WMI**

En cliquant sur le bouton `Depuis WMI` cela déclenche une mise à jour des informations des ordinateurs de la base de données à l'aide de WMI.

Dans le cas où les ordinateurs ne sont pas dans le domaine, les requêtes WMI échoueront et les informations ne seront pas mises à jour.

### **Paramètres**

Certains paramètres de l'application sont modifiables depuis l'onglet `Paramètres`.

#### **Gérer les types**

Vous pouvez gérer les types d'ordinateurs depuis l'onglet `Paramètres` en cliquant sur le bouton `Gérer les types`.

#### **Gérer les statuts**

Vous pouvez gérer les statuts des ordinateurs depuis l'onglet `Paramètres` en cliquant sur le bouton `Gérer les statuts`.

#### **Gérer les marques**

Vous pouvez gérer les marques des ordinateurs depuis l'onglet `Paramètres` en cliquant sur le bouton `Gérer les marques`.

# **Documentation technique**

## **Structure du projet**

- Dossier `classes` : contient les classes php du projet
- Dossier `css` : contient les fichiers css du projet
- Dossier `js` : contient les fichiers javascript du projet
- Dossier `mod` : contient les fichiers de modules utilisés sur le site
- Dossier `tools` : contient les fichiers d'outils utilisés sur le site
- Dossier `prog` : contient les fichiers de lancement du site
- Dossier `docs` : contient les fichiers de documentation du projet

## **Style**

Le dossier `css` contient les fichiers css du projet.

- Le fichier `variables.css` contient les variables css utilisées sur le site et est appelé sur toutes les pages du site en premier.
- Le fichier `main.css` est appelé sur toutes les pages du site.

### **Utilisation**

En créant une variable dans le fichier variables.css, celle-ci sera disponible sur toutes les pages du site.

```css
variables.css

:root {
	--my-variable: #000;
}
```

```css
main.css

body {
		background-color: var(--my-variable);
}
```

### **Classes pré-définies**

#### **Formulaires**

- `.form` : formulaire
- `.form-group` : groupe de champs de formulaire
- `.form-group-column` : groupe de champs de formulaire en colonne
- `.form-section` : section de plusieurs groupes
- `.form-element` : conteneur d'un champ de formulaire (label + input)
- `.form-element-extended` : conteneur d'un champ de formulaire étendu (flex: 1)
- `.form-label` : label d'un champ de formulaire
- `.form-input` : input d'un champ de formulaire
- `.form-select` : select d'un champ de formulaire
- `.form-select-extended` : select d'un champ de formulaire étendu (width: 100%)
- `.form-date` : input date d'un champ de formulaire
- `.form-button` : bouton d'un champ de formulaire
- `.form-spacing` : permet d'ajouter un espacement entre les éléments d'un formulaire

## **Javascript**

*L'application utilise jQuery et Jquery UI.*

Le dossier `js` contient les fichiers javascript du projet.

- Le fichier `functions.js` contient les fonctions javascript utilisées sur le site : il est appelé sur toutes les pages du site en premier.
- Le fichier `main.js` est appelé sur toutes les pages du site.

Le dossir `js/pages` contient les fichiers javascript spécifiques à chaque page du site.

Le dossier `js/lib` contient les fichiers javascript de librairies externes.

### **Utilisation**

En créant une fonction dans le fichier function.js, celle-ci sera disponible sur toutes les pages du site.

```javascript 
functions.js

function myFunction() {
		// code
}
```

```javascript
main.js

$(document).ready(function() {
		myFunction();
});
```

## **Modules**

Le dossier `mod` contient les fichiers de modules utilisés sur le site.

Chaque page possède un paramètre en get `mod` qui permet de charger un module spécifique sur la page.

### **Utilisation**

Pour utiliser un module, il faut ajouter le paramètre `mod` dans l'url de la page.
Si un module existe avec le nom passé en paramètre, il sera chargé sur la page.


## **Outils**

## **AJAX**

Les requêtes AJAX pointent vers les fichiers php du dossier situés dans `./tools/ajax/`.

## **Exports**

Les fichiers réalisant les exports sous format CSV ou autres se situent dans `./tools/exports/`.

## **Classes**

- La classe `Ldap.class.php` permet d'effectuer des requêtes LDAP.
- La classe `Wmi.class.php` permet d'effectuer des requêtes WMI à partir d'un nom d'hôte.
- La classe `Fonctions.class.php` contient les  fonctions utiles pour le projet.
- La classe `DB.class.php` contient les fonctions liées à la base de données.
- La classe `Display.class.php` contient les fonctions liées à l'affichage.
- La classe `Filter.class.php` contient les fonctions liées aux filtres.
- La classe `View.class.php` contient les fonctions utilisées à la création des vues (header, css, js, etc.).
- La classe `Select.class.php` contient les fonctions permettant de générer des selects.
- La classe `P_matos.class.php` permet de traiter les données de la table `matos` de la base de données sous forme d'objet.



