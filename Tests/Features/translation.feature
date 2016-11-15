# language: fr

Fonctionnalité: Traduction de messages d'abort de l'api
  Afin de pouvoir fonctionner
  L'api doit executer un abort

@GET
Scénario: On cherche à traduire un message après un app->abort et que l'utilisateur qui fais la requête est français
    Quand       je fais un GET sur /translate_fr
    Alors       le status HTTP devrait être 400
    Et          je devrais avoir un résultat d'API en JSON
    Et          le résultat devrait être identique au JSON suivant :
    """
        "Cette opération est impossible sur un contrat annulé."
    """

@GET
Scénario: On cherche à traduire un message après un app->abort et que l'utilisateur qui fais la requête est anglais
    Quand       je fais un GET sur /translate_en
    Alors       le status HTTP devrait être 400
    Et          je devrais avoir un résultat d'API en JSON
    Et          le résultat devrait être identique au JSON suivant :
    """
        "Bad request : operation forbidden on a deleted contract"
    """

@GET
Scénario: On cherche à traduire un message après un app->abort avec des tokens et que l'utilisateur qui fais la requête est français
    Quand       je fais un GET sur /translate_with_tokens_fr
    Alors       le status HTTP devrait être 404
    Et          je devrais avoir un résultat d'API en JSON
    Et          le résultat devrait être identique au JSON suivant :
    """
        "Responsable non trouvé : 42"
    """

@GET
Scénario: On cherche à traduire un message après un app->abort avec des tokens et que l'utilisateur qui fais la requête est anglais
    Quand       je fais un GET sur /translate_with_tokens_en
    Alors       le status HTTP devrait être 404
    Et          je devrais avoir un résultat d'API en JSON
    Et          le résultat devrait être identique au JSON suivant :
    """
        "Manager not found : 42"
    """
