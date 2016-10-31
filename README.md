# composer-translation-service-provider
Translation service provider

Ce provider va nous permettre de renvoyer les messages `abort` dans la langue de l'utilisateur à partir de nos API vers nos clients.

Il faut ajouter un (ou plusieurs) fichier de traduction et les ajouter au EtnaConfig.php de la façon suivante :

```
    $app['translator'] = $app->extend('translator', function ($translator) {
        $translator->addResource('xliff', __DIR__.'/Utils/Silex/Translator/validators.fr.xlf', 'fr', 'validators');
        $translator->addResource('xliff', __DIR__.'/Utils/Silex/Translator/abort.fr.xlf', 'fr', 'abort');

        return $translator;
    });
```

Ceux ci seront remplis de la façon suivante :

```
<?xml version="1.0"?>
<xliff version="1.2" xmlns="urn:oasis:names:tc:xliff:document:1.2">
    <file source-language="en" datatype="plaintext" original="file.ext">
        <body>
            <trans-unit id="1">
                <source>Hello %name%</source>
                <target>Bonjour %name%</target>
            </trans-unit>
        </body>
    </file>
</xliff>
```

`%name%` étant ici un token (optionnel) que l'on peut ajouter à la traduction, il faudra le placer dans le header du `abort` comme ceci :
/!\ Le nom du token ne doit pas contenir de caractères spéciaux, on préfèrera ici le camelCase.

```
    return $app->abort(400, "Bad request : operation forbidden on a deleted %entityName%",[
        'Translation' => json_encode(['%entityName%' => $entity_name])
    ]);
```
