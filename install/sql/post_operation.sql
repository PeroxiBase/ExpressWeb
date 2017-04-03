
/**
Update express_web.GOref with GO database
*/
INSERT express_db.GOref (nom,annot,type) SELECT acc,name,
CASE WHEN term_type ="biological_process" THEN 'P'
WHEN term_type ="molecular_function" THEN 'F'
WHEN term_type ="cellular_component" THEN 'C'
END AS term_type
FROM GOall.`term`
WHERE acc like 'GO:%'
ORDER by acc


