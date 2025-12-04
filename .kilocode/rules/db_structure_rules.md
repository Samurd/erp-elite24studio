# db_structure_rules.md

Esta son para contruir nueva tablas y campos en base de datos

## Guidelines

- cuando un nuevo campo es por tipos o tipo enums

por ejemplo: Estado: Activado o desactivdo, en proceso, etc.

Ahi se debera usar la tabla y modelo Tags, y en la tabla y migracion quedaria "status_id" con relacion a la tabla "tags"


igual para otros diferentes tipos, se utiliza la relacion con la tabla tags, cuando se trata como de enums o casos muy conctetos o incluso varios o muchos casos.











